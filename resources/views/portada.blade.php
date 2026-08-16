@extends('layouts.main')

@section('title', 'Portada - Última Hora TV')

@section('content')
<!-- Hero Section con Carrusel Principal - Inspirado en Brújula Digital -->
<section class="bg-white dark:bg-gray-900 pt-4 pb-1 transition-colors duration-300">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      
      <!-- Carrusel Principal de Noticias -->
      <div class="lg:col-span-3">
        <div class="relative bg-white rounded-xl shadow-xl overflow-hidden h-full">
          <!-- Etiqueta de "ÚLTIMAS NOTICIAS" -->
          <div class="absolute top-4 left-4 z-20">
            <span class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wide shadow-lg">
              <i class="fas fa-bolt mr-2"></i>Últimas Noticias
            </span>
          </div>
          
          <div id="newsCarousel" class="carousel slide carousel-fade h-full group" data-bs-ride="carousel">
            <div class="carousel-inner h-full">
              @foreach($noticias->take(5) as $index => $noticia)
                <div class="carousel-item h-full @if($index === 0) active @endif">
                  <div class="relative h-full">
                    <a href="{{ route('show', $noticia->id) }}" class="block h-full">
                      <img src="{{ $noticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                           alt="{{ $noticia->titulo }}" 
                           class="w-full h-full min-h-[450px] object-cover"
                           onerror="handleImageError(this)">
                    </a>
                    <!-- Overlay con gradiente más suave -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    
                    <!-- Contenido sobre la imagen -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                      <div class="mb-3">
                        <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide">
                          {{ $noticia->category->name ?? 'Destacado' }}
                        </span>
                      </div>
                      <a href="{{ route('show', $noticia->id) }}" class="text-white no-underline block">
                        <h2 class="text-3xl font-bold mb-3 leading-tight hover:text-purple-300 transition-colors duration-300 line-clamp-2">
                          {{ $noticia->titulo }}
                        </h2>
                        <p class="text-gray-200 text-sm mb-2 line-clamp-2">
                          {{ $noticia->excerptLimpio ?? Str::limit(strip_tags($noticia->contenido), 150) }}
                        </p>
                        <div class="flex items-center text-gray-300 text-sm">
                          <i class="fas fa-clock mr-2"></i>
                          {{ \Carbon\Carbon::parse($noticia->created_at)->locale('es')->diffForHumans() }}
                          <span class="mx-2">•</span>
                          <i class="fas fa-eye mr-1"></i>
                          <span>Leer más</span>
                        </div>
                      </a>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
            
            <!-- Controles del carrusel mejorados (visibles al pasar el cursor) -->
            <button class="carousel-control-prev absolute left-4 top-1/2 transform -translate-y-1/2 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300" type="button" data-bs-target="#newsCarousel" data-bs-slide="prev">
              <div class="bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-full p-3 transition-all duration-300 shadow-lg">
                <i class="fas fa-chevron-left text-white text-lg"></i>
              </div>
              <span class="sr-only">Anterior</span>
            </button>
            <button class="carousel-control-next absolute right-4 top-1/2 transform -translate-y-1/2 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300" type="button" data-bs-target="#newsCarousel" data-bs-slide="next">
              <div class="bg-white/20 backdrop-blur-sm hover:bg-white/30 rounded-full p-3 transition-all duration-300 shadow-lg">
                <i class="fas fa-chevron-right text-white text-lg"></i>
              </div>
              <span class="sr-only">Siguiente</span>
            </button>
            
            <!-- Indicadores mejorados -->
            <div class="carousel-indicators">
              @foreach($noticias->take(5) as $index => $noticia)
                <button type="button" data-bs-target="#newsCarousel" data-bs-slide-to="{{ $index }}" 
                        class="@if($index === 0) active @endif" 
                        aria-current="@if($index === 0) true @else false @endif" 
                        aria-label="Slide {{ $index + 1 }}"></button>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar: Videos y Noticias Destacadas -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Últimos Videos -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700">
          <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4">
            <h3 class="font-bold text-lg flex items-center">
              <i class="fab fa-youtube mr-2"></i>
              En Vivo
            </h3>
          </div>
          <div class="p-4">
            <div class="relative pb-[56.25%] rounded-lg overflow-hidden">
              <iframe 
                class="absolute top-0 left-0 w-full h-full"
                src="https://www.youtube.com/embed?listType=playlist&list=UUx8c9O9qP3IjtnEKkEr-Bng" 
                title="Últimos Videos de UHTV Bolivia" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                loading="lazy">
              </iframe>
            </div>
          </div>
        </div>

        <!-- Noticias Más Leídas -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
          <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white p-4">
            <h3 class="font-bold text-lg flex items-center">
              <i class="fas fa-fire mr-2"></i>
              Más Leídas
            </h3>
          </div>
          <div class="p-4 space-y-4">
            @foreach($masLeidas as $index => $noticia)
              <div class="flex items-start space-x-3 pb-3 @if(!$loop->last) border-b border-gray-100 @endif">
                <span class="bg-purple-600 text-white text-xs font-bold px-2 py-1 rounded-full min-w-[24px] text-center">
                  {{ $index + 1 }}
                </span>
                <div class="flex-1">
                  <a href="{{ route('show', $noticia->id) }}" class="block">
                    <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm leading-tight hover:text-purple-600 dark:hover:text-purple-400 transition-colors line-clamp-2">
                      {{ $noticia->titulo }}
                    </h4>
                    <p class="text-gray-500 dark:text-gray-400 text-xs mt-1 flex items-center">
                      <i class="fas fa-clock mr-1"></i>
                      {{ \Carbon\Carbon::parse($noticia->created_at)->locale('es')->diffForHumans() }}
                    </p>
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Banner Publicitario -->
        <!-- Banner Publicitario -->
        @if(isset($banners['sidebar']) && $banners['sidebar']->count() > 0)
            @foreach($banners['sidebar'] as $banner)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden mb-6 border border-gray-100 dark:border-gray-700">
                    <div class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 p-3 text-center">
                        <span class="text-xs font-medium uppercase tracking-wide">Publicidad</span>
                    </div>
                    <a href="{{ $banner->link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="block">
                        <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" 
                             class="w-full h-auto hover:opacity-90 transition-opacity duration-300"
                             loading="lazy">
                    </a>
                </div>
            @endforeach
        @endif
      </div>
    </div>
  </div>
