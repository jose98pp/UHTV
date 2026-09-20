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
          <!-- Etiqueta de "ÚLTIMAS NOTICIAS" / Indicador Principal -->
          <div class="absolute top-4 left-4 z-20">
            <span class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white px-4 py-2 rounded-lg text-sm font-bold uppercase tracking-wide shadow-lg border border-white/20">
              <i class="fas fa-bolt mr-2"></i>Últimas Noticias
            </span>
          </div>
          
          <div id="newsCarousel" class="carousel slide carousel-fade h-full group" data-bs-ride="carousel">
            <div class="carousel-inner h-full">
              @foreach($noticias->take(5) as $index => $noticia)
                <div class="carousel-item h-full @if($index === 0) active @endif">
                  <div class="relative h-full">
                    <a href="{{ $noticia->url }}" class="block h-full">
                      <img src="{{ $noticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                           alt="{{ $noticia->titulo }}" 
                           class="w-full h-full min-h-[450px] object-cover"
                           @if($index === 0) fetchpriority="high" @else loading="lazy" decoding="async" @endif
                           onerror="handleImageError(this)">
                    </a>
                    <!-- Overlay con gradiente más suave -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>
                    
                    <!-- Contenido sobre la imagen -->
                    <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                      <div class="mb-3">
                        <span class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide shadow-md border border-white/20">
                          {{ $noticia->category->name ?? 'Destacado' }}
                        </span>
                      </div>
                      <a href="{{ $noticia->url }}" class="text-white no-underline block">
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
        <!-- Videos UHTV -->
        @php
            $portadaRecientes = $transmisionesRecientes ?? collect();
            $portadaVivo = $transmisionEnVivo ?? null;
            $portadaStream = $portadaVivo ?? ($portadaRecientes->first() ?? null);
            $sidebarEmbed = $portadaStream ? $portadaStream->embed_url : 'https://www.youtube.com/embed?listType=playlist&list=UUx8c9O9qP3IjtnEKkEr-Bng';
            $sidebarTitle = $portadaStream ? $portadaStream->titulo : 'Videos de UHTV';
        @endphp
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700">
          <div class="bg-gradient-to-r from-red-600 to-red-700 text-white p-4 flex items-center justify-between">
            <h3 class="font-bold text-lg flex items-center">
              <i class="fab fa-youtube mr-2"></i>
              <span>Videos UHTV</span>
            </h3>
            @if($portadaStream && $portadaStream->en_vivo)
              <span class="inline-flex items-center gap-1 bg-white text-red-600 text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full shadow-sm animate-pulse">
                <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                VIVO
              </span>
            @endif
          </div>
          <div class="p-4">
            <div class="relative pb-[56.25%] rounded-lg overflow-hidden shadow-inner bg-black">
              <iframe 
                class="absolute top-0 left-0 w-full h-full"
                src="{{ $sidebarEmbed }}" 
                title="{{ $sidebarTitle }}" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen
                loading="lazy">
              </iframe>
            </div>
            @if($portadaStream)
              <div class="mt-3">
                <h4 class="font-bold text-gray-900 dark:text-gray-100 text-sm line-clamp-2 mb-2">
                  {{ $portadaStream->titulo }}
                </h4>
                <div class="flex items-center justify-between text-xs pt-2 border-t border-gray-100 dark:border-gray-700">
                  <button type="button" 
                          data-open-live-modal
                          data-stream-embed="{{ $portadaStream->embed_url }}"
                          data-stream-title="{{ $portadaStream->titulo }}"
                          class="text-red-600 dark:text-red-400 font-bold hover:underline flex items-center gap-1 focus:outline-none">
                    <i class="fas fa-expand-alt text-[10px]"></i>
                    <span>Abrir en Modal</span>
                  </button>
                </div>
              </div>
            @endif
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
                  <a href="{{ $noticia->url }}" class="block">
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
                             loading="lazy"
                             decoding="async">
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
    <div class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white text-xs font-bold uppercase px-3.5 py-1.5 rounded-full mr-4 flex-shrink-0 animate-pulse shadow-sm z-20 relative border border-white/20">
      <i class="fas fa-circle text-[8px] mr-2 align-middle"></i>Último Momento
    </div>
    
    <!-- Contenedor del Ticker -->
    <div class="ticker-wrap flex-1 overflow-hidden relative h-6">
      <div class="ticker">
        @foreach($ultimasNoticias->take(10) as $noticia)
          <div class="ticker__item inline-block px-4 text-sm font-medium hover:text-uhtv-purple-200 transition-colors">
            <a href="{{ $noticia->url }}" class="flex items-center">
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
  /* Indicadores del carrusel con degradé celeste a lila */
  .carousel-indicators button.active {
    background: linear-gradient(90deg, #0099ff, #9333ea) !important;
    border: 1px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 0 10px rgba(0, 153, 255, 0.5);
  }
</style>

<!-- ============================================================
     BANNER PUBLICITARIO - Antes de la sección de Videos UHTV
================================================================ -->
@if(isset($banners['portada_middle']) && $banners['portada_middle']->count() > 0)
  <div class="w-full bg-gray-100 dark:bg-gray-900 py-4 border-b border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4">
      <p class="text-center text-gray-400 dark:text-gray-500 text-[10px] uppercase tracking-widest font-semibold mb-2">Publicidad</p>
      @foreach($banners['portada_middle'] as $banner)
        <div class="flex justify-center mb-3 last:mb-0">
          <a href="{{ $banner->link ?? '#' }}" target="_blank" rel="noopener noreferrer sponsored" class="block max-w-5xl w-full group">
            <img src="{{ asset($banner->image_path) }}"
                 alt="{{ $banner->title }}"
                 class="w-full h-auto rounded-xl shadow-md hover:opacity-95 transition-opacity duration-300 border border-gray-100 dark:border-gray-700"
                 loading="lazy"
                 decoding="async">
          </a>
        </div>
      @endforeach
    </div>
  </div>
@endif

<!-- ============================================================
     SECCIÓN VIDEOS UHTV - YouTube embeds a lo ancho
================================================================ -->
<section class="py-10 bg-gray-950 dark:bg-black border-y-2 border-purple-800/40 transition-colors duration-300">
  <div class="container mx-auto px-4">

    <!-- Header de la sección -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-600 shadow-lg shadow-red-600/40">
          <i class="fab fa-youtube text-white text-lg"></i>
        </div>
        <div>
          <h2 class="text-2xl font-extrabold text-white leading-none">Videos <span class="text-red-500">UHTV</span></h2>
          <p class="text-gray-400 text-xs mt-0.5 uppercase tracking-wider">Canal Oficial · YouTube</p>
        </div>
        @php
          $hayVivo = isset($transmisionEnVivo) && $transmisionEnVivo;
        @endphp
        @if($hayVivo)
          <span class="inline-flex items-center gap-1.5 bg-red-600/20 border border-red-500/50 text-red-400 text-[10px] font-black uppercase px-2.5 py-1 rounded-full animate-pulse ml-2">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
            EN VIVO AHORA
          </span>
        @endif
      </div>
      <a href="https://www.youtube.com/@UHTVBolivia" target="_blank" rel="noopener noreferrer"
         class="hidden sm:flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-full transition-all duration-300 shadow hover:shadow-red-600/40 transform hover:-translate-y-0.5">
        <i class="fab fa-youtube"></i>
        Ver Canal
      </a>
    </div>

    @php
      $videosGrid = $transmisionesRecientes ?? collect();
      $videoVivo  = $transmisionEnVivo ?? null;
      // Si hay stream en vivo, lo ponemos primero
      if ($videoVivo && $videosGrid->where('id', $videoVivo->id)->isEmpty()) {
          $videosGrid = $videosGrid->prepend($videoVivo);
      }
      // Fallback: embed playlist del canal
      $playlistEmbed = 'https://www.youtube.com/embed?listType=playlist&list=UUx8c9O9qP3IjtnEKkEr-Bng&rel=0';
    @endphp

    @if($videosGrid->count() > 0)
      <!-- Grid de videos: 1 principal grande + hasta 3 secundarios -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Video Principal (en vivo o el más reciente) --}}
        @php $principal = $videosGrid->first(); @endphp
        <div class="lg:col-span-2 group">
          <div class="relative rounded-2xl overflow-hidden shadow-2xl shadow-black/50 border border-white/10 bg-black">
            <!-- Etiqueta en vivo o reciente -->
            <div class="absolute top-3 left-3 z-10">
              @if($principal->en_vivo ?? false)
                <span class="inline-flex items-center gap-1 bg-red-600 text-white text-[10px] font-black uppercase px-2.5 py-1 rounded-full shadow animate-pulse border border-white/20">
                  <span class="w-1.5 h-1.5 rounded-full bg-white inline-block"></span> EN VIVO
                </span>
              @else
                <span class="inline-flex items-center gap-1 bg-black/60 backdrop-blur text-gray-200 text-[10px] font-semibold uppercase px-2.5 py-1 rounded-full border border-white/10">
                  <i class="fab fa-youtube text-red-500 text-xs"></i> UHTV
                </span>
              @endif
            </div>
            <!-- iframe principal -->
            <div class="relative pb-[56.25%] bg-black">
              <iframe
                class="absolute top-0 left-0 w-full h-full"
                src="{{ $principal->embed_url ?? $playlistEmbed }}"
                title="{{ $principal->titulo ?? 'UHTV Bolivia' }}"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                loading="lazy">
              </iframe>
            </div>
            <!-- Título del video principal -->
            <div class="p-4 bg-gradient-to-t from-gray-950 to-gray-900/80">
              <h3 class="text-white font-bold text-base leading-snug line-clamp-2 group-hover:text-red-400 transition-colors">
                {{ $principal->titulo ?? 'Transmisión UHTV Bolivia' }}
              </h3>
              @if(isset($principal->created_at))
                <p class="text-gray-400 text-xs mt-1 flex items-center gap-1">
                  <i class="fas fa-clock text-[10px]"></i>
                  {{ \Carbon\Carbon::parse($principal->created_at)->locale('es')->diffForHumans() }}
                </p>
              @endif
            </div>
          </div>
        </div>

        {{-- Videos secundarios (hasta 3) --}}
        <div class="flex flex-col gap-4">
          @foreach($videosGrid->skip(1)->take(3) as $video)
            <div class="group flex-1 min-h-0">
              <div class="relative rounded-xl overflow-hidden shadow-xl border border-white/10 bg-black h-full">
                @if($video->en_vivo ?? false)
                  <div class="absolute top-2 left-2 z-10">
                    <span class="inline-flex items-center gap-1 bg-red-600 text-white text-[9px] font-black uppercase px-2 py-0.5 rounded-full shadow animate-pulse">
                      <span class="w-1 h-1 rounded-full bg-white inline-block"></span> VIVO
                    </span>
                  </div>
                @endif
                <div class="relative pb-[56.25%] bg-black">
                  <iframe
                    class="absolute top-0 left-0 w-full h-full"
                    src="{{ $video->embed_url }}"
                    title="{{ $video->titulo ?? 'Video UHTV' }}"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy">
                  </iframe>
                </div>
                <div class="p-3 bg-gray-900/90">
                  <h4 class="text-white text-xs font-semibold line-clamp-2 group-hover:text-red-400 transition-colors leading-snug">
                    {{ $video->titulo ?? 'Video UHTV' }}
                  </h4>
                </div>
              </div>
            </div>
          @endforeach

          {{-- Si hay menos de 3 videos secundarios, mostrar el canal --}}
          @if($videosGrid->count() < 3)
            <div class="flex-1 rounded-xl overflow-hidden border border-white/10 bg-gray-900 flex flex-col items-center justify-center p-6 gap-3 min-h-[120px]">
              <i class="fab fa-youtube text-red-500 text-4xl"></i>
              <p class="text-gray-300 text-sm text-center font-medium">Más videos en nuestro canal de YouTube</p>
              <a href="https://www.youtube.com/@UHTVBolivia" target="_blank" rel="noopener noreferrer"
                 class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-5 py-2 rounded-full transition-all duration-300">
                <i class="fab fa-youtube mr-1"></i> Suscríbete
              </a>
            </div>
          @endif
        </div>

      </div>
    @else
      {{-- Fallback: solo embed del canal playlist --}}
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2">
          <div class="relative rounded-2xl overflow-hidden shadow-2xl border border-white/10 bg-black">
            <div class="relative pb-[56.25%] bg-black">
              <iframe
                class="absolute top-0 left-0 w-full h-full"
                src="{{ $playlistEmbed }}"
                title="UHTV Bolivia - Canal Oficial"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                loading="lazy">
              </iframe>
            </div>
            <div class="p-4 bg-gray-900">
              <h3 class="text-white font-bold text-base">Canal Oficial UHTV Bolivia</h3>
              <p class="text-gray-400 text-xs mt-1">Transmisiones en vivo, noticias y más</p>
            </div>
          </div>
        </div>
        <div class="flex flex-col items-center justify-center gap-4 bg-gray-900 rounded-2xl border border-white/10 p-8">
          <i class="fab fa-youtube text-red-500 text-6xl"></i>
          <div class="text-center">
            <h4 class="text-white font-bold text-lg">UHTV Bolivia</h4>
            <p class="text-gray-400 text-sm mt-1">Noticias · Transmisiones · Análisis</p>
          </div>
          <a href="https://www.youtube.com/@UHTVBolivia" target="_blank" rel="noopener noreferrer"
             class="bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-full transition-all duration-300 flex items-center gap-2 shadow-lg shadow-red-600/30">
            <i class="fab fa-youtube"></i> Ver Canal
          </a>
        </div>
      </div>
    @endif

  </div>
