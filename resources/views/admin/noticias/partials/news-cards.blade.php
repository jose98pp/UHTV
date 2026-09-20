@if($news->count() > 0)
    <div class="news-cards-grid">
        @foreach($news as $noticia)
            <div class="news-card bg-white dark:bg-slate-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between" id="news-card-{{ $noticia->id }}">
                <!-- Card Image -->
                <div class="news-card-image relative h-48 w-full overflow-hidden rounded-t-2xl bg-slate-100 dark:bg-slate-900 group">
                    <!-- Select Checkbox -->
                    <div class="absolute top-2.5 left-2.5 z-10">
                        <div class="bg-black/50 backdrop-blur-md rounded-lg p-1.5 border border-white/20 shadow flex items-center justify-center">
                            <input type="checkbox" 
                                   name="news_ids[]" 
                                   value="{{ $noticia->id }}" 
                                   class="news-checkbox form-check-input m-0 cursor-pointer" 
                                   style="width: 18px; height: 18px; accent-color: #6366f1;"
                                   onchange="onNewsCheckboxChange()">
                        </div>
                    </div>

                    <!-- Real Image with graceful fallback -->
                    <img src="{{ $noticia->imageUrl }}" 
                         data-src="{{ $noticia->imageUrl }}"
                         alt="{{ $noticia->titulo }}" 
                         class="lazy-image loaded w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         loading="lazy"
                         decoding="async"
                         onload="this.classList.add('loaded')"
                         onerror="this.onerror=null; this.src='{{ asset('images/default-news.svg') }}'; this.classList.add('loaded');">
                    
                    <!-- Quick Status Toggle Badge (1-click toggle on image) -->
                    <div class="absolute top-2.5 right-2.5 z-10">
                        <button type="button" 
                                onclick="quickToggleStatus({{ $noticia->id }}, this)" 
                                class="status-toggle-btn px-2.5 py-1 text-[11px] font-bold rounded-full shadow-md backdrop-blur-md border transition-all duration-200 flex items-center gap-1.5 cursor-pointer {{ $noticia->publicada ? 'bg-emerald-600/90 hover:bg-emerald-600 text-white border-emerald-400/40' : 'bg-amber-500/90 hover:bg-amber-500 text-white border-amber-300/40' }}"
                                title="Clic para alternar publicación">
                            <i class="fas {{ $noticia->publicada ? 'fa-check' : 'fa-clock' }} text-[10px]"></i>
                            <span class="status-label">{{ $noticia->publicada ? 'Publicada' : 'Borrador' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Card Content -->
                <div class="news-card-content p-4 flex flex-col flex-grow justify-between">
                    <div>
                        <!-- Category Badge & Date Header -->
                        <div class="news-card-header flex items-center justify-between mb-2">
                            <span class="category-badge px-2.5 py-0.5 text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-950/70 dark:text-purple-300 dark:border dark:border-purple-500/30 rounded-full uppercase tracking-wider">
                                {{ $noticia->category->name ?? 'Sin Categoría' }}
                            </span>
                            <small class="text-slate-400 dark:text-slate-400 text-xs font-medium flex items-center gap-1">
                                <i class="far fa-calendar-alt text-[10px]"></i>
                                {{ $noticia->created_at->format('d/m/Y') }}
                            </small>
                        </div>

                        <!-- Card Title -->
                        <h5 class="news-card-title text-base font-bold text-slate-900 dark:text-white mb-1.5 line-clamp-2 leading-snug hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                            <a href="{{ route('admin.noticias.edit', $noticia->id) }}" class="text-inherit no-underline">
                                {{ $noticia->titulo }}
                            </a>
                        </h5>

                        <!-- Card Description -->
                        <p class="news-card-description text-xs text-slate-500 dark:text-slate-300 mb-3 line-clamp-2 leading-relaxed">
                            {{ Str::limit(strip_tags($noticia->contenido), 120) }}
                        </p>
                    </div>

                    <!-- Card Footer with Clean Actions -->
                    <div class="news-card-footer flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-700/60 mt-auto">
                        <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <span class="font-mono text-[11px] bg-slate-100 dark:bg-slate-700/60 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300 font-semibold">ID: {{ $noticia->id }}</span>
                            <span>•</span>
                            <span class="text-indigo-600 dark:text-indigo-400 font-semibold flex items-center gap-1 text-[11px]" title="Visualizaciones">
                                <i class="fas fa-eye text-[10px]"></i>{{ number_format($noticia->views ?? 0) }}
                            </span>
                        </div>
                        
                        <!-- Actions Dropdown & Quick Actions -->
                        <div class="actions-dropdown relative flex items-center gap-1">
                            @if($noticia->publicada)
                                <a href="{{ $noticia->url }}" 
                                   target="_blank"
                                   class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-950/40 transition-colors" 
                                   title="Ver noticia en portal">
                                    <i class="fas fa-external-link-alt text-[11px]"></i>
                                </a>
                            @endif

                            <a href="{{ route('admin.noticias.edit', $noticia->id) }}" 
                               class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors" 
                               title="Editar noticia">
                                <i class="fas fa-pen text-[11px]"></i>
                            </a>

                            <button type="button" 
                                    class="actions-trigger w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" 
                                    onclick="toggleDropdown({{ $noticia->id }})"
                                    title="Más opciones">
                                <i class="fas fa-ellipsis-v text-xs"></i>
                            </button>

                            <!-- Dropdown Menu (opens upwards cleanly without overflowing) -->
                            <div id="dropdown-{{ $noticia->id }}" class="actions-menu absolute right-0 bottom-full mb-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 z-50 hidden py-1 text-xs" style="flex-direction: column;">
                                <button type="button" 
                                        onclick="quickToggleStatus({{ $noticia->id }}, null)" 
                                        class="flex items-center w-full px-3.5 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors bg-transparent border-0 text-left font-medium"
                                        style="display: flex !important; width: 100% !important; align-items: center !important;">
                                    <i class="fas fa-exchange-alt mr-2.5 text-amber-500 w-4"></i>
                                    <span>Cambiar estado</span>
                                </button>
                                @if($noticia->publicada)
                                    <a href="{{ $noticia->url }}" 
                                       target="_blank"
                                       class="flex items-center w-full px-3.5 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors no-underline font-medium"
                                       style="display: flex !important; width: 100% !important; align-items: center !important;">
                                        <i class="fas fa-eye mr-2.5 text-blue-500 w-4"></i>
                                        <span>Ver noticia</span>
                                    </a>
                                @endif
                                <a href="{{ route('admin.noticias.edit', $noticia->id) }}" 
                                   class="flex items-center w-full px-3.5 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors no-underline font-medium"
                                   style="display: flex !important; width: 100% !important; align-items: center !important;">
                                    <i class="fas fa-edit mr-2.5 text-indigo-500 w-4"></i>
                                    <span>Editar</span>
                                </a>
                                <div class="divider my-1 border-t border-slate-100 dark:border-slate-700"></div>
                                <form action="{{ route('admin.noticias.destroy', $noticia->id) }}" 
                                      method="POST" 
                                      class="m-0 p-0 block w-full"
                                      style="width: 100% !important; margin: 0 !important; padding: 0 !important;"
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta noticia? Se borrarán también sus imágenes del almacenamiento.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="flex items-center w-full px-3.5 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 transition-colors bg-transparent border-0 text-left font-medium"
                                            style="display: flex !important; width: 100% !important; align-items: center !important;">
                                        <i class="fas fa-trash mr-2.5 text-red-500 w-4"></i>
                                        <span>Eliminar</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="text-center py-12">
        <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron noticias</h3>
        <p class="text-gray-500 mb-6">Intenta ajustar los filtros de búsqueda.</p>
        <button onclick="clearAllFilters()" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-times-circle mr-2"></i>
            Limpiar Filtros
        </button>
    </div>
@endif