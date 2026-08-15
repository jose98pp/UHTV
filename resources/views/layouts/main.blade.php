<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="author" content="Última Hora TV">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="theme-color" content="#7c3aed">
    
    <title>@yield('title', 'Última Hora TV')</title>
    
    <!-- DNS Prefetch for better performance -->
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdn.tailwindcss.com">
    
    <!-- Preconnect for critical resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Critical CSS - Load synchronously -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Non-critical CSS - Load asynchronously -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"></noscript>
    
    <!-- Google Fonts with display=swap for better performance -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dashing+Alternate&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'uhtv-purple': {
                            600: '#7c3aed',
                            700: '#6d28d9',
                        },
                        'uhtv-red': {
                            600: '#dc2626',
                            700: '#b91c1c',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Optimized CSS - Load with high priority -->
    <link rel="preload" href="{{ asset('css/optimized.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="{{ asset('css/optimized.css') }}" rel="stylesheet"></noscript>
    
    <!-- CSS y JS personalizados -->
    @php
        use App\Helpers\AssetHelper;
        $cssFiles = AssetHelper::getAllCssFiles();
        $jsFiles = ['resources/js/app.jsx'];
        $allAssets = array_merge($cssFiles, $jsFiles);
    @endphp
    @vite($allAssets)

    <!-- Script de inicialización inmediata para modo oscuro -->
    <script>
        // Aplicar modo oscuro inmediatamente para evitar flash
        (function() {
            try {
                const DARK_MODE_KEY = 'uhtv-dark-mode';
                const savedTheme = localStorage.getItem(DARK_MODE_KEY);
                const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                const shouldBeDark = savedTheme === 'dark' || (!savedTheme && systemPrefersDark);
                
                if (shouldBeDark) {
                    document.documentElement.classList.add('dark');
                    if (document.body) {
                        document.body.classList.add('dark');
                    }
                    document.documentElement.style.colorScheme = 'dark';
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                    
                    // Actualizar meta theme-color inmediatamente
                    const themeColorMeta = document.querySelector('meta[name="theme-color"]');
                    if (themeColorMeta) {
                        themeColorMeta.setAttribute('content', '#1f2937');
                    }
                } else {
                    document.documentElement.classList.remove('dark');
                    if (document.body) {
                        document.body.classList.remove('dark');
                    }
                    document.documentElement.style.colorScheme = 'light';
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                    
                    // Actualizar meta theme-color inmediatamente
                    const themeColorMeta = document.querySelector('meta[name="theme-color"]');
                    if (themeColorMeta) {
                        themeColorMeta.setAttribute('content', '#7c3aed');
                    }
                }
                
                // Marcar que la inicialización inmediata se completó
                window.darkModeImmediateInit = true;
                window.darkModeInitialState = shouldBeDark;
                
            } catch (e) {
                console.warn('Error in immediate dark mode initialization:', e);
            }
        })();
    </script>

    <!-- Inline critical CSS for immediate rendering -->
    <style>
        /* Critical above-the-fold styles */
        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }
        .d-flex { display: flex; }
        .justify-content-center { justify-content: center; }
        .align-items-center { align-items: center; }
        .text-center { text-align: center; }
        
        /* Transiciones suaves para modo oscuro */
        * {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Estilos base para modo oscuro */
        .dark {
            color-scheme: dark;
        }
    </style>

</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
  <!-- Header Mejorado - Inspirado en Brújula Digital -->
  <header class="relative bg-gradient-to-r from-purple-800 via-purple-600 to-red-600 dark:from-purple-900 dark:via-purple-700 dark:to-red-700 bg-cover bg-center min-h-[140px] flex items-center justify-center text-white overflow-hidden" 
          style="background-image: url('{{ isset($banners['portada_top']) && $banners['portada_top']->count() > 0 ? asset($banners['portada_top']->first()->image_path) : asset('images/banner.png') }}');">
    <!-- Overlay con gradiente mejorado -->
    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/70 dark:from-black/80 dark:via-black/60 dark:to-black/80"></div>
    
    <!-- Elementos decorativos -->
    <div class="absolute inset-0 opacity-10">
      <div class="absolute top-4 left-4 w-32 h-32 bg-white rounded-full blur-3xl"></div>
      <div class="absolute bottom-4 right-4 w-24 h-24 bg-purple-300 rounded-full blur-2xl"></div>
    </div>
    
    <div class="relative text-center z-10 py-6">
      <a href="/" class="flex items-center justify-center space-x-4 mb-4 group">
        <div class="relative">
          <img src="/images/Logo.jpg" alt="UltimaHoraTV" class="w-20 h-20 rounded-full border-4 border-white/30 shadow-2xl transition-transform duration-300 group-hover:scale-110">
          <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-purple-400/20 to-red-400/20"></div>
        </div>
        <div>
          <h1 class="text-5xl font-bold text-white mb-1 tracking-tight">Ultima Hora<span class="text-red-400">TV</span></h1>
          <p class="text-purple-200 text-sm font-medium uppercase tracking-widest">Noticias Del Momento</p>
        </div>
      </a>
      
      <!-- Redes Sociales Mejoradas -->
      <div class="flex justify-center space-x-3 mt-4">
        <a href="https://facebook.com/uhtvbolivia" target="_blank" class="bg-white/10 backdrop-blur-sm hover:bg-blue-600 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
          <i class="fab fa-facebook text-lg"></i>
        </a>
        <a href="https://www.youtube.com/@UHTVBolivia" target="_blank" class="bg-white/10 backdrop-blur-sm hover:bg-red-600 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
          <i class="fab fa-youtube text-lg"></i>
        </a>
        <a href="https://instagram.com/uhtvbolivia" target="_blank" class="bg-white/10 backdrop-blur-sm hover:bg-pink-600 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
          <i class="fab fa-instagram text-lg"></i>
        </a>
        <a href="https://x.com/UhtvBol" target="_blank" class="bg-white/10 backdrop-blur-sm hover:bg-gray-800 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
          <i class="fab fa-x-twitter text-lg"></i>
        </a>
        <a href="https://tiktok.com/@uhtvbolivia" target="_blank" class="bg-white/10 backdrop-blur-sm hover:bg-black text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 hover:shadow-lg">
          <i class="fab fa-tiktok text-lg"></i>
        </a>
      </div>
    </div>
  </header>

<!-- Navbar Mejorada -->
<nav class="bg-white dark:bg-gray-900 shadow-xl sticky top-0 left-0 w-full z-50 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
 <div class="container mx-auto flex justify-between items-center py-4 px-4">
  <!-- Logo/Inicio -->
  <a href="/" class="flex items-center space-x-2 text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300">
    <i class="fas fa-home text-xl"></i>
    <span class="text-xl font-bold">Inicio</span>
  </a>
  
  <!-- Botón del menú hamburguesa mejorado -->
  <button id="hamburgerButton" class="lg:hidden text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 focus:outline-none focus:text-purple-600 dark:focus:text-purple-400 transition-colors duration-300">
    <div class="w-6 h-6 flex flex-col justify-center items-center">
      <span class="block w-6 h-0.5 bg-current transition-all duration-300 transform"></span>
      <span class="block w-6 h-0.5 bg-current mt-1 transition-all duration-300 transform"></span>
      <span class="block w-6 h-0.5 bg-current mt-1 transition-all duration-300 transform"></span>
    </div>
  </button>
  
  <!-- Menú para pantallas grandes -->
  <div class="hidden lg:flex space-x-8 mx-auto">
    @forelse($categorias ?? [] as $categoria)
      <a href="{{ route('categoria.noticias', $categoria->id) }}" 
         class="text-gray-700 dark:text-gray-300 font-semibold hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300 relative group py-2">
        {{ $categoria->name }}
        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-gradient-to-r from-purple-600 to-red-600 transition-all duration-300 group-hover:w-full"></span>
      </a>
    @empty
      <span class="text-gray-500 dark:text-gray-400 italic">No hay categorías disponibles</span>
    @endforelse
  </div>
  
  <!-- Búsqueda y Controles -->
  <div class="hidden lg:flex items-center space-x-4">
    <!-- Formulario de búsqueda -->
    <form action="{{ route('search') }}" method="GET" class="relative">
      <input type="text" 
             name="q" 
             placeholder="Buscar noticias..." 
             class="w-64 px-4 py-2 pl-10 pr-4 text-sm text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-full focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300">
      <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500 text-sm"></i>
      <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-purple-600 hover:bg-purple-700 text-white p-1.5 rounded-full transition-colors duration-300">
        <i class="fas fa-arrow-right text-xs"></i>
      </button>
    </form>
    
    <!-- Toggle de modo oscuro -->
    <button data-dark-mode-toggle class="text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800">
      <i class="fas fa-sun sun-icon text-lg hidden"></i>
      <i class="fas fa-moon moon-icon text-lg"></i>
    </button>
  </div>
</div>

  <!-- Sidebar para móviles mejorado -->
  <div
    id="mobileMenu"
    class="fixed top-0 left-0 h-full w-80 bg-white dark:bg-gray-900 shadow-2xl transform -translate-x-full transition-all duration-300 lg:hidden z-50 overflow-y-auto"
  >
    <!-- Header del menú móvil -->
    <div class="bg-gradient-to-r from-purple-600 to-red-600 text-white p-6">
      <div class="flex justify-between items-center">
        <div class="flex items-center space-x-3">
          <img src="/images/Logo.jpg" alt="UHTV" class="w-10 h-10 rounded-full">
          <span class="text-xl font-bold">UHTV</span>
        </div>
        <button id="closeMenu" class="text-white hover:text-gray-200 focus:outline-none transition-colors duration-300">
          <i class="fas fa-times text-2xl"></i>
        </button>
      </div>
    </div>
    
    <!-- Navegación principal -->
    <div class="p-6">
      <a href="/" class="flex items-center space-x-3 text-gray-800 dark:text-gray-200 hover:text-purple-600 dark:hover:text-purple-400 transition-colors duration-300 py-3 border-b border-gray-100 dark:border-gray-700">
        <i class="fas fa-home text-lg"></i>
        <span class="font-semibold">Inicio</span>
      </a>
      
      <!-- Búsqueda móvil -->
      <div class="mt-4">
        <form action="{{ route('search') }}" method="GET" class="relative">
          <input type="text" 
                 name="q" 
                 placeholder="Buscar noticias..." 
                 class="w-full px-4 py-3 pl-10 pr-4 text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
          <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
          <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-lg transition-colors duration-300">
            <i class="fas fa-arrow-right text-sm"></i>
          </button>
        </form>
      </div>
    </div>
    
    <!-- Categorías -->
    <div class="px-6">
      <h3 class="text-gray-500 dark:text-gray-400 uppercase text-sm font-semibold tracking-wide mb-4">Categorías</h3>
      <div class="space-y-2">
        @forelse($categorias ?? [] as $categoria)
          <a href="{{ route('categoria.noticias', $categoria->id) }}" 
             class="flex items-center space-x-3 text-gray-700 dark:text-gray-300 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-all duration-300 py-3 px-4 rounded-lg">
            <i class="fas fa-folder text-sm"></i>
            <span class="font-medium">{{ $categoria->name }}</span>
          </a>
        @empty
          <span class="text-gray-500 dark:text-gray-400 italic text-sm">No hay categorías disponibles</span>
        @endforelse
      </div>
    </div>
    
    <!-- Redes sociales en el menú móvil -->
    <div class="p-6 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-4">
      <h4 class="text-gray-500 dark:text-gray-400 uppercase text-xs font-semibold tracking-wide mb-3">Síguenos</h4>
      <div class="flex space-x-3">
        <a href="https://facebook.com/uhtvbolivia" target="_blank" class="bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition-colors duration-300">
          <i class="fab fa-facebook text-sm"></i>
        </a>
        <a href="https://www.youtube.com/@UHTVBolivia" target="_blank" class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors duration-300">
          <i class="fab fa-youtube text-sm"></i>
        </a>
        <a href="https://instagram.com/uhtvbolivia" target="_blank" class="bg-pink-600 text-white p-2 rounded-full hover:bg-pink-700 transition-colors duration-300">
          <i class="fab fa-instagram text-sm"></i>
        </a>
        <a href="https://x.com/UhtvBol" target="_blank" class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-900 transition-colors duration-300">
          <i class="fab fa-x-twitter text-sm"></i>
        </a>
        <a href="https://tiktok.com/@uhtvbolivia" target="_blank" class="bg-black text-white p-2 rounded-full hover:bg-gray-800 transition-colors duration-300">
          <i class="fab fa-tiktok text-sm"></i>
        </a>
      </div>
    </div>
  </div>
  
  <!-- Overlay para cerrar el menú -->
  <div id="mobileMenuOverlay" class="fixed inset-0 bg-black bg-opacity-50 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden z-40"></div>
</nav>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const hamburgerButton = document.getElementById('hamburgerButton');
    const closeMenu = document.getElementById('closeMenu');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    function openMobileMenu() {
      mobileMenu.classList.remove('-translate-x-full');
      mobileMenuOverlay.classList.remove('opacity-0', 'pointer-events-none');
      document.body.style.overflow = 'hidden';
      
      // Animación del botón hamburguesa
      const spans = hamburgerButton.querySelectorAll('span');
      spans[0].style.transform = 'rotate(45deg) translate(5px, 5px)';
      spans[1].style.opacity = '0';
      spans[2].style.transform = 'rotate(-45deg) translate(7px, -6px)';
    }

    function closeMobileMenu() {
      mobileMenu.classList.add('-translate-x-full');
      mobileMenuOverlay.classList.add('opacity-0', 'pointer-events-none');
      document.body.style.overflow = '';
      
      // Restaurar botón hamburguesa
      const spans = hamburgerButton.querySelectorAll('span');
      spans[0].style.transform = '';
      spans[1].style.opacity = '';
      spans[2].style.transform = '';
    }

    // Event listeners
    if (hamburgerButton) {
      hamburgerButton.addEventListener('click', openMobileMenu);
    }

    if (closeMenu) {
      closeMenu.addEventListener('click', closeMobileMenu);
    }

    if (mobileMenuOverlay) {
      mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    }

    // Cerrar menú al hacer clic en un enlace
    const mobileMenuLinks = mobileMenu.querySelectorAll('a');
    mobileMenuLinks.forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });

    // Cerrar menú con tecla Escape
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && !mobileMenu.classList.contains('-translate-x-full')) {
        closeMobileMenu();
      }
    });
  });
