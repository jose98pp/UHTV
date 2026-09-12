const CACHE_NAME = 'uhtv-cache-v1';
const OFFLINE_URL = '/offline.html';

const PRECACHE_ASSETS = [
    '/',
    '/offline.html',
    '/manifest.json',
    '/images/icons/icon.svg',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
    '/images/icons/apple-touch-icon.png',
    '/images/default-news.svg'
];

// 1. Install: Precache shell and offline fallback
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(PRECACHE_ASSETS).catch((err) => {
                console.warn('[SW] Warning precaching assets:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate: Clean up old caches and claim clients
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.map((key) => {
                    if (key !== CACHE_NAME) {
                        return caches.delete(key);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch: Network-first for navigation, Stale-while-revalidate for assets
self.addEventListener('fetch', (event) => {
    const { request } = event;

    // Solo procesar peticiones GET
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // No interceptar rutas de administración ni autenticación
    if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/login') || url.pathname.startsWith('/logout')) {
        return;
    }

    // A. Peticiones de Navegación HTML (páginas web)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }
                    return networkResponse;
                })
                .catch(async () => {
                    // Si falla la red, intentar devolver la página en caché o el fallback offline
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    const offlinePage = await caches.match(OFFLINE_URL);
                    return offlinePage || new Response('Sin conexión a Internet', {
                        status: 503,
                        statusText: 'Service Unavailable',
                        headers: { 'Content-Type': 'text/plain; charset=utf-8' }
                    });
                })
        );
        return;
    }

    // B. Recursos estáticos (imágenes, estilos, scripts, fuentes)
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        const copy = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
                    }
                    return networkResponse;
                }).catch(() => cachedResponse);

                return cachedResponse || fetchPromise;
            })
        );
        return;
    }

    // C. Peticiones regulares: red con fallback a caché
    event.respondWith(
        fetch(request).catch(() => caches.match(request))
    );
});
