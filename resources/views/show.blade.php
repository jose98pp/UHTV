@extends('layouts.main')

@section('title', $noticia->titulo . ' - UHTV')

@section('content')
<!-- Breadcrumb Mejorado -->
<section class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 py-4 border-b border-gray-200 dark:border-gray-600 transition-colors duration-300">
    <div class="container mx-auto px-4">
        <nav class="flex items-center space-x-3 text-sm">
            <a href="{{ route('portada') }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 transition-colors duration-300 flex items-center font-medium">
                <i class="fas fa-home mr-2"></i>Inicio
            </a>
            <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
            <a href="{{ route('categoria.noticias', $noticia->category->id) }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 transition-colors duration-300 font-medium">
                {{ $noticia->category->name ?? 'Noticias' }}
            </a>
            <i class="fas fa-chevron-right text-gray-400 dark:text-gray-500"></i>
            <span class="text-gray-600 dark:text-gray-300 font-medium">{{ Str::limit($noticia->titulo, 60) }}</span>
        </nav>
    </div>
</section>

<!-- Artículo Principal - Diseño Moderno -->
<article class="bg-white dark:bg-gray-900 transition-colors duration-300">
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header del Artículo -->
            <header class="mb-12">
                <!-- Metadata Superior -->
                <div class="flex flex-wrap items-center justify-between mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-4 mb-4 md:mb-0">
                        <span class="bg-gradient-to-r from-purple-600 to-red-600 text-white px-4 py-2 rounded-full text-sm font-semibold uppercase tracking-wide">
                            {{ $noticia->category->name ?? 'General' }}
                        </span>
                        <time class="text-gray-500 dark:text-gray-400 text-sm flex items-center font-medium">
                            <i class="fas fa-clock mr-2 text-purple-600 dark:text-purple-400"></i>
                            {{ \Carbon\Carbon::parse($noticia->created_at)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm') }}
                        </time>
                    </div>
                    
                    <!-- Botones de Compartir Mejorados -->
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-500 text-sm font-medium">Compartir:</span>
                        <div class="flex space-x-2">
                            <a href="https://twitter.com/intent/tweet?text={{ urlencode($noticia->titulo) }}&url={{ urlencode(Request::url()) }}" 
                               target="_blank" 
                               class="bg-blue-400 hover:bg-blue-500 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 shadow-lg">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}" 
                               target="_blank" 
                               class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 shadow-lg">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ urlencode($noticia->titulo . ' ' . Request::url()) }}" 
                               target="_blank" 
                               class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 shadow-lg">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <button onclick="copyToClipboard('{{ Request::url() }}')" 
                                    class="bg-gray-600 hover:bg-gray-700 text-white p-3 rounded-full transition-all duration-300 transform hover:scale-110 shadow-lg">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Título Principal -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 dark:text-gray-100 leading-tight mb-8 transition-colors duration-300">
                    {{ $noticia->titulo }}
                </h1>
                
                <!-- Resumen/Extracto Mejorado -->
                <div class="bg-gradient-to-r from-purple-50 to-red-50 dark:from-purple-900/20 dark:to-red-900/20 rounded-2xl p-8 mb-8 border-l-4 border-gradient-to-b border-purple-600 dark:border-purple-400">
                    <p class="text-xl md:text-2xl text-gray-700 dark:text-gray-300 leading-relaxed italic font-light">
                        {{ $noticia->excerptLimpio ?? Str::limit(strip_tags($noticia->contenido), 250) }}
                    </p>
                </div>
                
                <!-- Información del Autor y Estadísticas -->
                <div class="flex flex-wrap items-center justify-between text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-xl p-4 transition-colors duration-300">
                    <div class="flex items-center space-x-4">
                        <span class="flex items-center">
                            <i class="fas fa-user-edit mr-2 text-purple-600 dark:text-purple-400"></i>
                            Por <strong class="ml-1 text-gray-700 dark:text-gray-300">Redacción UHTV</strong>
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-eye mr-2 text-purple-600 dark:text-purple-400"></i>
                            <span id="view-count">{{ rand(100, 1000) }} lecturas</span>
                        </span>
                    </div>
                    <div class="flex items-center space-x-4 mt-2 md:mt-0">
                        <span class="flex items-center">
                            <i class="fas fa-clock mr-2 text-purple-600 dark:text-purple-400"></i>
                            Lectura: {{ ceil(str_word_count(strip_tags($noticia->contenido)) / 200) }} min
                        </span>
                    </div>
                </div>
            </header>

            <!-- Imagen Principal -->
            @if($noticia->imagen)
                <figure class="mb-12">
                    <div class="relative overflow-hidden rounded-2xl shadow-2xl">
                        <img src="{{ $imagenUrl }}" 
                             alt="{{ $noticia->titulo }}" 
                             class="w-full h-auto object-cover"
                             onerror="handleImageError(this)">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <figcaption class="text-gray-500 dark:text-gray-400 text-sm mt-4 text-center italic bg-gray-50 dark:bg-gray-800 rounded-lg p-3 transition-colors duration-300">
                        <i class="fas fa-camera mr-2"></i>{{ $noticia->titulo }}
                    </figcaption>
                </figure>
            @endif

            <!-- Contenido del Artículo -->
            <div class="prose prose-xl max-w-none mb-12 dark:prose-invert">
                <div class="text-gray-800 dark:text-gray-200 leading-relaxed article-content" style="font-size: 1.2rem; line-height: 1.9;">
                    {!! $noticia->contenidoSanitizado ?? nl2br(e($noticia->contenido)) !!}
                </div>
            </div>

            <!-- Video de YouTube -->
            @if($noticia->video_youtube)
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center transition-colors duration-300">
                        <i class="fab fa-youtube text-red-600 mr-2"></i>
                        Video Relacionado
                    </h3>
                    <div class="relative pb-[56.25%] rounded-lg overflow-hidden shadow-lg">
                        <iframe 
                            class="absolute top-0 left-0 w-full h-full"
                            src="https://www.youtube.com/embed/{{ $noticia->video_youtube }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                </div>
            @endif

            <!-- Tags/Etiquetas -->
            <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center space-x-2">
                    <span class="text-gray-600 dark:text-gray-300 font-medium">Etiquetas:</span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full text-sm transition-colors duration-300">
                        {{ $noticia->category->name ?? 'General' }}
                    </span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full text-sm transition-colors duration-300">
                        Noticias
                    </span>
                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full text-sm transition-colors duration-300">
                        UHTV
                    </span>
                </div>
            </div>

            <!-- Compartir Nuevamente - Diseño Mejorado -->
            <div class="bg-gradient-to-r from-purple-50 to-red-50 dark:from-purple-900/20 dark:to-red-900/20 rounded-2xl p-8 mb-12 border border-purple-100 dark:border-purple-800 transition-colors duration-300">
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4 transition-colors duration-300">¿Te gustó esta noticia?</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6 text-lg transition-colors duration-300">Compártela con tus amigos y familiares en redes sociales</p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($noticia->titulo) }}&url={{ urlencode(Request::url()) }}" 
                           target="_blank" 
                           class="bg-blue-400 hover:bg-blue-500 text-white px-6 py-3 rounded-xl transition-all duration-300 flex items-center font-semibold transform hover:-translate-y-1 shadow-lg">
                            <i class="fab fa-twitter mr-3 text-lg"></i>Compartir en Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}" 
                           target="_blank" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition-all duration-300 flex items-center font-semibold transform hover:-translate-y-1 shadow-lg">
                            <i class="fab fa-facebook mr-3 text-lg"></i>Compartir en Facebook
                        </a>
                        <a href="https://api.whatsapp.com/send?text={{ urlencode($noticia->titulo . ' ' . Request::url()) }}" 
                           target="_blank" 
                           class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl transition-all duration-300 flex items-center font-semibold transform hover:-translate-y-1 shadow-lg">
                            <i class="fab fa-whatsapp mr-3 text-lg"></i>Enviar por WhatsApp
                        </a>
                        <button onclick="copyToClipboard('{{ Request::url() }}')" 
                                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl transition-all duration-300 flex items-center font-semibold transform hover:-translate-y-1 shadow-lg">
                            <i class="fas fa-link mr-3 text-lg"></i>Copiar enlace
                        </button>
                    </div>
                </div>
            </div>

            <!-- Script para copiar al portapapeles -->
            <script>
                function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(function() {
                        // Mostrar notificación de éxito
                        const notification = document.createElement('div');
                        notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';
                        notification.innerHTML = '<i class="fas fa-check mr-2"></i>Enlace copiado al portapapeles';
                        document.body.appendChild(notification);
                        
                        setTimeout(() => {
                            notification.classList.remove('translate-x-full');
                        }, 100);
                        
                        setTimeout(() => {
                            notification.classList.add('translate-x-full');
                            setTimeout(() => {
                                document.body.removeChild(notification);
                            }, 300);
                        }, 3000);
                    }).catch(function(err) {
                        console.error('Error al copiar: ', err);
                    });
                }
            </script>
        </div>
    </div>