</script>

    <!-- Contenedor de Publicidad -->
    <!-- Banner Publicitario -->
    <!-- Contenedor de Publicidad -->
    <!-- Banner Publicitario -->
    @if(isset($banners['footer']) && $banners['footer']->count() > 0)
        @foreach($banners['footer'] as $banner)
            <div class="publicidad w-full flex justify-center items-center my-6 px-4">
                <a href="{{ $banner->link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="block w-full max-w-5xl transition-transform hover:scale-[1.01] duration-300"> 
                    <img src="{{ asset($banner->image_path) }}" 
                         alt="{{ $banner->title }}" 
                         class="w-full h-auto rounded-xl shadow-lg object-cover border border-gray-200 dark:border-gray-700" 
                         loading="lazy">
                </a>
            </div>
        @endforeach
    @endif
    
    <!-- Contenido Principal -->
    <main class="container my-4">
        @yield('content')
    </main>

   <!-- Footer Moderno - Inspirado en la imagen -->
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-12 border-t border-gray-700">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Logo y Descripción -->
            <div class="lg:col-span-1">
                <div class="flex items-center space-x-3 mb-4">
                    <img src="{{ asset('images/Logo.jpg') }}" alt="ÚltimaHoraTV" class="w-12 h-12 rounded-full" loading="lazy">
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
                            <a href="{{ route('categoria.noticias', $categoria->id) }}" 
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
    
    <!-- CSS Optimization and Browser Compatibility -->
    <script src="{{ asset('js/css-optimization.js') }}"></script>
    
    <!-- Diagnostics (solo en desarrollo) -->
    <script src="{{ asset('js/diagnostics.js') }}"></script>
    
    <!-- Error Handler -->
    <script src="{{ asset('js/error-handler.js') }}"></script>
    
    <!-- Bootstrap JS - Solo una versión -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>


