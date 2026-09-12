@extends('layouts.main')

@section('title', 'En Vivo, Podcasts y Videos - UHTV Play | Última Hora TV')

@section('content')
<div class="bg-gray-950 text-white min-h-screen pb-16">
    <!-- Cinema Hero Section: Reproductor Principal -->
    <section class="relative bg-gradient-to-b from-black via-gray-900 to-gray-950 pt-6 pb-10 border-b border-gray-800">
        <div class="container mx-auto px-4 max-w-6xl">
            <!-- Header de Sección -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full bg-red-600/20 border border-red-500/30 flex items-center justify-center text-red-500">
                        <i class="fas fa-satellite-dish text-lg {{ ($activeStream && $activeStream->en_vivo) ? 'animate-pulse' : '' }}"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight flex items-center gap-3">
                            <span>UHTV <span class="text-red-500">Play</span></span>
                            @if($activeStream && $activeStream->en_vivo)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-red-600 text-white shadow-lg animate-pulse">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    En Vivo Ahora
                                </span>
                            @endif
                        </h1>
                        <p class="text-xs sm:text-sm text-gray-400">Canal digital de transmisiones, podcasts y clips informativos en directo</p>
                    </div>
                </div>

                <!-- Botón de Abrir en Modal Rápido -->
                <div class="flex items-center gap-2">
                    <button type="button" 
                            data-open-live-modal 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all backdrop-blur-sm border border-white/10">
                        <i class="fas fa-window-restore"></i>
                        <span>Modo Flotante / Modal</span>
                    </button>
                </div>
            </div>

            <!-- Cinema Video Player Container -->
            <div class="relative bg-black rounded-2xl overflow-hidden shadow-2xl border border-white/10" style="padding-top: 56.25%;">
                @if($activeStream && !empty($activeStream->embed_url))
                    <iframe id="cinemaPlayerIframe" 
                            class="absolute inset-0 w-full h-full border-0" 
                            src="{{ $activeStream->embed_url }}" 
                            title="{{ $activeStream->titulo }}" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                            allowfullscreen>
                    </iframe>
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-gray-400 bg-gray-900">
                        <i class="fas fa-tv fa-4x mb-3 text-gray-600"></i>
                        <h3 class="text-xl font-bold text-white mb-2">No hay transmisiones disponibles</h3>
                        <p class="text-sm max-w-md text-gray-400 mb-4">Estamos preparando nuestra próxima emisión en vivo. Vuelve pronto o explora nuestras noticias.</p>
                        <a href="/" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                            Ir a Portada de Noticias
                        </a>
                    </div>
                @endif
            </div>

            <!-- Metadatos y Barra del Video Activo -->
            @if($activeStream)
                <div class="mt-5 p-5 bg-gray-900/80 rounded-2xl border border-white/5 backdrop-blur-sm">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span id="cinemaPlatformBadge" 
                                      class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold text-white uppercase tracking-wider" 
                                      style="background-color: {{ $activeStream->plataforma_color }};">
                                    <i id="cinemaPlatformIcon" class="{{ $activeStream->plataforma_icon }}"></i>
                                    <span id="cinemaPlatformText">{{ $activeStream->plataforma_nombre }}</span>
                                </span>

                                <span id="cinemaTypeBadge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-white/10 text-gray-200">
                                    {{ $activeStream->tipo_nombre }}
                                </span>

                                @if($activeStream->duracion)
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-400 bg-black/40 px-2.5 py-1 rounded-full">
                                        <i class="far fa-clock text-[11px]"></i> {{ $activeStream->duracion }}
                                    </span>
                                @endif

                                <span class="text-xs text-gray-400">
                                    <i class="far fa-calendar-alt text-[11px] me-1"></i>
                                    {{ $activeStream->fecha_transmision ? $activeStream->fecha_transmision->isoFormat('D [de] MMMM, YYYY') : $activeStream->created_at->isoFormat('D [de] MMMM, YYYY') }}
                                </span>
                            </div>

                            <h2 class="text-lg sm:text-2xl font-bold text-white tracking-tight leading-snug mb-2" id="cinemaTitle">
                                {{ $activeStream->titulo }}
                            </h2>

                            @if($activeStream->descripcion)
                                <p class="text-sm text-gray-300 leading-relaxed max-w-3xl" id="cinemaDescription">
                                    {{ $activeStream->descripcion }}
                                </p>
                            @endif
                        </div>

                        <!-- Botones de Compartir -->
                        <div class="flex flex-wrap items-center gap-2 lg:flex-col lg:items-end">
                            <div class="flex items-center space-x-2">
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($activeStream->titulo . ' ' . url()->current()) }}" 
                                   target="_blank" 
                                   class="w-9 h-9 rounded-full bg-green-600 hover:bg-green-700 text-white flex items-center justify-center transition-transform hover:scale-105" 
                                   title="Compartir en WhatsApp">
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                                   target="_blank" 
                                   class="w-9 h-9 rounded-full bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-transform hover:scale-105" 
                                   title="Compartir en Facebook">
                                    <i class="fab fa-facebook-f text-sm"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($activeStream->titulo) }}&url={{ urlencode(url()->current()) }}" 
                                   target="_blank" 
                                   class="w-9 h-9 rounded-full bg-sky-500 hover:bg-sky-600 text-white flex items-center justify-center transition-transform hover:scale-105" 
                                   title="Compartir en X">
                                    <i class="fab fa-twitter text-sm"></i>
                                </a>
                                <button type="button" 
                                        id="btnCopyCinemaLink" 
                                        class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold flex items-center gap-1.5 transition-all">
                                    <i class="fas fa-link"></i>
                                    <span id="copyLinkText">Copiar Enlace</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Galería y Filtros -->
    <section class="container mx-auto px-4 max-w-6xl mt-8">
        <!-- Filtros por Tipo -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-4 border-b border-gray-800">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('transmisiones.en-vivo', array_merge(request()->query(), ['tipo' => 'todos'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($selectedTipo == 'todos') ? 'bg-gradient-to-r from-red-600 to-purple-600 text-white shadow-lg' : 'bg-gray-800/80 text-gray-300 hover:bg-gray-700' }}">
                    <i class="fas fa-layer-group me-1.5"></i> Todos los Videos
                </a>
                <a href="{{ route('transmisiones.en-vivo', array_merge(request()->query(), ['tipo' => 'en_vivo'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($selectedTipo == 'en_vivo') ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-800/80 text-gray-300 hover:bg-gray-700' }}">
                    <i class="fas fa-broadcast-tower me-1.5"></i> 🔴 En Vivo
                </a>
                <a href="{{ route('transmisiones.en-vivo', array_merge(request()->query(), ['tipo' => 'podcast'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($selectedTipo == 'podcast') ? 'bg-purple-600 text-white shadow-lg' : 'bg-gray-800/80 text-gray-300 hover:bg-gray-700' }}">
                    <i class="fas fa-podcast me-1.5"></i> 🎙️ Podcasts
                </a>
                <a href="{{ route('transmisiones.en-vivo', array_merge(request()->query(), ['tipo' => 'clip'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ ($selectedTipo == 'clip') ? 'bg-amber-500 text-black shadow-lg' : 'bg-gray-800/80 text-gray-300 hover:bg-gray-700' }}">
                    <i class="fas fa-bolt me-1.5"></i> ⚡ Clips
                </a>
            </div>

            <!-- Filtros por Plataforma -->
            <div class="flex items-center gap-1.5 bg-gray-900 p-1 rounded-xl border border-gray-800">
                <span class="text-[11px] text-gray-400 font-semibold px-2 uppercase tracking-wider">Plataforma:</span>
                <a href="{{ route('transmisiones.en-vivo', array_merge(request()->query(), ['plataforma' => 'todas'])) }}" 
                   class="px-2.5 py-1 rounded-lg text-xs font-semibold {{ ($selectedPlataforma == 'todas') ? 'bg-white/20 text-white' : 'text-gray-400 hover:text-white' }}">
                    Todas
                </a>
                <a href="{{ route('transmisiones.en-vivo', array_merge(request()->query(), ['plataforma' => 'youtube'])) }}" 
                   class="px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1 {{ ($selectedPlataforma == 'youtube') ? 'bg-red-600 text-white' : 'text-gray-400 hover:text-red-400' }}">
                    <i class="fab fa-youtube"></i> YouTube
                </a>
                <a href="{{ route('transmisiones.en-vivo', array_merge(request()->query(), ['plataforma' => 'facebook'])) }}" 
                   class="px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1 {{ ($selectedPlataforma == 'facebook') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:text-blue-400' }}">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a href="{{ route('transmisiones.en-vivo', array_merge(request()->query(), ['plataforma' => 'tiktok'])) }}" 
                   class="px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1 {{ ($selectedPlataforma == 'tiktok') ? 'bg-black text-white border border-gray-700' : 'text-gray-400 hover:text-white' }}">
                    <i class="fab fa-tiktok"></i> TikTok
                </a>
            </div>
        </div>

        <!-- Grilla de Videos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @forelse($transmisiones as $item)
                <div class="group bg-gray-900/90 rounded-2xl overflow-hidden border border-white/5 hover:border-purple-500/40 shadow-lg hover:shadow-2xl transition-all duration-300 flex flex-col">
                    <!-- Thumbnail Container -->
                    <div class="relative w-full pb-[56.25%] bg-black overflow-hidden cursor-pointer play-in-cinema-btn"
                         data-embed="{{ $item->embed_url }}"
                         data-title="{{ $item->titulo }}"
                         data-description="{{ $item->descripcion }}"
                         data-platform="{{ $item->plataforma_nombre }}"
                         data-platform-color="{{ $item->plataforma_color }}"
                         data-platform-icon="{{ $item->plataforma_icon }}"
                         data-type="{{ $item->tipo_nombre }}">
                        
                        <img src="{{ $item->effective_thumbnail }}" 
                             alt="{{ $item->titulo }}" 
                             class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                             loading="lazy"
                             decoding="async"
                             onerror="this.src='/images/Logo.jpg'">

                        <!-- Overlay Oscuro al Hover -->
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-red-600 text-white flex items-center justify-center transform scale-75 group-hover:scale-100 transition-transform shadow-lg">
                                <i class="fas fa-play text-sm ml-0.5"></i>
                            </div>
                        </div>

                        <!-- Badge En Vivo -->
                        @if($item->en_vivo)
                            <span class="absolute top-2.5 left-2.5 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-red-600 text-white shadow-lg animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                En Vivo
                            </span>
                        @endif

                        <!-- Badge Plataforma -->
                        <span class="absolute bottom-2.5 left-2.5 inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold text-white shadow" style="background-color: {{ $item->plataforma_color }};">
                            <i class="{{ $item->plataforma_icon }}"></i>
                            <span>{{ $item->plataforma_nombre }}</span>
                        </span>

                        <!-- Duración -->
                        @if($item->duracion)
                            <span class="absolute bottom-2.5 right-2.5 bg-black/80 text-white text-[10px] font-semibold px-2 py-0.5 rounded backdrop-blur-sm">
                                {{ $item->duracion }}
                            </span>
                        @endif
                    </div>

                    <!-- Contenido de la Tarjeta -->
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-[10px] uppercase font-extrabold tracking-wider text-purple-400">
                                    {{ $item->tipo_nombre }}
                                </span>
                                <span class="text-[11px] text-gray-400">
                                    {{ $item->fecha_transmision ? $item->fecha_transmision->diffForHumans() : $item->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-white group-hover:text-purple-400 transition-colors line-clamp-2 leading-snug mb-1.5 cursor-pointer play-in-cinema-btn"
                                data-embed="{{ $item->embed_url }}"
                                data-title="{{ $item->titulo }}"
                                data-description="{{ $item->descripcion }}"
                                data-platform="{{ $item->plataforma_nombre }}"
                                data-platform-color="{{ $item->plataforma_color }}"
                                data-platform-icon="{{ $item->plataforma_icon }}"
                                data-type="{{ $item->tipo_nombre }}">
                                {{ $item->titulo }}
                            </h3>

                            @if($item->descripcion)
                                <p class="text-xs text-gray-400 line-clamp-2 mb-3">
                                    {{ $item->descripcion }}
                                </p>
                            @endif
                        </div>

                        <!-- Footer de la Tarjeta -->
                        <div class="pt-3 border-t border-white/5 flex items-center justify-between text-xs text-gray-400">
                            <span><i class="far fa-eye me-1"></i>{{ number_format($item->views) }} vistas</span>
                            <button type="button" 
                                    class="text-purple-400 hover:text-purple-300 font-bold text-xs flex items-center gap-1 play-in-cinema-btn"
                                    data-embed="{{ $item->embed_url }}"
                                    data-title="{{ $item->titulo }}"
                                    data-description="{{ $item->descripcion }}"
                                    data-platform="{{ $item->plataforma_nombre }}"
                                    data-platform-color="{{ $item->plataforma_color }}"
                                    data-platform-icon="{{ $item->plataforma_icon }}"
                                    data-type="{{ $item->tipo_nombre }}">
                                <span>Reproducir</span>
                                <i class="fas fa-play text-[9px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-gray-500">
                    <i class="fas fa-film fa-3x mb-3 text-gray-600"></i>
                    <h4 class="text-base font-bold text-white mb-1">No se encontraron videos con los filtros seleccionados</h4>
                    <p class="text-xs text-gray-400 mb-4">Intenta seleccionando otra categoría o plataforma.</p>
                    <a href="{{ route('transmisiones.en-vivo') }}" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-xl text-xs font-semibold">
                        Ver Todos los Videos
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($transmisiones->hasPages())
            <div class="mt-8">
                {{ $transmisiones->links() }}
            </div>
        @endif
    </section>
</div>

<!-- Script para el reproductor interactivo en cinema mode -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cinemaIframe = document.getElementById('cinemaPlayerIframe');
    const cinemaTitle = document.getElementById('cinemaTitle');
    const cinemaDesc = document.getElementById('cinemaDescription');
    const platBadge = document.getElementById('cinemaPlatformBadge');
    const platIcon = document.getElementById('cinemaPlatformIcon');
    const platText = document.getElementById('cinemaPlatformText');
    const typeBadge = document.getElementById('cinemaTypeBadge');

    const copyBtn = document.getElementById('btnCopyCinemaLink');
    const copyText = document.getElementById('copyLinkText');

    const playBtns = document.querySelectorAll('.play-in-cinema-btn');

    playBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const embed = this.getAttribute('data-embed');
            const title = this.getAttribute('data-title');
            const desc = this.getAttribute('data-description');
            const platform = this.getAttribute('data-platform');
            const platformColor = this.getAttribute('data-platform-color');
            const platformIcon = this.getAttribute('data-platform-icon');
            const type = this.getAttribute('data-type');

            if (cinemaIframe && embed) {
                cinemaIframe.src = embed;
            }

            if (cinemaTitle && title) cinemaTitle.textContent = title;
            if (cinemaDesc && desc) cinemaDesc.textContent = desc;
            if (platText && platform) platText.textContent = platform;
            if (platBadge && platformColor) platBadge.style.backgroundColor = platformColor;
            if (platIcon && platformIcon) platIcon.className = platformIcon;
            if (typeBadge && type) typeBadge.textContent = type;

            // Scroll suave hacia el reproductor
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });

    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                copyText.textContent = '¡Copiado!';
                setTimeout(() => {
                    copyText.textContent = 'Copiar Enlace';
                }, 2000);
            });
        });
    }
});
</script>
@endsection
