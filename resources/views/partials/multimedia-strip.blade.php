@php
    $stripItems = collect($contenidos ?? [])
        ->filter(fn ($item) => in_array($item->tipo, ['podcast', 'clip', 'programa'], true) && !$item->en_vivo)
        ->values();
@endphp

@if($stripItems->isNotEmpty())
    <!-- Franja multimedia: se renderiza inmediatamente después del navbar de categorías. -->
    <section id="multimedia-strip"
             class="multimedia-strip relative z-20 w-full overflow-hidden border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900"
             aria-label="UHTV Play">
        <div class="container mx-auto px-4 py-2.5">
            <div class="mb-2 flex items-center justify-between gap-3">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="inline-flex flex-shrink-0 items-center gap-2 rounded-full bg-gradient-to-r from-purple-700 to-red-600 px-3 py-1 text-[10px] font-extrabold uppercase tracking-[0.16em] text-white shadow-sm">
                        <i class="fas fa-play"></i>
                        UHTV Play
                    </span>
                </div>

                <a href="{{ route('transmisiones.en-vivo') }}"
                   class="group inline-flex flex-shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1.5 text-xs font-bold text-purple-700 transition-colors hover:bg-purple-50 hover:text-purple-900 dark:text-purple-300 dark:hover:bg-purple-950/50 dark:hover:text-purple-200"
                   aria-label="Ver todas las transmisiones, podcasts y clips">
                    <span class="hidden sm:inline">Ver todos</span>
                    <i class="fas fa-arrow-right text-[10px] transition-transform group-hover:translate-x-0.5"></i>
                </a>
            </div>

            <div id="multimedia-strip-track"
                 class="flex snap-x snap-mandatory gap-3 overflow-x-auto pb-2"
                 style="scrollbar-width: thin; scrollbar-color: #a78bfa transparent;"
                 aria-label="Contenidos multimedia destacados">
                @foreach($stripItems as $item)
                    @php
                        $typeBadgeBg = match($item->tipo) {
                            'podcast' => '#7c3aed',
                            'clip' => '#d97706',
                            'programa' => '#2563eb',
                            default => '#4b5563',
                        };
                        $typeIcon = match($item->tipo) {
                            'podcast' => 'fas fa-podcast',
                            'clip' => 'fas fa-bolt',
                            'programa' => 'fas fa-tv',
                            default => 'fas fa-video',
                        };
                    @endphp
                    <button type="button"
                            data-open-live-modal
                            data-stream-embed="{{ $item->embed_url }}"
                            data-stream-title="{{ $item->titulo }}"
                            data-stream-platform="{{ $item->plataforma_nombre }}"
                            data-stream-platform-color="{{ $item->plataforma_color }}"
                            data-stream-platform-icon="{{ $item->plataforma_icon }}"
                            data-stream-type="{{ $item->tipo_nombre }}"
                            class="multimedia-strip-card group flex w-[310px] flex-shrink-0 snap-start items-stretch overflow-hidden rounded-xl border border-gray-200 bg-white text-left shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-purple-300 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:border-gray-700 dark:bg-gray-800 dark:focus:ring-offset-gray-900 sm:w-[350px]"
                            aria-label="Reproducir {{ $item->tipo_nombre }}: {{ $item->titulo }}">

                        <div class="relative h-[92px] w-[120px] flex-shrink-0 overflow-hidden bg-gray-900 sm:h-[100px] sm:w-[138px]">
                            <img src="{{ $item->effective_thumbnail }}"
                                 alt="{{ $item->titulo }}"
                                 class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                 loading="lazy"
                                 decoding="async"
                                 onerror="this.onerror=null;this.src='{{ asset('images/Logo.jpg') }}';">

                            <div class="absolute inset-0 bg-gradient-to-t from-black/65 via-transparent to-black/10"></div>

                            <span class="absolute left-1.5 top-1.5 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[8px] font-extrabold uppercase tracking-wide text-white shadow"
                                  style="background-color: {{ $typeBadgeBg }};">
                                <i class="{{ $typeIcon }} text-[7px]"></i>
                                {{ $item->tipo_nombre }}
                            </span>

                            @if($item->duracion)
                                <span class="absolute bottom-1.5 right-1.5 rounded bg-black/80 px-1.5 py-0.5 text-[9px] font-bold text-white">
                                    {{ $item->duracion }}
                                </span>
                            @endif
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col justify-center px-3 py-2.5">
                            <div class="mb-1 flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <i class="{{ $item->plataforma_icon }}" style="color: {{ $item->plataforma_color }};"></i>
                                <span class="truncate">{{ $item->plataforma_nombre }}</span>
                            </div>

                            <h3 class="line-clamp-2 text-xs font-extrabold leading-snug text-gray-900 transition-colors group-hover:text-purple-700 dark:text-gray-100 dark:group-hover:text-purple-300 sm:text-sm">
                                {{ $item->titulo }}
                            </h3>

                            @if($item->descripcion)
                                <p class="mt-1 line-clamp-1 text-[10px] leading-relaxed text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($item->descripcion), 80) }}
                                </p>
                            @endif

                            <div class="mt-auto flex items-center justify-between gap-2 pt-1.5 text-[9px] text-gray-400 dark:text-gray-500">
                                @if($item->fecha_transmision)
                                    <span class="inline-flex min-w-0 items-center gap-1 truncate">
                                        <i class="far fa-clock"></i>
                                        {{ $item->fecha_transmision->locale('es')->diffForHumans() }}
                                    </span>
                                @else
                                    <span></span>
                                @endif

                                <i class="fas fa-circle-play text-sm text-purple-600 transition-transform group-hover:scale-110 dark:text-purple-400"></i>
                            </div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>
    </section>
@endif
