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
@if($ultimasNoticias->isNotEmpty())
  <section id="latest-news-ticker"
           class="latest-news-ticker bg-uhtv-purple-700 dark:bg-uhtv-purple-900 text-white py-2 overflow-hidden border-y border-uhtv-purple-500 dark:border-uhtv-purple-800 relative shadow-md z-10"
           aria-label="Últimas noticias">
    <div class="container mx-auto px-4 flex items-center gap-3">
      <div class="bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] text-white text-xs font-bold uppercase px-3.5 py-1.5 rounded-full flex-shrink-0 shadow-sm z-20 relative border border-white/20">
        <span class="inline-flex items-center gap-2">
          <i class="fas fa-circle text-[8px]" aria-hidden="true"></i>
          Último Momento
        </span>
      </div>

      <div id="latest-news-ticker-viewport"
           class="latest-news-ticker__viewport flex-1 overflow-hidden h-7"
           aria-label="Noticias recientes en movimiento">
        <div id="latest-news-ticker-track" class="latest-news-ticker__track">
          <div class="latest-news-ticker__group">
            @foreach($ultimasNoticias->take(10) as $noticia)
              <span class="latest-news-ticker__item inline-flex items-center text-sm font-medium">
                <a href="{{ $noticia->url }}" class="flex items-center hover:text-uhtv-purple-200 transition-colors focus:outline-none focus:underline">
                  <span class="text-uhtv-purple-300 mr-2">[{{ $noticia->created_at->format('H:i') }}]</span>
                  <span>{{ $noticia->titulo }}</span>
                </a>
                <span class="text-uhtv-purple-400 mx-3" aria-hidden="true">•</span>
              </span>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </section>
@endif

<style>
  .latest-news-ticker__viewport {
    min-width: 0;
    white-space: nowrap;
    scrollbar-width: none;
  }

  .latest-news-ticker__viewport::-webkit-scrollbar {
    display: none;
  }

  .latest-news-ticker__track {
    display: flex;
    align-items: center;
    width: max-content;
    min-width: 100%;
    will-change: transform;
  }

  .latest-news-ticker__track.is-ready {
    animation: latest-news-ticker var(--latest-news-duration, 60s) linear infinite;
  }

  .latest-news-ticker__group {
    display: flex;
    align-items: center;
    flex: 0 0 auto;
  }

  .latest-news-ticker__item {
    display: inline-flex;
    align-items: center;
  }

  .latest-news-ticker__track:hover,
  .latest-news-ticker__track:focus-within {
    animation-play-state: paused;
  }

  @keyframes latest-news-ticker {
    from { transform: translate3d(0, 0, 0); }
    to { transform: translate3d(-50%, 0, 0); }
  }

  @media (prefers-reduced-motion: reduce) {
    .latest-news-ticker__viewport {
      overflow-x: auto;
    }

    .latest-news-ticker__track.is-ready {
      animation: none;
    }
  }

  /* Indicadores del carrusel con degradé celeste a lila */
  .carousel-indicators button.active {
    background: linear-gradient(90deg, #0099ff, #9333ea) !important;
    border: 1px solid rgba(255, 255, 255, 0.4);
    box-shadow: 0 0 10px rgba(0, 153, 255, 0.5);
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewport = document.getElementById('latest-news-ticker-viewport');
    const track = document.getElementById('latest-news-ticker-track');
    if (!viewport || !track) return;

    const sourceGroup = track.querySelector('.latest-news-ticker__group');
    if (!sourceGroup) return;

    // Duplica grupos hasta que el bucle sea más ancho que dos pantallas.
    const minimumWidth = Math.max(viewport.clientWidth * 2, 800);
    let copies = 1;
    let safety = 0;

    while (track.scrollWidth < minimumWidth && safety < 12) {
        const clone = sourceGroup.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        clone.querySelectorAll('a').forEach(link => link.setAttribute('tabindex', '-1'));
        track.appendChild(clone);
        copies += 1;
        safety += 1;
    }

    // Una cantidad par hace que TranslateX(-50%) cierre el bucle sin salto.
    if (copies % 2 !== 0) {
        const clone = sourceGroup.cloneNode(true);
        clone.setAttribute('aria-hidden', 'true');
        clone.querySelectorAll('a').forEach(link => link.setAttribute('tabindex', '-1'));
        track.appendChild(clone);
    }

    const pixelsPerSecond = 28;
    const duration = Math.max(35, Math.min(90, track.scrollWidth / pixelsPerSecond));
    track.style.setProperty('--latest-news-duration', `${duration}s`);
    track.classList.add('is-ready');
});
</script>

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
     SECCIÓN VIDEOS UHTV - Cinema Showcase Interactivo y Optimizado