</section>

<!-- Sección de Noticias por Categorías - Estilo Brújula Digital -->
<section class="py-12 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">Noticias por Categorías</h2>
      <div class="w-32 h-1 bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] mx-auto rounded-full"></div>
      <p class="text-gray-600 dark:text-gray-300 mt-4 text-lg">Mantente informado con las últimas noti    <!-- Secciones de Categorías como Brújula Digital -->
    @foreach($categorias->take(4) as $categoria)
      @php
        $noticiasCat = isset($noticiasPorCategoria[$categoria->id]) ? $noticiasPorCategoria[$categoria->id]->take(5) : collect();
      @endphp
      @if($noticiasCat->count() > 0)
        @php
          $principal = $noticiasCat->first();
          $secundarias = $noticiasCat->slice(1, 4);
        @endphp
        <div class="mb-16">
          <!-- Header de la Categoría -->
          <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
              <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $categoria->name }}</h3>
              <div class="w-16 h-1 bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] rounded-full"></div>
            </div>
            <a href="{{ $categoria->url }}" 
               class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-semibold flex items-center transition-colors duration-300">
              Ver todas <i class="fas fa-arrow-right ml-2"></i>
            </a>
          </div>

          <!-- Composición Balanceada de 5 Noticias (Sin espacios en blanco) -->
          @if($secundarias->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
              <!-- Noticia Principal (Columna Izquierda: 5 cols en desktop) -->
              <div class="lg:col-span-5 flex">
                <a href="{{ $principal->url }}" class="block w-full group">
                  <article class="h-full bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col justify-between">
                    <div>
                      <!-- Imagen de la Noticia Principal -->
                      <div class="relative overflow-hidden">
                        <img src="{{ $principal->imagenUrl ?? asset('images/default-news.svg') }}" 
                             alt="{{ $principal->titulo }}" 
                             class="w-full h-64 sm:h-72 lg:h-76 object-cover transition-transform duration-500 group-hover:scale-105"
                             loading="lazy"
                             decoding="async"
                             onerror="handleImageError(this)">
                        
                        <!-- Etiqueta de categoría -->
                        <div class="absolute top-4 left-4">
                          <span class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide shadow-lg border border-white/20">
                            {{ $categoria->name }}
                          </span>
                        </div>
                        
                        <!-- Indicador de noticia principal -->
                        <div class="absolute top-4 right-4">
                          <span class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg border border-white/20">
                            PRINCIPAL
                          </span>
                        </div>
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                      </div>
                      
                      <!-- Contenido Principal (Sin botón Leer noticia completa) -->
                      <div class="p-5 sm:p-6 flex flex-col justify-between flex-grow">
                        <div class="mb-2.5">
                          <span class="text-gray-500 dark:text-gray-400 text-sm flex items-center">
                            <i class="fas fa-clock mr-2 text-purple-600"></i>
                            {{ \Carbon\Carbon::parse($principal->created_at)->locale('es')->diffForHumans() }}
                          </span>
                        </div>
                        
                        <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2.5 text-xl sm:text-2xl leading-tight line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                          {{ $principal->titulo }}
                        </h4>
                        
                        <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base line-clamp-3 leading-relaxed">
                          {{ $principal->excerptLimpio ?? Str::limit(strip_tags($principal->contenido), 170) }}
                        </p>
                      </div>
                    </div>
                  </article>
                </a>
              </div>

              <!-- 4 Noticias Secundarias (Columna Derecha: 7 cols en cuadrícula 2x2) -->
              <div class="lg:col-span-7">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 h-full">
                  @foreach($secundarias as $noticia)
                    <a href="{{ $noticia->url }}" class="block h-full group">
                      <article class="h-full bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col justify-between">
                        <div>
                          <!-- Imagen -->
                          <div class="relative overflow-hidden">
                            <img src="{{ $noticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                                 alt="{{ $noticia->titulo }}" 
                                 class="w-full h-36 sm:h-40 object-cover transition-transform duration-500 group-hover:scale-105"
                                 loading="lazy"
                                 decoding="async"
                                 onerror="handleImageError(this)">
                            
                            <div class="absolute top-2.5 left-2.5">
                              <span class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wide shadow border border-white/20">
                                {{ $categoria->name }}
                              </span>
                            </div>
                            
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                          </div>
                          
                          <!-- Contenido Secundario -->
                          <div class="p-3.5 sm:p-4">
                            <div class="mb-2">
                              <span class="text-gray-500 dark:text-gray-400 text-xs flex items-center">
                                <i class="fas fa-clock mr-1.5 text-purple-600 text-[10px]"></i>
                                {{ \Carbon\Carbon::parse($noticia->created_at)->locale('es')->diffForHumans() }}
                              </span>
                            </div>
                            
                            <h5 class="font-bold text-gray-900 dark:text-gray-100 text-sm sm:text-base leading-snug line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                              {{ $noticia->titulo }}
                            </h5>
                            
                            <p class="text-gray-600 dark:text-gray-300 text-xs line-clamp-2 leading-relaxed mt-1.5">
                              {{ $noticia->excerptLimpio ?? Str::limit(strip_tags($noticia->contenido), 90) }}
                            </p>
                          </div>
                        </div>
                      </article>
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          @else
            <!-- Fallback para categorías con solo 1 noticia -->
            <div class="max-w-xl">
              <a href="{{ $principal->url }}" class="block group">
                <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden">
                  <div class="relative overflow-hidden">
                    <img src="{{ $principal->imagenUrl ?? asset('images/default-news.svg') }}" 
                         alt="{{ $principal->titulo }}" 
                         class="w-full h-64 sm:h-72 object-cover transition-transform duration-500 group-hover:scale-105"
                         loading="lazy"
                         decoding="async"
                         onerror="handleImageError(this)">
                    <div class="absolute top-4 left-4">
                      <span class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide shadow-lg border border-white/20">
                        {{ $categoria->name }}
                      </span>
                    </div>
                  </div>
                  <div class="p-5 sm:p-6">
                    <div class="mb-3">
                      <span class="text-gray-500 dark:text-gray-400 text-sm flex items-center">
                        <i class="fas fa-clock mr-2 text-purple-600"></i>
                        {{ \Carbon\Carbon::parse($principal->created_at)->locale('es')->diffForHumans() }}
                      </span>
                    </div>
                    <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-2.5 text-xl sm:text-2xl leading-tight group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                      {{ $principal->titulo }}
                    </h4>
                    <p class="text-gray-600 dark:text-gray-300 text-sm sm:text-base line-clamp-3 leading-relaxed">
                      {{ $principal->excerptLimpio ?? Str::limit(strip_tags($principal->contenido), 180) }}
                    </p>
                  </div>
                </article>
              </a>
            </div>
          @endif
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
              <a href="{{ $categoria->url }}" 
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

<!-- Banner Publicitario Portada Medio -->
@if(isset($banners['portada_middle']) && $banners['portada_middle']->count() > 0)
    <section class="py-6 bg-gray-50 dark:bg-gray-850 transition-colors duration-300">
        <div class="container mx-auto px-4">
            <div class="text-center mb-2">
                <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-wider">Publicidad</span>
            </div>
            @foreach($banners['portada_middle'] as $banner)
                <div class="flex justify-center mb-4">
                    <a href="{{ $banner->link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="block max-w-5xl w-full group">
                        <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" 
                             class="w-full h-auto rounded-2xl shadow-lg hover:opacity-95 transition-opacity duration-300 border border-gray-100 dark:border-gray-700" 
                             loading="lazy"
                             decoding="async">
                    </a>
                </div>
            @endforeach
        </div>
    </section>
@endif

<!-- Sección de Últimas Noticias - Diseño Moderno -->
<section class="py-12 bg-white dark:bg-gray-900 transition-colors duration-300">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">Últimas Noticias</h2>
      <div class="w-32 h-1 bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] mx-auto rounded-full"></div>
      <p class="text-gray-600 dark:text-gray-300 mt-4 text-lg">Las noticias más recientes e importantes del momento</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      @foreach($ultimasNoticias->take(6) as $noticia)
        <a href="{{ $noticia->url }}" class="block">
          <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
            <div class="relative overflow-hidden">
              <img src="{{ $noticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                   alt="{{ $noticia->titulo }}" 
                   class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110"
                   loading="lazy"
                   decoding="async"
                   onerror="handleImageError(this)">
              
              <!-- Etiqueta de Categoría -->
              <div class="absolute top-4 left-4">
                <span class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wide shadow-lg border border-white/20">
                  {{ $noticia->category->name ?? 'General' }}
                </span>
              </div>
              
              <!-- Indicador de "Nuevo" para noticias recientes -->
              @if(\Carbon\Carbon::parse($noticia->created_at)->diffInHours() < 6)
                <div class="absolute top-4 right-4">
                  <span class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white px-2.5 py-1 rounded-full text-xs font-bold animate-pulse shadow-md border border-white/20">
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
             loading="lazy"
             decoding="async">
      </a>
    </div>
  </div>
</section>


{{-- Widget de Elfsight habilitado --}}
<script src="https://static.elfsight.com/platform/platform.js" async></script>
<div class="elfsight-app-fbb50d0e-c779-44ab-bf7f-b16fd3542ccc" data-elfsight-app-lazy></div>



@endsection