</section>

<!-- Cintillo de Últimas Noticias (News Ticker) -->
<section class="bg-uhtv-purple-700 dark:bg-uhtv-purple-900 text-white py-2 overflow-hidden border-y border-uhtv-purple-500 dark:border-uhtv-purple-800 relative shadow-md z-10">
  <div class="container mx-auto px-4 flex items-center">
    <!-- Etiqueta "Último Momento" -->
    <div class="bg-red-600 text-white text-xs font-bold uppercase px-3 py-1 rounded-full mr-4 flex-shrink-0 animate-pulse shadow-sm z-20 relative">
      <i class="fas fa-circle text-[8px] mr-2 align-middle"></i>Último Momento
    </div>
    
    <!-- Contenedor del Ticker -->
    <div class="ticker-wrap flex-1 overflow-hidden relative h-6">
      <div class="ticker">
        @foreach($ultimasNoticias->take(10) as $noticia)
          <div class="ticker__item inline-block px-4 text-sm font-medium hover:text-uhtv-purple-200 transition-colors">
            <a href="{{ route('show', $noticia->id) }}" class="flex items-center">
              <span class="text-uhtv-purple-300 mr-2">[{{ $noticia->created_at->format('H:i') }}]</span>
              {{ $noticia->titulo }}
            </a>
          </div>
          <span class="text-uhtv-purple-400 mx-2">•</span>
        @endforeach
      </div>
    </div>
  </div>
</section>

<style>
  /* Animación del Ticker */
  .ticker-wrap {
    width: 100%;
    white-space: nowrap;
  }
  .ticker {
    display: inline-block;
    animation: ticker 60s linear infinite;
  }
  .ticker:hover {
    animation-play-state: paused;
  }
  .ticker__item {
    display: inline-block;
  }
  @keyframes ticker {
    0% { transform: translateX(0); }
    100% { transform: translateX(-100%); }
  }
</style>