================================================================ -->
@php
    $channelUrl = 'https://www.youtube.com/@UHTVBolivia';
    $channelPlaylistEmbed = 'https://www.youtube-nocookie.com/embed?listType=playlist&list=UUx8c9O9qP3IjtnEKkEr-Bng';
    
    // Obtener videos grabados (excluyendo emisiones en vivo que van al botón superior)
    $uhtvVideos = ($transmisionesRecientes ?? collect())
        ->filter(fn($v) => !($v->en_vivo ?? false))
        ->values();

    $featuredVideo = $uhtvVideos->first();

    $initialEmbed = $featuredVideo ? $featuredVideo->embed_url : $channelPlaylistEmbed;
    $initialTitle = $featuredVideo ? $featuredVideo->titulo : 'Canal Oficial UHTV Bolivia en YouTube';
    $initialThumb = $featuredVideo ? $featuredVideo->effective_thumbnail : asset('images/Logo.jpg');
    $initialType = $featuredVideo ? $featuredVideo->tipo_nombre : 'Canal Oficial';
    $initialDuration = $featuredVideo ? $featuredVideo->duracion : null;
    $initialDate = $featuredVideo 
        ? ($featuredVideo->fecha_transmision ? $featuredVideo->fecha_transmision->locale('es')->diffForHumans() : ($featuredVideo->created_at ? $featuredVideo->created_at->locale('es')->diffForHumans() : ''))
        : 'Actualizado recientemente';
    $initialUrl = $featuredVideo ? ($featuredVideo->url ?: $channelUrl) : $channelUrl;
@endphp

