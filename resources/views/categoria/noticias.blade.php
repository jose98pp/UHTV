@extends('layouts.main')

@section('title', $categoria->name . ' - UHTV')

@section('content')
<!-- Header de Categoría -->
<section class="relative overflow-hidden rounded-3xl mb-8 shadow-2xl">
    <div class="absolute inset-0 bg-gradient-to-r from-purple-900 via-purple-800 to-red-900"></div>
    <div class="absolute inset-0 bg-[url('/images/pattern.png')] opacity-10"></div>
    <div class="relative container mx-auto px-6 py-16 text-center">
        <div class="inline-flex items-center bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-6 py-2 mb-6 shadow-lg transform hover:scale-105 transition-transform duration-300">
            <i class="fas fa-tag mr-2 text-purple-300"></i>
            <span class="text-sm font-bold tracking-wider text-white uppercase">Categoría</span>
        </div>
        <h1 class="text-5xl md:text-6xl font-black text-white mb-4 tracking-tight drop-shadow-lg">
            {{ $categoria->name }}
        </h1>
        <p class="text-purple-200 text-xl font-medium max-w-2xl mx-auto">
            Explora las últimas noticias y actualizaciones sobre {{ strtolower($categoria->name) }}.
            <span class="block mt-2 text-sm bg-black/20 inline-block px-4 py-1 rounded-full">
                {{ $categoria->noticias->count() }} artículos disponibles
            </span>
        </p>
    </div>
</section>

<!-- Noticias de la Categoría -->
<section class="py-4">
    <div class="container mx-auto">
        @forelse($noticiasCategoria as $index => $noticia)
            <article class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden mb-8 hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 transform hover:-translate-y-1">
                <div class="md:flex h-full">
                    <!-- Imagen -->
                    <div class="md:w-2/5 relative overflow-hidden">
                        <a href="{{ route('show', $noticia->id) }}" class="block h-full">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 z-10"></div>
                            <img src="{{ $noticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                                 alt="{{ $noticia->titulo }}" 
                                 class="w-full h-64 md:h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                            
                            <!-- Categoría Badge en imagen (Móvil) -->
                            <div class="absolute top-4 left-4 md:hidden z-20">
                                <span class="bg-purple-600/90 backdrop-blur-sm text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                    {{ $categoria->name }}
                                </span>
                            </div>
                        </a>
                    </div>
                    
                    <!-- Contenido -->
                    <div class="md:w-3/5 p-6 md:p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="hidden md:inline-block bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                                    {{ $categoria->name }}
                                </span>
                                <span class="text-gray-500 dark:text-gray-400 text-sm flex items-center font-medium">
                                    <i class="far fa-clock mr-2 text-purple-500"></i>
                                    {{ \Carbon\Carbon::parse($noticia->created_at)->locale('es')->diffForHumans() }}
                                </span>
                            </div>
                            
                            <a href="{{ route('show', $noticia->id) }}" class="block">
                                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-4 leading-tight group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                                    {{ $noticia->titulo }}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 line-clamp-3 text-lg">
                                    {{ $noticia->excerptLimpio ?? Str::limit(strip_tags($noticia->contenido), 200) }}
                                </p>
                            </a>
                        </div>
                        
                        <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center space-x-4">
                                <button class="text-gray-400 hover:text-red-500 transition-colors duration-300">
                                    <i class="far fa-heart"></i>
                                </button>
                                <button class="text-gray-400 hover:text-blue-500 transition-colors duration-300">
                                    <i class="far fa-share-square"></i>
                                </button>
                            </div>
                            <a href="{{ route('show', $noticia->id) }}" 
                               class="inline-flex items-center bg-gray-50 dark:bg-gray-700 hover:bg-purple-600 dark:hover:bg-purple-600 text-gray-700 dark:text-gray-200 hover:text-white px-6 py-2 rounded-full font-semibold transition-all duration-300 group-hover:shadow-md">
                                Leer artículo <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </article>
        @empty
            <div class="text-center py-20">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-12 max-w-lg mx-auto border border-gray-100 dark:border-gray-700">
                    <div class="w-24 h-24 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-newspaper text-purple-500 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No hay noticias aún</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 text-lg">
                        Estamos trabajando en traer las últimas novedades de esta categoría.
                    </p>
                    <a href="{{ route('portada') }}" 
                       class="inline-flex items-center bg-purple-600 text-white px-8 py-3 rounded-full hover:bg-purple-700 transition-colors shadow-lg hover:shadow-purple-500/30 font-semibold">
                        <i class="fas fa-home mr-2"></i>
                        Volver al inicio
                    </a>
                </div>
            </div>
        @endforelse

        <!-- Paginación -->
        @if($noticiasCategoria->hasPages())
            <div class="mt-12 mb-12">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">
                            Mostrando <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $noticiasCategoria->firstItem() }}</span> - <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $noticiasCategoria->lastItem() }}</span> 
                            de <span class="text-gray-900 dark:text-white font-bold">{{ $noticiasCategoria->total() }}</span> resultados
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Ver por página:</span>
                            <select onchange="changePerPage(this.value)" class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-shadow">
                                <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-100 dark:border-gray-700 my-4"></div>
                    
                    <!-- Enlaces de paginación -->
                    <div class="flex items-center justify-center flex-wrap gap-2">
                        {{-- Botón Anterior --}}
                        @if ($noticiasCategoria->onFirstPage())
                            <span class="px-4 py-2 text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-800 rounded-lg cursor-not-allowed border border-gray-200 dark:border-gray-700">
                                <i class="fas fa-chevron-left mr-2"></i>Anterior
                            </span>
                        @else
                            <a href="{{ $noticiasCategoria->previousPageUrl() }}" 
                               class="px-4 py-2 text-purple-600 dark:text-purple-400 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-900/50 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-300 transition-all duration-300 shadow-sm">
                                <i class="fas fa-chevron-left mr-2"></i>Anterior
                            </a>
                        @endif

                        {{-- Números de página --}}
                        <div class="hidden md:flex space-x-1">
                            @foreach ($noticiasCategoria->getUrlRange(1, $noticiasCategoria->lastPage()) as $page => $url)
                                @if ($page == $noticiasCategoria->currentPage())
                                    <span class="w-10 h-10 flex items-center justify-center bg-purple-600 text-white rounded-lg font-bold shadow-md shadow-purple-500/30">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" 
                                       class="w-10 h-10 flex items-center justify-center text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-600 dark:hover:text-purple-400 transition-all duration-300">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        {{-- Botón Siguiente --}}
                        @if ($noticiasCategoria->hasMorePages())
                            <a href="{{ $noticiasCategoria->nextPageUrl() }}" 
                               class="px-4 py-2 text-purple-600 dark:text-purple-400 bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-900/50 rounded-lg hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:text-purple-700 dark:hover:text-purple-300 transition-all duration-300 shadow-sm">
                                Siguiente<i class="fas fa-chevron-right ml-2"></i>
                            </a>
                        @else
                            <span class="px-4 py-2 text-gray-400 dark:text-gray-600 bg-gray-100 dark:bg-gray-800 rounded-lg cursor-not-allowed border border-gray-200 dark:border-gray-700">
                                Siguiente<i class="fas fa-chevron-right ml-2"></i>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<script>