<!-- Sección de Noticias por Categorías - Estilo Brújula Digital -->
<section class="py-12 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">Noticias por Categorías</h2>
      <div class="w-32 h-1 bg-gradient-to-r from-purple-600 to-red-600 mx-auto rounded-full"></div>
      <p class="text-gray-600 dark:text-gray-300 mt-4 text-lg">Mantente informado con las últimas noticias de cada sección</p>
    </div>

    <!-- Secciones de Categorías como Brújula Digital -->
    @foreach($categorias->take(4) as $categoria)
      @if(isset($noticiasPorCategoria[$categoria->id]) && $noticiasPorCategoria[$categoria->id]->count() > 0)
        <div class="mb-16">
          <!-- Header de la Categoría -->
          <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
              <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $categoria->name }}</h3>
              <div class="w-16 h-1 bg-gradient-to-r from-purple-600 to-red-600 rounded-full"></div>
            </div>
            <a href="{{ route('categoria.noticias', $categoria->id) }}" 
               class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-semibold flex items-center transition-colors duration-300">
              Ver todas <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>

          <!-- Grid de Noticias de la Categoría -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($noticiasPorCategoria[$categoria->id]->take(6) as $index => $noticia)
              <a href="{{ route('show', $noticia->id) }}" class="block">
                <article class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700 @if($index === 0) md:col-span-2 md:row-span-2 @endif">
                  
                  <!-- Imagen de la Noticia -->
                  <div class="relative overflow-hidden">
                    <img src="{{ $noticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                         alt="{{ $noticia->titulo }}" 
                         class="w-full @if($index === 0) h-64 md:h-80 @else h-48 @endif object-cover transition-transform duration-500 hover:scale-105"
                         loading="lazy"
                         onerror="handleImageError(this)">
                    
                    <!-- Etiqueta de categoría -->
                    <div class="absolute top-4 left-4">
                      <span class="bg-gradient-to-r from-purple-600 to-red-600 text-white px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide shadow-lg">
                        {{ $categoria->name }}
                      </span>
                    </div>
                    
                    <!-- Indicador de noticia principal -->
                    @if($index === 0)
                      <div class="absolute top-4 right-4">
                        <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                          PRINCIPAL
                        </span>
                      </div>
                    @endif
                    
                    <!-- Overlay sutil -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                  </div>
                  
                  <!-- Contenido -->
                  <div class="p-4 @if($index === 0) md:p-6 @endif">
                    <!-- Metadata -->
                    <div class="mb-3">
                      <span class="text-gray-500 dark:text-gray-400 text-sm flex items-center">
                        <i class="fas fa-clock mr-2 text-purple-600"></i>
                        {{ \Carbon\Carbon::parse($noticia->created_at)->locale('es')->diffForHumans() }}
                      </span>
                    </div>
                    
                    <!-- Título y contenido -->
                    <div class="block group">
                      <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2 @if($index === 0) text-xl md:text-2xl @else text-lg @endif leading-tight line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                        {{ $noticia->titulo }}
                      </h4>
                      @if($index === 0)
                        <p class="text-gray-600 dark:text-gray-300 text-base line-clamp-3 leading-relaxed mb-4">
                          {{ $noticia->excerptLimpio ?? Str::limit(strip_tags($noticia->contenido), 200) }}
                        </p>
                      @else
                        <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 leading-relaxed">
                          {{ $noticia->excerptLimpio ?? Str::limit(strip_tags($noticia->contenido), 100) }}
                        </p>
                      @endif
                    </div>
                    
                    <!-- Botón de acción -->
                    @if($index === 0)
                      <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <div class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-semibold text-sm transition-colors duration-300 group">
                          Leer noticia completa
                          <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                        </div>
                      </div>
                    @endif
                  </div>
                </article>
              </a>
            @endforeach
          </div>
        </div>
      @endif
    @endforeach
    
    <!-- Sección de Todas las Categorías -->
    <div class="text-center mt-16">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-10 relative overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-red-50 dark:from-purple-900/10 dark:to-red-900/10"></div>
        <div class="relative">
          <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">Explora Todas las Secciones</h3>
          <p class="text-gray-600 dark:text-gray-300 mb-8 text-lg">Descubre todas nuestras categorías de noticias</p>
          <div class="flex flex-wrap justify-center gap-4">
            @foreach($categorias as $categoria)
              <a href="{{ route('categoria.noticias', $categoria->id) }}" 
                 class="bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-purple-600 hover:to-red-600 hover:text-white px-6 py-3 rounded-full transition-all duration-300 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-1 border border-gray-200 dark:border-gray-600">
                {{ $categoria->name }}
              </a>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Script de carrusel optimizado -->
<script src="{{ asset('js/carousel.js') }}"></script>