<section id="seccion-videos-uhtv" class="py-12 bg-gradient-to-b from-gray-950 via-gray-900 to-black border-y border-red-900/30 text-white transition-colors duration-300 relative overflow-hidden">
  <!-- Glow decorativo de fondo -->
  <div class="absolute -top-24 -left-24 w-96 h-96 bg-red-600/10 rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

  <div class="container mx-auto px-4 relative z-10">

    <!-- Header de la sección -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-4 border-b border-white/10">
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-11 h-11 rounded-2xl bg-gradient-to-tr from-red-600 to-rose-500 shadow-lg shadow-red-600/30 text-white flex-shrink-0">
          <i class="fab fa-youtube text-xl"></i>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h2 class="text-2xl font-black text-white leading-none tracking-tight">
              Videos <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-rose-400">UHTV</span>
            </h2>
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-red-600/20 text-red-400 border border-red-500/30">
              YouTube Oficial
            </span>
          </div>
          <p class="text-xs text-gray-400 mt-1">Reportajes, entrevistas, programas completos y resúmenes</p>
        </div>
      </div>

      <div class="flex items-center gap-2.5">
        <a href="https://www.youtube.com/@UHTVBolivia?sub_confirmation=1" 
           target="_blank" 
           rel="noopener noreferrer"
           class="inline-flex items-center gap-2 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-500 hover:to-rose-500 text-white text-xs font-extrabold px-4 py-2.5 rounded-xl shadow-lg shadow-red-600/30 transition-all duration-300 transform hover:scale-105"
           title="Suscribirse al canal oficial de YouTube">
          <i class="fab fa-youtube text-sm"></i>
          <span>Suscribirme</span>
        </a>

        <a href="{{ route('transmisiones.en-vivo') }}" 
           class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/15 text-gray-200 text-xs font-bold px-3.5 py-2.5 rounded-xl border border-white/10 transition-colors">
          <span>UHTV Play</span>
          <i class="fas fa-arrow-right text-[10px]"></i>
        </a>
      </div>
    </div>

    <!-- Contenedor Principal: Cinema Player + Playlist Lateral -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

      <!-- Columna Izquierda: Reproductor Cinema Principal (8 cols) -->
      <div class="lg:col-span-8 flex flex-col gap-4">
        <div id="uhtv-cinema-container" 
             class="relative w-full pb-[56.25%] rounded-2xl overflow-hidden shadow-2xl bg-black border border-white/10 group">
          
          <!-- Facade Inicial Lácteo/Optimizado (Carga 0 KB de iframe hasta interactuar) -->
          <div id="uhtv-cinema-facade" 
               class="absolute inset-0 z-10 cursor-pointer flex items-center justify-center bg-black transition-opacity duration-300"
               onclick="playCinemaFacadeVideo()">
            
            <img id="uhtv-cinema-thumb" 
                 src="{{ $initialThumb }}" 
                 alt="{{ $initialTitle }}" 
                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                 loading="lazy"
                 onerror="this.onerror=null;this.src='{{ asset('images/Logo.jpg') }}';">
            
            <!-- Gradiente de sombra cinematográfico -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-black/40"></div>
            
            <!-- Botón Central de Play Estilo YouTube -->
            <div class="relative z-20 w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-red-600/95 hover:bg-red-600 text-white flex items-center justify-center shadow-2xl shadow-red-600/60 transform group-hover:scale-110 transition-all duration-300 border-2 border-white/40">
              <i class="fas fa-play text-xl sm:text-2xl ml-1 text-white"></i>
            </div>

            <!-- Badge Tipo de Contenido -->
            <span id="uhtv-cinema-type-badge" 
                  class="absolute top-4 left-4 z-20 px-3 py-1 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-black/80 backdrop-blur-md text-white border border-white/20 shadow-md">
              {{ $initialType }}
            </span>

            <!-- Badge Duración -->
            <span id="uhtv-cinema-duration-badge" 
                  class="absolute bottom-4 right-4 z-20 px-2.5 py-1 rounded-lg text-xs font-bold bg-black/80 text-white backdrop-blur-md border border-white/10 {{ empty($initialDuration) ? 'hidden' : '' }}">
              {{ $initialDuration }}
            </span>
          </div>

          <!-- Iframe embebido (Se activa al hacer clic o al seleccionar video de la lista) -->
          <iframe id="uhtv-cinema-iframe"
                  class="absolute inset-0 w-full h-full border-0 hidden z-20"
                  src=""
                  data-default-src="{{ $initialEmbed }}"
                  title="{{ $initialTitle }}"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                  allowfullscreen>
          </iframe>
        </div>

        <!-- Barra de Metadatos y Acciones del Video Seleccionado -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gray-900/80 border border-white/10 backdrop-blur-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="min-w-0 flex-1">
            <h3 id="uhtv-cinema-title" class="text-base sm:text-lg font-bold text-white line-clamp-2 leading-snug">
              {{ $initialTitle }}
            </h3>
            <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-400">
              <span id="uhtv-cinema-date" class="flex items-center gap-1.5">
                <i class="far fa-clock text-[11px] text-red-500"></i>
                <span>{{ $initialDate }}</span>
              </span>
              <span class="text-gray-600">·</span>
              <span class="text-gray-300 flex items-center gap-1">
                <i class="fab fa-youtube text-red-500"></i>
                <span>Última Hora TV</span>
              </span>
            </div>
          </div>

          <div class="flex items-center gap-2 flex-shrink-0">
            <a id="uhtv-cinema-external-link" 
               href="{{ $initialUrl }}" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 hover:text-white text-xs font-semibold border border-white/10 transition-colors">
              <i class="fab fa-youtube text-red-500"></i>
              <span>Abrir en YouTube</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Columna Derecha: Playlist Interactiva (4 cols) -->
      <div class="lg:col-span-4 flex flex-col gap-3">
        <div class="rounded-2xl bg-gray-900/80 border border-white/10 p-4 backdrop-blur-md">
          <div class="flex items-center justify-between mb-3 pb-3 border-b border-white/10">
            <div class="flex items-center gap-2">
              <i class="fas fa-list-ul text-red-500 text-xs"></i>
              <h4 class="text-xs font-extrabold uppercase tracking-wider text-gray-200">Videos Disponibles</h4>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/10 text-gray-300">
              {{ $uhtvVideos->count() > 0 ? $uhtvVideos->count() : 'Canal' }}
            </span>
          </div>

          <!-- Lista de videos interactiva -->
          <div class="flex flex-col gap-2.5 max-h-[380px] overflow-y-auto pr-1" style="scrollbar-width: thin; scrollbar-color: #ef4444 transparent;">
            @forelse($uhtvVideos as $index => $item)
              @php
                $itemDate = $item->fecha_transmision 
                    ? $item->fecha_transmision->locale('es')->diffForHumans() 
                    : ($item->created_at ? $item->created_at->locale('es')->diffForHumans() : '');
                $itemBadgeColor = match($item->tipo) {
                    'programa' => 'bg-blue-600',
                    'podcast' => 'bg-purple-600',
                    'clip' => 'bg-amber-500 text-black',
                    default => 'bg-red-600',
                };
              @endphp
              <button type="button" 
                      class="uhtv-playlist-item w-full text-left p-2.5 rounded-xl border transition-all duration-200 flex gap-3 items-center group cursor-pointer {{ $index === 0 ? 'bg-white/10 border-red-500/50 shadow-md' : 'bg-black/30 border-white/5 hover:bg-white/5 hover:border-white/20' }}"
                      data-embed-url="{{ $item->embed_url }}"
                      data-title="{{ $item->titulo }}"
                      data-thumb="{{ $item->effective_thumbnail }}"
                      data-type="{{ $item->tipo_nombre }}"
                      data-duration="{{ $item->duracion ?? '' }}"
                      data-date="{{ $itemDate }}"
                      data-url="{{ $item->url ?: $channelUrl }}"
                      onclick="selectCinemaVideo(this)">
                
                <div class="relative w-24 h-14 rounded-lg overflow-hidden flex-shrink-0 bg-black">
                  <img src="{{ $item->effective_thumbnail }}" 
                       alt="{{ $item->titulo }}" 
                       class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                       loading="lazy"
                       onerror="this.onerror=null;this.src='{{ asset('images/Logo.jpg') }}';">
                  <div class="absolute inset-0 bg-black/40 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                    <i class="fas fa-play text-white/90 text-xs transform group-hover:scale-110 transition-transform"></i>
                  </div>
                  @if($item->duracion)
                    <span class="absolute bottom-1 right-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-black/85 text-white">
                      {{ $item->duracion }}
                    </span>
                  @endif
                </div>

                <div class="flex-1 min-w-0">
                  <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase {{ $itemBadgeColor }} mb-1">
                    {{ $item->tipo_nombre }}
                  </span>
                  <h5 class="text-xs font-bold text-gray-200 group-hover:text-white line-clamp-2 leading-snug">
                    {{ $item->titulo }}
                  </h5>
                  @if($itemDate)
                    <p class="text-[10px] text-gray-500 mt-0.5">{{ $itemDate }}</p>
                  @endif
                </div>
              </button>
            @empty
              <!-- Estado si no hay videos registrados en BD: acceso directo al playlist del canal -->
              <div class="p-4 rounded-xl bg-black/40 border border-white/5 text-center">
                <i class="fab fa-youtube text-red-500 text-3xl mb-2"></i>
                <p class="text-xs text-gray-300 font-semibold mb-1">Playlist Oficial de YouTube</p>
                <p class="text-[11px] text-gray-500 mb-3">Reproduce las emisiones y videos más recientes directamente desde nuestro canal.</p>
                <button type="button" 
                        class="px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-all shadow"
                        onclick="playCinemaFacadeVideo()">
                  <i class="fas fa-play me-1"></i> Reproducir Playlist
                </button>
              </div>
            @endforelse
          </div>

          <!-- Banner inferior de suscripción al canal -->
          <div class="mt-3 pt-3 border-t border-white/10 flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
              <i class="fab fa-youtube text-red-500 text-lg flex-shrink-0"></i>
              <div class="min-w-0">
                <p class="text-[11px] font-bold text-white truncate">@UHTVBolivia</p>
                <p class="text-[10px] text-gray-400 truncate">Transmisiones y reportajes diarios</p>
              </div>
            </div>
            <a href="https://www.youtube.com/@UHTVBolivia?sub_confirmation=1" 
               target="_blank" 
               rel="noopener noreferrer" 
               class="px-2.5 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold transition-colors flex-shrink-0 flex items-center gap-1">
              <i class="fas fa-bell text-[10px]"></i>
              <span>Unirse</span>
            </a>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

