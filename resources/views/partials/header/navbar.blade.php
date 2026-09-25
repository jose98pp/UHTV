<!-- ============================================================
     BANNER PUBLICITARIO - A todo el ancho, entre header y nav
     Posición: portada_top
================================================================ -->
<style>
  .uhtv-banner-matched-height {
    height: 140px;
    min-height: 140px;
  }
  .uhtv-banner-img-matched {
    height: 100%;
    max-height: 132px;
    width: auto;
    max-width: 100%;
    object-fit: contain;
  }
</style>

@if(isset($banners['portada_top']) && $banners['portada_top']->count() > 0)
  <div class="uhtv-banner-matched-height w-full bg-gray-100 dark:bg-gray-950 border-b-2 border-purple-700/30 flex justify-center items-center py-1">
    <div class="container mx-auto px-2 sm:px-4 flex justify-center items-center h-full">
      <a href="{{ $banners['portada_top']->first()->link ?? '#' }}" target="_blank" rel="noopener noreferrer sponsored" class="flex justify-center items-center w-full h-full text-center" title="Publicidad">
        <img src="{{ asset($banners['portada_top']->first()->image_path) }}"
             alt="{{ $banners['portada_top']->first()->title ?? 'Publicidad' }}"
             class="uhtv-banner-img-matched mx-auto rounded transition-all duration-300"
             loading="eager"
             decoding="async">
      </a>
    </div>
  </div>
@else
  {{-- Placeholder cuando no hay banner: franja de color con logo/texto con misma altura del header --}}
  <div class="uhtv-banner-matched-height w-full bg-gradient-to-r from-purple-900 via-indigo-800 to-purple-900 dark:from-gray-900 dark:via-indigo-950 dark:to-gray-900 border-b-2 border-purple-600/40 flex items-center justify-center">
    <div class="flex items-center gap-4 opacity-60">
      <span class="text-white/50 text-xs uppercase tracking-widest font-semibold">Espacio Publicitario</span>
      <div class="h-px w-24 bg-white/20"></div>
      <span class="text-white/30 text-[10px] uppercase tracking-wider">728 × 90</span>
    </div>
  </div>
@endif

<!-- Navbar Mejorada -->
<nav id="category-navigation" class="bg-white dark:bg-gray-900 shadow-xl sticky top-0 left-0 w-full z-50 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
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
      <a href="{{ $categoria->url }}" 
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
          <img src="/images/Logo.jpg" alt="UHTV" class="w-10 h-10 rounded-full" loading="lazy" decoding="async">
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
      @if(isset($transmisionEnVivo) && $transmisionEnVivo)
        <a href="{{ route('transmisiones.en-vivo') }}" class="flex items-center space-x-3 text-red-600 dark:text-red-400 hover:text-red-700 font-semibold py-3 border-b border-gray-100 dark:border-gray-700">
          <span class="relative flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-600"></span>
          </span>
          <span>🔴 En Vivo Ahora</span>
        </a>
      @endif
      
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
          <a href="{{ $categoria->url }}" 
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

  @if(isset($transmisionEnVivo) && $transmisionEnVivo)
    <!-- Alerta En Vivo Principal -->
    <aside id="liveStreamAlert" class="bg-gradient-to-r from-red-700 via-red-600 to-red-800 text-white shadow-xl border-y border-red-500/40 relative z-30 transition-all duration-300" aria-label="Alerta de Transmisión en Vivo">
      <div class="container mx-auto px-4 py-2.5 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3 overflow-hidden min-w-0">
          <span class="relative flex h-3 w-3 flex-shrink-0">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-90"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
          </span>
          <span class="bg-black/40 text-white text-[10px] font-black uppercase tracking-widest px-2.5 py-0.5 rounded-full border border-white/20 flex-shrink-0">
            🔴 EN VIVO AHORA
          </span>
          <span class="text-xs sm:text-sm font-bold truncate text-white">
            {{ $transmisionEnVivo->titulo }}
          </span>
        </div>
        <div class="flex items-center space-x-2 flex-shrink-0 ml-auto">
          <button type="button" 
                  data-open-live-modal 
                  data-stream-embed="{{ $transmisionEnVivo->embed_url }}"
                  data-stream-title="{{ $transmisionEnVivo->titulo }}"
                  class="bg-white text-red-600 hover:bg-gray-100 font-extrabold text-xs px-3.5 py-1.5 rounded-full shadow transition-all duration-200 flex items-center gap-1.5 transform hover:scale-105 cursor-pointer">
            <i class="fas fa-play text-[9px]"></i>
            <span>Ver Transmisión</span>
          </button>
          <button type="button" 
                  onclick="document.getElementById('liveStreamAlert').style.display='none'" 
                  class="text-white/80 hover:text-white p-1 focus:outline-none transition-colors" 
                  title="Cerrar aviso">
            <i class="fas fa-times text-xs"></i>
          </button>
        </div>
      </div>
    </aside>
  @endif

@if(request()->routeIs('portada'))
  @include('partials.multimedia-strip', [
      'contenidos' => $multimediaPortada ?? ($transmisionesRecientes ?? collect())
  ])
@endif

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
