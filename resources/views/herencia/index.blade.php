@extends('layouts.main')

@section('title', 'Herencia Invisible - Videos, Podcasts y Transmisiones en Vivo | Última Hora TV')

@section('meta_description', 'Disfruta de todos los videos, podcasts, clips informativos y transmisiones en vivo del programa oficial Herencia Invisible en Última Hora TV.')

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 transition-colors duration-300">

    <!-- ============================================================
         1. HERO HEADER: IDENTIDAD Y BRANDING DE HERENCIA INVISIBLE
    ================================================================ -->
    <section class="relative overflow-hidden border-b border-purple-900/40 bg-gradient-to-r from-slate-950 via-purple-950/80 to-slate-950 py-10 sm:py-14">
        <!-- Luces y efectos de fondo -->
        <div class="pointer-events-none absolute -left-20 -top-20 h-72 w-72 rounded-full bg-purple-600/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-20 -bottom-20 h-80 w-80 rounded-full bg-red-600/20 blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <!-- Identidad visual y logotipo -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left gap-5">
                    <div class="relative group">
                        <div class="absolute -inset-1 rounded-2xl bg-gradient-to-r from-purple-600 via-indigo-600 to-red-600 opacity-75 blur transition duration-300 group-hover:opacity-100"></div>
                        <div class="relative h-24 w-24 sm:h-28 sm:w-28 rounded-2xl bg-black p-2 shadow-2xl flex items-center justify-center overflow-hidden border border-white/20">
                            <img src="{{ asset('images/herencia_logo.png') }}"
                                 alt="Herencia Invisible"
                                 class="h-full w-full object-contain"
                                 loading="eager"
                                 onerror="this.onerror=null;this.src='{{ asset('images/Logo.jpg') }}';">
                        </div>
                    </div>

                    <div class="max-w-2xl">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gradient-to-r from-purple-700 to-indigo-600 text-white shadow-sm border border-purple-400/30">
                                <i class="fas fa-certificate text-[9px] text-amber-300"></i>
                                Producción Original UHTV
                            </span>
                            @if($liveStream)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-600 text-white shadow-lg shadow-red-600/40 animate-pulse">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                    En Vivo Ahora
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight text-white mb-2 leading-tight">
                            Herencia <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-pink-400 to-red-400">Invisible</span>
                        </h1>

                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                            El espacio audiovisual de análisis profundo, entrevistas exclusivas, podcasts informativos y coberturas en directo de <strong class="text-white">Última Hora TV</strong>.
                        </p>
                    </div>
                </div>

                <!-- Métricas y enlaces a plataformas -->
                <div class="flex flex-col sm:flex-row md:flex-col items-center md:items-end gap-3 w-full sm:w-auto">
                    <div class="flex items-center gap-2 bg-white/5 border border-white/10 backdrop-blur-md px-4 py-2 rounded-xl text-xs">
                        <i class="fas fa-layer-group text-purple-400"></i>
                        <span class="text-slate-300 font-medium">Contenidos disponibles:</span>
                        <span class="font-extrabold text-white text-sm bg-purple-600/40 px-2 py-0.5 rounded-md">{{ $counts['todos'] }}</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="https://www.youtube.com/@uhtvnoticias" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-all shadow-md hover:scale-105"
                           title="Suscribirse en YouTube">
                            <i class="fab fa-youtube text-sm"></i>
                            <span>Suscribirse</span>
                        </a>

                        <a href="#catalogo-multimedia" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all border border-white/15">
                            <i class="fas fa-th-large text-xs"></i>
                            <span>Ver Catálogo</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         2. CINEMA PLAYER (REPRODUCTOR DESTACADO PRINCIPAL)
    ================================================================ -->
    <section class="py-8 bg-slate-950/90 border-b border-slate-800" id="cinema-section">
        <div class="container mx-auto px-4 max-w-5xl">
            <!-- Barra superior del reproductor -->
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-red-600/20 text-red-500 border border-red-500/30">
                        <i class="fas fa-tv text-xs"></i>
                    </span>
                    <div>
                        <span class="text-[11px] uppercase font-bold tracking-widest text-slate-400">Reproductor Principal</span>
                        <h2 class="text-sm font-extrabold text-white flex items-center gap-2">
                            <span>Cinema Player HD</span>
                            @if($activeStream && ($activeStream->en_vivo ?? false))
                                <span class="h-2 w-2 rounded-full bg-red-500 animate-ping"></span>
                            @endif
                        </h2>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <button type="button" 
                            onclick="toggleCinemaFullscreen()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors border border-slate-700 font-semibold"
                            title="Ver en pantalla completa">
                        <i class="fas fa-expand text-[11px]"></i>
                        <span class="hidden sm:inline">Pantalla Completa</span>
                    </button>

                    <button type="button" 
                            onclick="shareCurrentVideo()" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors border border-slate-700 font-semibold"
                            title="Compartir este video">
                        <i class="fas fa-share-alt text-[11px]"></i>
                        <span>Compartir</span>
                    </button>
                </div>
            </div>

            <!-- Marco de video 16:9 con efecto cine -->
            <div id="cinemaWrapper" class="relative w-full rounded-2xl overflow-hidden bg-black shadow-2xl border border-white/10" style="padding-top: 56.25%;">
                @if($activeStream && !empty($activeStream->embed_url))
                    <iframe id="herenciaCinemaIframe"
                            class="absolute inset-0 w-full h-full border-0"
                            src="{{ $activeStream->embed_url }}"
                            title="{{ $activeStream->titulo ?? 'Herencia Invisible' }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen>
                    </iframe>
                @else
                    <div class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-slate-400 bg-slate-900">
                        <i class="fas fa-film text-5xl mb-3 text-purple-500/50"></i>
                        <h3 class="text-lg font-bold text-white mb-1">Selecciona un contenido del catálogo</h3>
                        <p class="text-xs text-slate-400 max-w-sm mb-4">Haz clic en cualquier video, podcast o clip de abajo para reproducirlo al instante en alta definición.</p>
                    </div>
                @endif
            </div>

            <!-- Metadatos del video activo -->
            @if($activeStream)
                <div class="mt-4 p-4 sm:p-5 rounded-2xl bg-slate-900 border border-slate-800">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <!-- Badge de tipo -->
                        @php
                            $tipoColor = match($activeStream->tipo ?? 'clip') {
                                'en_vivo' => 'bg-red-600 text-white',
                                'podcast' => 'bg-purple-600 text-white',
                                'programa' => 'bg-blue-600 text-white',
                                default => 'bg-amber-600 text-white',
                            };
                            $tipoIcon = match($activeStream->tipo ?? 'clip') {
                                'en_vivo' => 'fas fa-broadcast-tower',
                                'podcast' => 'fas fa-podcast',
                                'programa' => 'fas fa-tv',
                                default => 'fas fa-bolt',
                            };
                        @endphp
                        <span id="cinemaActiveType" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $tipoColor }}">
                            <i class="{{ $tipoIcon }} text-[9px]"></i>
                            <span>{{ $activeStream->tipo_nombre ?? 'Video' }}</span>
                        </span>

                        <!-- Badge de plataforma -->
                        <span id="cinemaActivePlatform" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold text-white uppercase tracking-wider"
                              style="background-color: {{ $activeStream->plataforma_color ?? '#ff0000' }};">
                            <i class="{{ $activeStream->plataforma_icon ?? 'fab fa-youtube' }} text-[10px]"></i>
                            <span>{{ $activeStream->plataforma_nombre ?? 'YouTube' }}</span>
                        </span>

                        @if(!empty($activeStream->duracion))
                            <span class="inline-flex items-center gap-1 text-[11px] text-slate-400 bg-slate-800 px-2 py-0.5 rounded-md">
                                <i class="far fa-clock text-[10px]"></i> {{ $activeStream->duracion }}
                            </span>
                        @endif

                        @if(!empty($activeStream->fecha_transmision))
                            <span class="text-[11px] text-slate-400">
                                <i class="far fa-calendar-alt text-[10px] mr-1"></i>
                                {{ is_string($activeStream->fecha_transmision) ? $activeStream->fecha_transmision : $activeStream->fecha_transmision->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>

                    <h2 id="cinemaActiveTitle" class="text-base sm:text-xl font-extrabold text-white leading-snug mb-2">
                        {{ $activeStream->titulo ?? 'Herencia Invisible - Contenido Audiovisual' }}
                    </h2>

                    @if(!empty($activeStream->descripcion))
                        <p id="cinemaActiveDescription" class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-4xl">
                            {{ $activeStream->descripcion }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <!-- ============================================================
         3. CATÁLOGO MULTIMEDIA CON BUSCADOR Y FILTROS POR PESTAÑA
    ================================================================ -->
    <section class="py-10" id="catalogo-multimedia">
        <div class="container mx-auto px-4 max-w-6xl">
            <!-- Encabezado de catálogo -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6">
                <div>
                    <span class="text-[11px] uppercase font-black tracking-widest text-purple-400">Catálogo Completo</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                        Explorar Contenidos de Herencia Invisible
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-400">Filtra por podcasts, transmisiones completas o momentos destacados.</p>
                </div>

                <!-- Buscador rápido dentro de la sección -->
                <form action="{{ route('herencia.index') }}" method="GET" class="w-full md:w-80 relative">
                    @if(request('tipo') && request('tipo') !== 'todos')
                        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                    @endif
                    <input type="text"
                           name="q"
                           value="{{ $searchQuery }}"
                           placeholder="Buscar videos, temas o invitados..."
                           class="w-full px-4 py-2.5 pl-10 pr-10 text-xs text-white bg-slate-800/90 border border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent placeholder-slate-400">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    @if($searchQuery !== '')
                        <a href="{{ route('herencia.index', ['tipo' => $selectedTipo]) }}" 
                           class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white text-xs" 
                           title="Limpiar búsqueda">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            <!-- Barra de Pestañas de Filtro -->
            <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 no-scrollbar border-b border-slate-800">
                <!-- Todos -->
                <a href="{{ route('herencia.index', array_merge(request()->except('tipo'), ['tipo' => 'todos'])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedTipo === 'todos' ? 'bg-purple-600 text-white shadow-lg shadow-purple-600/30' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 border border-slate-700/60' }}">
                    <i class="fas fa-layer-group"></i>
                    <span>Todos los Contenidos</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $selectedTipo === 'todos' ? 'bg-white/20' : 'bg-slate-700 text-slate-300' }}">{{ $counts['todos'] }}</span>
                </a>

                <!-- En Vivo -->
                <a href="{{ route('herencia.index', array_merge(request()->except('tipo'), ['tipo' => 'en_vivo'])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedTipo === 'en_vivo' ? 'bg-red-600 text-white shadow-lg shadow-red-600/30' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 border border-slate-700/60' }}">
                    <i class="fas fa-broadcast-tower text-red-400"></i>
                    <span>En Vivo</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $selectedTipo === 'en_vivo' ? 'bg-white/20' : 'bg-slate-700 text-slate-300' }}">{{ $counts['en_vivo'] }}</span>
                </a>

                <!-- Podcasts -->
                <a href="{{ route('herencia.index', array_merge(request()->except('tipo'), ['tipo' => 'podcast'])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedTipo === 'podcast' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 border border-slate-700/60' }}">
                    <i class="fas fa-podcast text-indigo-400"></i>
                    <span>Podcasts & Charlas</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $selectedTipo === 'podcast' ? 'bg-white/20' : 'bg-slate-700 text-slate-300' }}">{{ $counts['podcast'] }}</span>
                </a>

                <!-- Programas Completos -->
                <a href="{{ route('herencia.index', array_merge(request()->except('tipo'), ['tipo' => 'programa'])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedTipo === 'programa' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 border border-slate-700/60' }}">
                    <i class="fas fa-tv text-blue-400"></i>
                    <span>Programas & Videos</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $selectedTipo === 'programa' ? 'bg-white/20' : 'bg-slate-700 text-slate-300' }}">{{ $counts['programa'] }}</span>
                </a>

                <!-- Clips -->
                <a href="{{ route('herencia.index', array_merge(request()->except('tipo'), ['tipo' => 'clip'])) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $selectedTipo === 'clip' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/30' : 'bg-slate-800/80 hover:bg-slate-800 text-slate-300 border border-slate-700/60' }}">
                    <i class="fas fa-bolt text-amber-400"></i>
                    <span>Clips & Cortos</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $selectedTipo === 'clip' ? 'bg-white/20' : 'bg-slate-700 text-slate-300' }}">{{ $counts['clip'] }}</span>
                </a>
            </div>

            <!-- Grilla de Tarjetas Multimedia -->
            @if($allContents->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($allContents as $item)
                        @php
                            $itemId = isset($item->id) ? $item->id : null;
                            $youtubeId = isset($item->youtube_id) ? $item->youtube_id : null;
                            $thumbnail = $item->effective_thumbnail ?? ($item->thumbnail_url ?? asset('images/Logo.jpg'));
                            $embedUrl = $item->embed_url ?? '';
                            $titulo = $item->titulo ?? 'Herencia Invisible';
                            $descripcion = $item->descripcion ?? '';
                            $duracion = $item->duracion ?? null;
                            $tipo = $item->tipo ?? 'clip';
                            $tipoNombre = $item->tipo_nombre ?? 'Video';
                            $plataformaNombre = $item->plataforma_nombre ?? 'YouTube';
                            $plataformaIcon = $item->plataforma_icon ?? 'fab fa-youtube';
                            $plataformaColor = $item->plataforma_color ?? '#ff0000';
                            $isLive = (bool) ($item->en_vivo ?? false);

                            $badgeClass = match($tipo) {
                                'en_vivo' => 'bg-red-600 text-white',
                                'podcast' => 'bg-purple-600 text-white',
                                'programa' => 'bg-blue-600 text-white',
                                default => 'bg-amber-600 text-white',
                            };
                            $typeIcon = match($tipo) {
                                'en_vivo' => 'fas fa-broadcast-tower',
                                'podcast' => 'fas fa-podcast',
                                'programa' => 'fas fa-tv',
                                default => 'fas fa-bolt',
                            };
                        @endphp

                        <div class="group flex flex-col rounded-2xl overflow-hidden bg-slate-800/70 border border-slate-700/70 hover:border-purple-500/50 hover:shadow-xl hover:shadow-purple-950/40 transition-all duration-300 transform hover:-translate-y-1">
                            <!-- Miniatura con botón de reproducción -->
                            <div class="relative w-full aspect-video bg-black overflow-hidden">
                                <img src="{{ $thumbnail }}"
                                     alt="{{ $titulo }}"
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="this.onerror=null;this.src='{{ asset('images/Logo.jpg') }}';">

                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                                <!-- Insignia de tipo en la miniatura -->
                                <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider shadow-md {{ $badgeClass }}">
                                        <i class="{{ $typeIcon }} text-[8px]"></i>
                                        <span>{{ $tipoNombre }}</span>
                                    </span>
                                    @if($isLive)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-red-600 text-white shadow-md animate-pulse">
                                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
                                            En Vivo
                                        </span>
                                    @endif
                                </div>

                                <!-- Plataforma e icono -->
                                <div class="absolute top-2.5 right-2.5">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-black/60 backdrop-blur-md text-white shadow border border-white/20"
                                          title="{{ $plataformaNombre }}">
                                        <i class="{{ $plataformaIcon }} text-[11px]" style="color: {{ $plataformaColor }};"></i>
                                    </span>
                                </div>

                                <!-- Duración -->
                                @if($duracion)
                                    <span class="absolute bottom-2 right-2 px-1.5 py-0.5 rounded bg-black/80 text-[10px] font-bold text-white backdrop-blur-sm">
                                        {{ $duracion }}
                                    </span>
                                @endif

                                <!-- Botón central de Play en Hover -->
                                <button type="button"
                                        onclick="playInCinema('{{ $embedUrl }}', '{{ addslashes($titulo) }}', '{{ addslashes($descripcion) }}', '{{ $tipoNombre }}', '{{ $tipo }}', '{{ $plataformaNombre }}', '{{ $plataformaIcon }}', '{{ $plataformaColor }}')"
                                        class="absolute inset-0 m-auto h-12 w-12 rounded-full bg-purple-600/90 text-white flex items-center justify-center shadow-xl opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300 border border-white/30 backdrop-blur-sm"
                                        title="Reproducir en Cinema Player">
                                    <i class="fas fa-play text-sm ml-0.5"></i>
                                </button>
                            </div>

                            <!-- Contenido de la tarjeta -->
                            <div class="p-4 flex flex-col flex-1 justify-between">
                                <div>
                                    <div class="flex items-center justify-between text-[10px] text-slate-400 mb-2">
                                        <span class="font-semibold text-purple-400 flex items-center gap-1">
                                            <i class="fas fa-circle text-[6px]"></i>
                                            {{ $plataformaNombre }}
                                        </span>
                                        @if(!empty($item->fecha_transmision))
                                            <span>
                                                <i class="far fa-calendar text-[9px] mr-1"></i>
                                                {{ is_string($item->fecha_transmision) ? $item->fecha_transmision : $item->fecha_transmision->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-sm font-bold text-white line-clamp-2 leading-snug group-hover:text-purple-300 transition-colors mb-2">
                                        {{ $titulo }}
                                    </h3>

                                    @if($descripcion)
                                        <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed mb-3">
                                            {{ Str::limit(strip_tags($descripcion), 110) }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Botones de acción al pie de la tarjeta -->
                                <div class="pt-3 border-t border-slate-700/60 flex items-center justify-between mt-auto gap-2">
                                    <button type="button"
                                            onclick="playInCinema('{{ $embedUrl }}', '{{ addslashes($titulo) }}', '{{ addslashes($descripcion) }}', '{{ $tipoNombre }}', '{{ $tipo }}', '{{ $plataformaNombre }}', '{{ $plataformaIcon }}', '{{ $plataformaColor }}')"
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-purple-400 hover:text-purple-300 transition-colors">
                                        <i class="fas fa-play text-[10px]"></i>
                                        <span>Reproducir</span>
                                    </button>

                                    <div class="flex items-center gap-1.5">
                                        <button type="button" 
                                                onclick="copyVideoLink('{{ $item->url ?? $embedUrl }}')" 
                                                class="h-7 w-7 rounded-lg bg-slate-700/60 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors"
                                                title="Copiar enlace">
                                            <i class="fas fa-link text-[10px]"></i>
                                        </button>

                                        @if(!empty($item->url))
                                            <a href="{{ $item->url }}" 
                                               target="_blank" 
                                               rel="noopener noreferrer"
                                               class="h-7 w-7 rounded-lg bg-slate-700/60 hover:bg-red-600/80 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors"
                                               title="Ver en plataforma original">
                                                <i class="fas fa-external-link-alt text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Estado vacío cuando no hay resultados de búsqueda -->
                <div class="text-center py-16 px-4 rounded-2xl bg-slate-800/40 border border-slate-700/60 max-w-lg mx-auto">
                    <div class="h-14 w-14 rounded-full bg-purple-600/20 text-purple-400 flex items-center justify-center mx-auto mb-4 text-xl">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2">No se encontraron contenidos</h3>
                    <p class="text-xs text-slate-400 mb-5 leading-relaxed">
                        No hay videos, podcasts o transmisiones que coincidan con "{{ $searchQuery }}". Prueba con otros términos o restablece los filtros.
                    </p>
                    <a href="{{ route('herencia.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold transition-all shadow-md">
                        <i class="fas fa-undo"></i>
                        <span>Ver Todos los Contenidos</span>
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- ============================================================
         4. ACERCA DE HERENCIA INVISIBLE / COMUNIDAD
    ================================================================ -->
    <section class="py-12 bg-slate-950 border-t border-slate-800">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-purple-950/40 via-slate-900 to-indigo-950/40 border border-purple-800/30 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 text-purple-400 text-xs font-bold uppercase tracking-wider mb-2">
                        <i class="fas fa-microphone-alt"></i>
                        <span>Periodismo Audiovisual de Vanguardia</span>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-black text-white mb-2">
                        ¿Tienes una historia, denuncia o sugerencia para Herencia Invisible?
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Nuestro equipo de producción investiga permanentemente temas de impacto social, político y cultural en Bolivia y el mundo. Envíanos tus propuestas o participa en nuestras emisiones en vivo.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <a href="https://wa.me/59170000000" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold transition-all shadow-lg hover:scale-105">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span>Escribir por WhatsApp</span>
                    </a>
                    <a href="https://www.youtube.com/@uhtvnoticias" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-all border border-slate-700">
                        <i class="fab fa-youtube text-red-500 text-sm"></i>
                        <span>Canal de YouTube</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- ============================================================
     5. TOAST NOTIFICACIÓN Y SCRIPTS INTERACTIVOS
================================================================ -->
<div id="herenciaToast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 bg-slate-900 text-white border border-purple-500/50 px-4 py-3 rounded-xl shadow-2xl flex items-center gap-2.5 text-xs">
    <i class="fas fa-check-circle text-emerald-400"></i>
    <span id="herenciaToastMessage">Enlace copiado al portapapeles</span>
</div>

<script>
/**
 * Cambia el video en el Cinema Player principal sin recargar la página
 */
function playInCinema(embedUrl, title, description, tipoNombre, tipo, plataformaNombre, plataformaIcon, plataformaColor) {
    if (!embedUrl) return;

    // Actualizar Iframe
    const iframe = document.getElementById('herenciaCinemaIframe');
    if (iframe) {
        // Asegurar autoplay al hacer clic explícito
        const separator = embedUrl.includes('?') ? '&' : '?';
        iframe.src = embedUrl + separator + 'autoplay=1';
    }

    // Actualizar Título
    const titleEl = document.getElementById('cinemaActiveTitle');
    if (titleEl && title) {
        titleEl.textContent = title;
    }

    // Actualizar Descripción
    const descEl = document.getElementById('cinemaActiveDescription');
    if (descEl) {
        descEl.textContent = description || '';
    }

    // Actualizar Tipo
    const typeEl = document.getElementById('cinemaActiveType');
    if (typeEl && tipoNombre) {
        typeEl.innerHTML = `<i class="fas fa-play text-[9px]"></i> <span>${tipoNombre}</span>`;
    }

    // Actualizar Plataforma
    const platEl = document.getElementById('cinemaActivePlatform');
    if (platEl && plataformaNombre) {
        platEl.style.backgroundColor = plataformaColor || '#ff0000';
        platEl.innerHTML = `<i class="${plataformaIcon || 'fab fa-youtube'} text-[10px]"></i> <span>${plataformaNombre}</span>`;
    }

    // Desplazamiento suave al Cinema Player
    const playerSection = document.getElementById('cinema-section');
    if (playerSection) {
        playerSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Pantalla completa en el reproductor Cinema
 */
function toggleCinemaFullscreen() {
    const wrapper = document.getElementById('cinemaWrapper');
    if (!wrapper) return;

    if (!document.fullscreenElement) {
        wrapper.requestFullscreen().catch(err => {
            console.warn('Error al activar pantalla completa:', err);
        });
    } else {
        document.exitFullscreen();
    }
}

/**
 * Compartir video activo o enlace
 */
function shareCurrentVideo() {
    const url = window.location.href;
    const title = document.getElementById('cinemaActiveTitle')?.textContent || 'Herencia Invisible - UHTV';

    if (navigator.share) {
        navigator.share({
            title: title,
            text: 'Mira este contenido en Herencia Invisible - Última Hora TV:',
            url: url,
        }).catch(() => {});
    } else {
        copyVideoLink(url);
    }
}

/**
 * Copiar enlace al portapapeles con Toast de confirmación
 */
function copyVideoLink(url) {
    if (!url) url = window.location.href;

    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            showHerenciaToast('¡Enlace copiado al portapapeles!');
        }).catch(() => {
            showHerenciaToast('No se pudo copiar automáticamente. Copia la URL manualmente.');
        });
    } else {
        showHerenciaToast('Enlace listo para compartir');
    }
}

function showHerenciaToast(msg) {
    const toast = document.getElementById('herenciaToast');
    const msgEl = document.getElementById('herenciaToastMessage');
    if (!toast || !msgEl) return;

    msgEl.textContent = msg;
    toast.classList.remove('translate-y-20', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-20', 'opacity-0');
    }, 2800);
}
</script>
@endsection