@push('scripts')
<script>
function playCinemaFacadeVideo() {
    const iframe = document.getElementById('uhtv-cinema-iframe');
    const facade = document.getElementById('uhtv-cinema-facade');
    if (!iframe || !facade) return;

    let src = iframe.getAttribute('data-default-src') || iframe.src;
    if (!src.includes('autoplay=')) {
        src += (src.includes('?') ? '&' : '?') + 'autoplay=1';
    }
    iframe.src = autoPlayParams(src);
    iframe.classList.remove('hidden');
    facade.classList.add('hidden');
}

function autoPlayParams(url) {
    if (!url) return '';
    return url.includes('autoplay=') ? url : url + (url.includes('?') ? '&' : '?') + 'autoplay=1';
}

function selectCinemaVideo(button) {
    const embedUrl = button.getAttribute('data-embed-url');
    const title = button.getAttribute('data-title');
    const thumb = button.getAttribute('data-thumb');
    const type = button.getAttribute('data-type');
    const duration = button.getAttribute('data-duration');
    const date = button.getAttribute('data-date');
    const url = button.getAttribute('data-url');

    const iframe = document.getElementById('uhtv-cinema-iframe');
    const facade = document.getElementById('uhtv-cinema-facade');
    const titleEl = document.getElementById('uhtv-cinema-title');
    const dateEl = document.getElementById('uhtv-cinema-date');
    const typeBadge = document.getElementById('uhtv-cinema-type-badge');
    const durationBadge = document.getElementById('uhtv-cinema-duration-badge');
    const thumbImg = document.getElementById('uhtv-cinema-thumb');
    const extLink = document.getElementById('uhtv-cinema-external-link');

    // Actualizar datos del video
    if (titleEl) titleEl.textContent = title;
    if (dateEl) dateEl.innerHTML = `<i class="far fa-clock text-[11px] text-red-500"></i> <span>${date || 'Reciente'}</span>`;
    if (typeBadge && type) typeBadge.textContent = type;
    if (durationBadge) {
        if (duration) {
            durationBadge.textContent = duration;
            durationBadge.classList.remove('hidden');
        } else {
            durationBadge.classList.add('hidden');
        }
    }
    if (thumbImg && thumb) thumbImg.src = thumb;
    if (extLink && url) extLink.href = url;

    // Cargar iframe con reproducción automática
    if (iframe) {
        iframe.src = autoPlayParams(embedUrl);
        iframe.classList.remove('hidden');
    }
    if (facade) {
        facade.classList.add('hidden');
    }

    // Actualizar estilo activo en la lista
    document.querySelectorAll('.uhtv-playlist-item').forEach(item => {
        item.classList.remove('bg-white/10', 'border-red-500/50', 'shadow-md');
        item.classList.add('bg-black/30', 'border-white/5');
    });
    button.classList.remove('bg-black/30', 'border-white/5');
    button.classList.add('bg-white/10', 'border-red-500/50', 'shadow-md');
}
</script>
@endpush

<!-- Sección de Noticias por Categorías - Estilo Brújula Digital -->
<section class="py-12 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
  <div class="container mx-auto px-4">
    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">Noticias por Categorías</h2>
      <div class="w-32 h-1 bg-gradient-to-r from-[#0099ff] via-[#4f46e5] to-[#9333ea] mx-auto rounded-full"></div>
      <p class="text-gray-600 dark:text-gray-300 mt-4 text-lg">Mantente informado con las últimas noti    <!-- Secciones de Categorías como Brújula Digital -->
    @foreach($seccionesCategoria->take(4) as $categoria)
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