</article>

<!-- Banner Publicitario -->
@if(isset($banners['show_bottom']) && $banners['show_bottom']->count() > 0)
    <section class="py-8 bg-gray-50 dark:bg-gray-800 transition-colors duration-300">
        <div class="container mx-auto px-4">
            <div class="text-center mb-4">
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">PUBLICIDAD</span>
            </div>
            @foreach($banners['show_bottom'] as $banner)
                <div class="flex justify-center mb-6">
                    <a href="{{ $banner->link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="block max-w-4xl"> 
                        <img src="{{ asset($banner->image_path) }}" alt="{{ $banner->title }}" 
                             class="w-full h-auto rounded-lg shadow-lg hover:opacity-90 transition">
                    </a>
                </div>
            @endforeach
        </div>
    </section>
@endif

<!-- Noticias Relacionadas - Diseño Moderno -->
<section class="py-16 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 transition-colors duration-300">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4 transition-colors duration-300">Te Puede Interesar</h2>
                <div class="w-32 h-1 bg-gradient-to-r from-purple-600 to-red-600 mx-auto rounded-full"></div>
                <p class="text-gray-600 dark:text-gray-300 mt-4 text-lg transition-colors duration-300">Descubre más noticias relacionadas</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($noticias as $otraNoticia)
                    <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                        <div class="relative overflow-hidden">
                            <a href="{{ route('show', $otraNoticia->id) }}" class="block">
                                <img src="{{ $otraNoticia->imagenUrl ?? asset('images/default-news.svg') }}" 
                                     alt="{{ $otraNoticia->titulo }}" 
                                     class="w-full h-56 object-cover transition-transform duration-500 hover:scale-110"
                                     onerror="handleImageError(this)">
                            </a>
                            
                            <!-- Etiqueta de Categoría -->
                            <div class="absolute top-4 left-4">
                                <span class="bg-gradient-to-r from-purple-600 to-red-600 text-white px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide shadow-lg">
                                    {{ $otraNoticia->category->name ?? 'General' }}
                                </span>
                            </div>
                            
                            <!-- Overlay sutil -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Metadata -->
                            <div class="mb-4">
                                <span class="text-gray-500 dark:text-gray-400 text-sm flex items-center">
                                    <i class="fas fa-clock mr-2 text-purple-600 dark:text-purple-400"></i>
                                    {{ \Carbon\Carbon::parse($otraNoticia->created_at)->locale('es')->diffForHumans() }}
                                </span>
                            </div>
                            
                            <!-- Título y Contenido -->
                            <a href="{{ route('show', $otraNoticia->id) }}" class="block group">
                                <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-3 text-lg leading-tight line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300">
                                    {{ $otraNoticia->titulo }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 leading-relaxed transition-colors duration-300">
                                    {{ $otraNoticia->excerptLimpio ?? Str::limit(strip_tags($otraNoticia->contenido), 120) }}
                                </p>
                            </a>
                            
                            <!-- Botón de Acción -->
                            <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <a href="{{ route('show', $otraNoticia->id) }}" 
                                   class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 font-semibold text-sm transition-colors duration-300 group">
                                    Leer noticia
                                    <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform duration-300"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            
            <!-- Botón para Ver Más -->
            <div class="text-center mt-12">
                <a href="{{ route('portada') }}" class="inline-flex items-center bg-gradient-to-r from-purple-600 to-red-600 text-white px-8 py-4 rounded-full font-semibold text-lg hover:from-purple-700 hover:to-red-700 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                    <i class="fas fa-newspaper mr-3"></i>
                    Ver Más Noticias
                    <i class="fas fa-arrow-right ml-3"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