<!-- Sección de Últimas Noticias - Diseño Moderno -->
<section class="py-12 bg-white dark:bg-gray-900 transition-colors duration-300">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">Últimas Noticias</h2>
      <div class="w-32 h-1 bg-gradient-to-r from-red-600 to-purple-600 mx-auto rounded-full"></div>
      <p class="text-gray-600 dark:text-gray-300 mt-4 text-lg">Las noticias más recientes e importantes del momento</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      @foreach($ultimasNoticias->take(6) as $noticia)
        <a href="{{ route('show', $noticia->id) }}" class="block">
          <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
            <div class="relative overflow-hidden">
              <img src="{{ $noticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                   alt="{{ $noticia->titulo }}" 
                   class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110"
                   loading="lazy"
                   onerror="handleImageError(this)">
              
              <!-- Etiqueta de Categoría -->
              <div class="absolute top-4 left-4">
                <span class="bg-gradient-to-r from-purple-600 to-red-600 text-white px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide shadow-lg">
                  {{ $noticia->category->name ?? 'General' }}
                </span>
              </div>
              
              <!-- Indicador de "Nuevo" para noticias recientes -->
              @if(\Carbon\Carbon::parse($noticia->created_at)->diffInHours() < 6)
                <div class="absolute top-4 right-4">
                  <span class="bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold animate-pulse">
                    NUEVO
                  </span>
                </div>
              @endif
              
              <!-- Overlay sutil -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
            </div>
            
            <div class="p-6">
              <!-- Metadata -->
              <div class="mb-4 flex items-center justify-between">
                <span class="text-gray-500 dark:text-gray-400 text-sm flex items-center">
                  <i class="fas fa-clock mr-2 text-red-600"></i>
                  {{ \Carbon\Carbon::parse($noticia->created_at)->locale('es')->diffForHumans() }}
                </span>
                <span class="text-gray-400 dark:text-gray-550 text-xs flex items-center">
                  <i class="fas fa-eye mr-1"></i>
                  Leer más
                </span>
              </div>
              
              <!-- Título y Contenido -->
              <div class="block group">
                <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-3 text-lg leading-tight line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                  {{ $noticia->titulo }}
                </h3>
                <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 leading-relaxed">
                  {{ $noticia->excerptLimpio ?? Str::limit(strip_tags($noticia->contenido), 120) }}
                </p>
              </div>
              
              <!-- Botón de Acción -->
              <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                <div class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-semibold text-sm transition-colors duration-300 group">
                  Continuar leyendo
                  <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                </div>
              </div>
            </div>
          </article>
        </a>
      @endforeach
    </div>
    
    <!-- Botón para Ver Más Noticias -->
    <div class="text-center mt-12">
      <a href="#" class="inline-flex items-center bg-gradient-to-r from-purple-600 to-red-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:from-purple-700 hover:to-red-700 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
        <i class="fas fa-newspaper mr-3"></i>
        Ver Todas las Noticias
        <i class="fas fa-arrow-right ml-3"></i>
      </a>
    </div>
  </div>
</section>

<!-- Banner Publicitario Horizontal -->
<section class="py-6 bg-white dark:bg-gray-900 transition-colors duration-300">
  <div class="container mx-auto px-4">
    <div class="text-center mb-4">
      <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">PUBLICIDAD</span>
    </div>
    <div class="flex justify-center">
      <a href="https://radiobetania.com/" target="_blank" rel="noopener noreferrer" class="block max-w-4xl"> 
        <img src="{{ asset('images/betania.jpg') }}" alt="Publicidad Radio Betania" 
             class="w-full h-auto rounded-lg shadow-lg hover:opacity-90 transition"
             loading="lazy">
      </a>
    </div>
  </div>
</section>


{{-- Widget de Elfsight habilitado --}}
<script src="https://static.elfsight.com/platform/platform.js" async></script>
<div class="elfsight-app-fbb50d0e-c779-44ab-bf7f-b16fd3542ccc" data-elfsight-app-lazy></div>

@if(isset($banners['popup']) && $banners['popup']->count() > 0)
  @php
    $popupBanner = $banners['popup']->first();
  @endphp
  <!-- Modal Publicitario Emergente -->
  <div id="promoModal" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-black/85 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="relative max-w-lg w-full mx-4 transform scale-90 opacity-0 transition-all duration-300 ease-out" id="promoModalContent">
      
      <!-- Contenedor con marco grueso gris oscuro e imagen -->
      <div class="relative bg-black rounded overflow-hidden shadow-2xl" style="border: 8px solid #2d3748; outline: 2px solid #1a202c;">
        
        <!-- Botón de cerrar rojo 'X' en la esquina superior derecha interna -->
        <button onclick="closePromoModal()" class="absolute top-3 right-4 z-10 text-red-500 hover:text-red-400 transition-colors duration-200 focus:outline-none" aria-label="Cerrar">
          <i class="fas fa-times text-2xl filter drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]"></i>
        </button>

        <a href="{{ $popupBanner->link ?? '#' }}" {{ $popupBanner->link ? 'target="_blank" rel="noopener noreferrer"' : '' }} class="block overflow-hidden group">
          <img src="{{ asset($popupBanner->image_path) }}" alt="{{ $popupBanner->title }}" 
               class="w-full h-auto object-cover max-h-[70vh] transition-transform duration-500 group-hover:scale-[1.01]">
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
        
        // Mostrar modal tras 1.5 segundos
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            modalContent.classList.remove('scale-90', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
            
            // Prevenir scroll en la página mientras el modal está abierto
            document.body.style.overflow = 'hidden';
        }, 1500);

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
            modalContent.classList.remove('scale-100', 'opacity-100');
            modalContent.classList.add('scale-95', 'opacity-0');
            modal.classList.remove('opacity-100', 'pointer-events-auto');
            modal.classList.add('opacity-0', 'pointer-events-none');
            
            // Restaurar scroll de la página
            document.body.style.overflow = '';
        }
    }
  </script>
@endif

@endsection
