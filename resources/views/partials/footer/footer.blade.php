    <!-- Banner Publicitario Footer (Pre-Footer) -->
    @if(isset($banners['footer']) && $banners['footer']->count() > 0)
        <div class="publicidad-footer w-full flex flex-col justify-center items-center my-8 px-4">
            <div class="text-center mb-2">
                <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-wider">Publicidad</span>
            </div>
            @foreach($banners['footer'] as $banner)
                <div class="w-full flex justify-center mb-4">
                    <a href="{{ $banner->link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="block w-full max-w-5xl transition-transform hover:scale-[1.01] duration-300 group"> 
                        <img src="{{ asset($banner->image_path) }}" 
                             alt="{{ $banner->title }}" 
                             class="w-full h-auto rounded-2xl shadow-lg object-cover border border-gray-200 dark:border-gray-700" 
                             loading="lazy"
                             decoding="async">
                    </a>
                </div>
            @endforeach
        </div>
    @endif

   <!-- Footer Moderno - Inspirado en la imagen -->
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-12 border-t border-gray-700">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Logo y Descripción -->
            <div class="lg:col-span-1">
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ asset('images/Logo.jpg') }}" alt="ÚltimaHoraTV" class="w-12 h-12 rounded-full" loading="lazy" decoding="async">
                    <div>
                        <h3 class="text-xl font-bold text-white">Última<span class="text-purple-400">Hora</span> TV</h3>
                        <p class="text-gray-400 text-sm">Tu fuente confiable de noticias y análisis. © {{ date('Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Secciones -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Secciones</h4>
                <ul class="space-y-2">
                    @forelse($categorias ?? [] as $categoria)
                        <li>
                            <a href="{{ $categoria->url }}" 
                               class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                                {{ $categoria->name }}
                            </a>
                        </li>
                    @empty
                        <li class="text-gray-500 text-sm">Política</li>
                        <li class="text-gray-500 text-sm">Deportes</li>
                        <li class="text-gray-500 text-sm">Negocios</li>
                        <li class="text-gray-500 text-sm">Tecnología</li>
                    @endforelse
                </ul>
            </div>

            <!-- Legal -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Legal</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                            Aviso de Privacidad
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                            Términos y Condiciones
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                            Contacto
                        </a>
                    </li>
                    <li>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                            Acerca de Nosotros
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Síguenos -->
            <div>
                <h4 class="text-lg font-semibold text-white mb-4">Síguenos</h4>
                <div class="flex space-x-3 mb-4">
                    <a href="https://facebook.com/uhtvbolivia" target="_blank" rel="noopener noreferrer" 
                       class="bg-gray-700 hover:bg-blue-600 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-facebook text-lg"></i>
                    </a>
                    <a href="https://x.com/UhtvBol" target="_blank" rel="noopener noreferrer" 
                       class="bg-gray-700 hover:bg-gray-900 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-x-twitter text-lg"></i>
                    </a>
                    <a href="https://instagram.com/uhtvbolivia" target="_blank" rel="noopener noreferrer" 
                       class="bg-gray-700 hover:bg-pink-600 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-instagram text-lg"></i>
                    </a>
                    <a href="https://tiktok.com/@uhtvbolivia" target="_blank" rel="noopener noreferrer" 
                       class="bg-gray-700 hover:bg-black text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110">
                        <i class="fab fa-tiktok text-lg"></i>
                    </a>
                </div>
                
                <!-- Toggle de modo oscuro en footer -->
                <div class="mt-4">
                    <button data-dark-mode-toggle 
                            class="flex items-center space-x-2 text-gray-400 hover:text-purple-400 transition-colors duration-300 text-sm">
                        <i class="fas fa-sun sun-icon hidden"></i>
                        <i class="fas fa-moon moon-icon"></i>
                        <span class="sun-text hidden">Modo Claro</span>
                        <span class="moon-text">Modo Oscuro</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Línea divisoria -->
        <div class="border-t border-gray-700 mt-8 pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm mb-4 md:mb-0">
                    Desarrollado con <i class="fas fa-heart text-red-500 mx-1"></i> por el equipo de Última Hora TV.
                </p>
                <div class="flex items-center space-x-4 text-gray-400 text-sm">
                    <span>Bolivia</span>
                    <span>•</span>
                    <span>{{ date('Y') }}</span>
                </div>
            </div>
        </div>
    </div>
</footer>
    <!-- Dark Mode Script (debe cargarse antes que otros scripts) -->
    <script src="{{ asset('js/dark-mode.js') }}"></script>

    @if(config('app.debug'))
    <!-- Scripts de diagnóstico (solo en entorno de desarrollo) -->
    <script src="{{ asset('js/diagnostics.js') }}"></script>
    @endif
    
    <!-- Error Handler -->
    <script src="{{ asset('js/error-handler.js') }}"></script>
    
    <!-- Bootstrap JS - Solo una versión -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

@if(isset($banners['popup']) && $banners['popup']->count() > 0)
  @php
    $popupBanner = $banners['popup']->first();
  @endphp
  <!-- Modal Publicitario Emergente Global -->
  <div id="promoModal" style="position: fixed; inset: 0; z-index: 99999; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(4px); transition: opacity 0.3s ease; opacity: 0; pointer-events: none;">
    <div id="promoModalContent" class="relative max-w-lg w-full mx-4" style="transform: scale(0.9); opacity: 0; transition: transform 0.3s ease, opacity 0.3s ease;">
      
      <!-- Contenedor con marco grueso gris oscuro e imagen -->
      <div class="relative bg-black rounded overflow-hidden shadow-2xl" style="border: 8px solid #2d3748; outline: 2px solid #1a202c;">
        
        <!-- Botón de cerrar rojo 'X' en la esquina superior derecha interna -->
        <button onclick="closePromoModal()" class="absolute top-3 right-4 z-10 text-red-500 hover:text-red-400 transition-colors duration-200 focus:outline-none" aria-label="Cerrar" style="background: none; border: none; cursor: pointer;">
          <i class="fas fa-times text-2xl filter drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]"></i>
        </button>

        <a href="{{ $popupBanner->link ?? '#' }}" {{ $popupBanner->link ? 'target="_blank" rel="noopener noreferrer"' : '' }} class="block overflow-hidden group">
          <img src="{{ asset($popupBanner->image_path) }}" alt="{{ $popupBanner->title }}" 
               class="w-full h-auto object-cover max-h-[70vh] transition-transform duration-500 group-hover:scale-[1.01]"
               loading="lazy"
               decoding="async">
        </a>
      </div>
      
      <!-- Indicación de teclado sutil debajo del modal -->
      <div class="text-center mt-4 animate-pulse">
        <span class="text-xs bg-black/60 text-gray-300 px-3 py-1.5 rounded-full border border-white/10 uppercase tracking-widest font-mono shadow-md">Presiona [Z] para cerrar</span>
      </div>
      
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('promoModal');
        const modalContent = document.getElementById('promoModalContent');
        
        if (modal && modalContent) {
            // Mostrar modal tras 1.5 segundos
            setTimeout(() => {
                modal.style.opacity = '1';
                modal.style.pointerEvents = 'auto';
                modalContent.style.transform = 'scale(1)';
                modalContent.style.opacity = '1';
                
                // Prevenir scroll en la página mientras el modal está abierto
                document.body.style.overflow = 'hidden';
            }, 1500);
        }

        // Escuchador de teclado para la tecla 'z' o 'Z'
        document.addEventListener('keydown', function(e) {
            if (e.key === 'z' || e.key === 'Z' || e.keyCode === 90) {
                closePromoModal();
            }
        });
    });

    function closePromoModal() {
        const modal = document.getElementById('promoModal');
        const modalContent = document.getElementById('promoModalContent');
        
        if (modal && modalContent) {
            modalContent.style.transform = 'scale(0.9)';
            modalContent.style.opacity = '0';
            modal.style.opacity = '0';
            modal.style.pointerEvents = 'none';
            
            // Restaurar scroll de la página
            document.body.style.overflow = '';
        }
    }
  </script>
@endif

  <!-- Modal de Streaming En Vivo y Podcasts -->
  @include('partials.live-modal')

  @if(config('app.enable_pwa_install', false))
  <!-- Banner Flotante de Instalación PWA (App Móvil) -->
  <div id="pwa-install-banner" class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:w-96 bg-gray-900/95 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl border border-purple-500/30 z-50 transform translate-y-32 opacity-0 transition-all duration-500 pointer-events-none flex items-center justify-between gap-3">
      <div class="flex items-center gap-3">
          <img src="{{ asset('images/icons/icon-192x192.png') }}" alt="UHTV App" class="w-12 h-12 rounded-xl shadow-md border border-white/20 flex-shrink-0" loading="lazy" decoding="async">
          <div>
              <h6 class="font-bold text-sm text-white leading-tight">Instalar App UHTV</h6>
              <p class="text-xs text-purple-200 mt-0.5">Accede al instante y lee noticias sin conexión.</p>
          </div>
      </div>
      <div class="flex items-center gap-2">
          <button id="pwa-install-btn" class="bg-gradient-to-r from-purple-600 to-red-600 hover:from-purple-700 hover:to-red-700 text-white text-xs font-bold px-3 py-2 rounded-xl shadow-md transition-all transform hover:scale-105">
              Instalar
          </button>
          <button id="pwa-dismiss-btn" class="text-gray-400 hover:text-white p-1.5 text-xs transition-colors" title="Cerrar">
              <i class="fas fa-times"></i>
          </button>
      </div>
  </div>

  <script>
    // Registro de Service Worker para PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(reg) {
                    console.log('[PWA] Service Worker registrado con éxito:', reg.scope);
                })
                .catch(function(err) {
                    console.warn('[PWA] Error al registrar Service Worker:', err);
                });
        });
    }

    // Manejo del aviso de instalación nativa PWA
    let deferredInstallPrompt = null;
    const installBanner = document.getElementById('pwa-install-banner');
    const installBtn = document.getElementById('pwa-install-btn');
    const dismissBtn = document.getElementById('pwa-dismiss-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredInstallPrompt = e;

        if (!localStorage.getItem('uhtv-pwa-dismissed')) {
            if (installBanner) {
                installBanner.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            }
        }
    });

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredInstallPrompt) return;
            deferredInstallPrompt.prompt();
            const { outcome } = await deferredInstallPrompt.userChoice;
            console.log('[PWA] Respuesta del usuario:', outcome);
            deferredInstallPrompt = null;
            if (installBanner) {
                installBanner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
            }
        });
    }

    if (dismissBtn) {
        dismissBtn.addEventListener('click', () => {
            if (installBanner) {
                installBanner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
                localStorage.setItem('uhtv-pwa-dismissed', 'true');
            }
        });
    }

    window.addEventListener('appinstalled', () => {
        console.log('[PWA] Aplicación UHTV instalada exitosamente');
        if (installBanner) {
            installBanner.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
        }
    });
  </script>
  @endif