function changePerPage(perPage) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset to first page
    window.location.href = url.toString();
}
</script>

<!-- Banner Publicitario -->
@if(isset($banners['category_bottom']) && $banners['category_bottom']->count() > 0)
    <section class="py-8">
        <div class="container mx-auto px-4">
            @foreach($banners['category_bottom'] as $banner)
                <div class="flex justify-center mb-6">
                    <div class="relative group max-w-4xl w-full">
                        <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <a href="{{ $banner->link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="relative block bg-white dark:bg-gray-900 rounded-xl p-1">     
                            <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" 
                                 class="w-full h-auto rounded-lg shadow-sm">
                            <div class="absolute top-2 right-2 bg-black/50 text-white text-[10px] px-2 py-0.5 rounded uppercase tracking-wider">Publicidad</div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif

<!-- Otras Noticias Relacionadas -->
@if(isset($noticias) && $noticias->count() > 0)
<section class="py-12 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-800">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Otras Noticias</h2>
                <div class="h-1 w-20 bg-purple-600 rounded-full"></div>
            </div>
            <a href="{{ route('portada') }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-medium flex items-center transition-colors">
                Ver todas <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($noticias as $otraNoticia)
                <article class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 group border border-gray-100 dark:border-gray-700">
                    <div class="relative overflow-hidden h-48">
                        <a href="{{ route('show', $otraNoticia->id) }}">
                            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/10 transition-colors z-10"></div>
                            <img src="{{ $otraNoticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                                 alt="{{ $otraNoticia->titulo }}" 
                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            <div class="absolute top-3 left-3 z-20">
                                <span class="bg-purple-600 text-white px-2 py-1 rounded text-xs font-bold shadow-md">
                                    {{ $otraNoticia->category->name ?? 'General' }}
                                </span>
                            </div>
                        </a>
                    </div>
                    
                    <div class="p-5">
                        <div class="mb-3">
                            <span class="text-gray-500 dark:text-gray-400 text-xs font-medium flex items-center">
                                <i class="far fa-clock mr-1.5"></i>
                                {{ \Carbon\Carbon::parse($otraNoticia->created_at)->locale('es')->diffForHumans() }}
                            </span>
                        </div>
                        <a href="{{ route('show', $otraNoticia->id) }}">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-3 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                {{ $otraNoticia->titulo }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 leading-relaxed">
                                {{ $otraNoticia->excerptLimpio ?? Str::limit(strip_tags($otraNoticia->contenido), 120) }}
                            </p>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
