@extends('layouts.main')

@section('title', 'Búsqueda: ' . $query . ' - UHTV')

@section('content')
<!-- Breadcrumb de Búsqueda -->
<section class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 py-4 border-b border-gray-200 dark:border-gray-700">
    <div class="container mx-auto px-4">
        <nav class="flex items-center space-x-3 text-sm mb-4">
            <a href="{{ route('portada') }}" class="text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-300 flex items-center font-medium">
                <i class="fas fa-home mr-2"></i>Inicio
            </a>
            <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
            <span class="text-gray-600 dark:text-gray-300 font-medium">Búsqueda</span>
        </nav>
        
        <!-- Formulario de búsqueda -->
        <div class="max-w-2xl">
            <form action="{{ route('search') }}" method="GET" class="flex">
                <div class="relative flex-1">
                    <input type="text" 
                           name="q" 
                           value="{{ $query }}" 
                           placeholder="Buscar noticias..." 
                           class="w-full px-4 py-3 pl-12 pr-4 text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                </div>
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-red-600 hover:from-purple-700 hover:to-red-700 text-white px-8 py-3 rounded-r-xl font-semibold transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg">
                    Buscar
                </button>
            </form>
        </div>
    </div>
</section>

<!-- Resultados de Búsqueda -->
<section class="py-12 bg-white dark:bg-gray-900 min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header de Resultados -->
            <div class="mb-8">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Resultados de búsqueda
                </h1>
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <p class="text-gray-600 dark:text-gray-400 text-lg">
                        <span class="font-semibold text-purple-600 dark:text-purple-400">{{ $total }}</span> 
                        {{ $total == 1 ? 'resultado encontrado' : 'resultados encontrados' }} 
                        para "<span class="font-semibold">{{ $query }}</span>"
                    </p>
                    
                    @if($total > 0)
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Mostrando {{ $noticias->firstItem() }} - {{ $noticias->lastItem() }} de {{ $total }}
                        </div>
                    @endif
                </div>
                <div class="w-32 h-1 bg-gradient-to-r from-purple-600 to-red-600 rounded-full mt-4"></div>
            </div>

            @if($noticias->count() > 0)
                <!-- Grid de Resultados -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach($noticias as $noticia)
                        <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                            <div class="relative overflow-hidden">
                                <a href="{{ route('show', $noticia->id) }}" class="block">
                                    <img src="{{ $noticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                                         alt="{{ $noticia->titulo }}" 
                                         class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110"
                                         onerror="handleImageError(this)">
                                </a>
                                
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
                                </div>
                                
                                <!-- Título y Contenido -->
                                <a href="{{ route('show', $noticia->id) }}" class="block group">
                                    <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-3 text-lg leading-tight line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                                        {{ $noticia->titulo }}
                                    </h3>
                                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 leading-relaxed">
                                        {{ $noticia->excerptLimpio ?? Str::limit(strip_tags($noticia->contenido), 120) }}
                                    </p>
                                </a>
                                
                                <!-- Botón de Acción -->
                                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <a href="{{ route('show', $noticia->id) }}" 
                                       class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-semibold text-sm transition-colors duration-300 group">
                                        Leer noticia completa
                                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="flex justify-center">
                    {{ $noticias->appends(['q' => $query])->links() }}
                </div>
            @else
                <!-- Sin Resultados -->
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="mb-8">
                            <i class="fas fa-search text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            No se encontraron resultados
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-8">
                            No pudimos encontrar noticias que coincidan con "<strong>{{ $query }}</strong>". 
                            Intenta con otros términos de búsqueda.
                        </p>
                        
                        <!-- Sugerencias -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 text-left">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Sugerencias:</h3>
                            <ul class="text-gray-600 dark:text-gray-400 text-sm space-y-2">
                                <li>• Verifica la ortografía de las palabras</li>
                                <li>• Intenta con términos más generales</li>
                                <li>• Usa palabras clave diferentes</li>
                                <li>• Explora nuestras categorías</li>
                            </ul>
                        </div>
                        
                        <!-- Botón para volver -->
                        <div class="mt-8">
                            <a href="{{ route('portada') }}" class="inline-flex items-center bg-gradient-to-r from-purple-600 to-red-600 text-white px-6 py-3 rounded-xl font-semibold hover:from-purple-700 hover:to-red-700 transition-all duration-300 transform hover:-translate-y-1 shadow-lg">
                                <i class="fas fa-home mr-2"></i>
                                Volver al Inicio
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Categorías Sugeridas -->
@if($noticias->count() == 0)
<section class="py-12 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                Explora nuestras categorías
            </h2>
            <div class="flex flex-wrap justify-center gap-4">
                @foreach($categorias as $categoria)
                    <a href="{{ route('categoria.noticias', $categoria->id) }}" 
                       class="bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gradient-to-r hover:from-purple-600 hover:to-red-600 hover:text-white px-6 py-3 rounded-full transition-all duration-300 font-semibold shadow-md hover:shadow-lg transform hover:-translate-y-1">
                        {{ $categoria->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif
@endsection