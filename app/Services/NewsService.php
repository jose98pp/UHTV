<?php

namespace App\Services;

use App\Repositories\NoticiaRepository;
use App\Services\ImageValidationService;
use App\Services\ContentSanitizationService;
use App\Models\Category;
use App\Models\Noticia;
use Illuminate\Support\Facades\Cache;

class NewsService
{
    public function __construct(
        private NoticiaRepository $noticiaRepository,
        private ImageValidationService $imageValidationService,
        private ContentSanitizationService $contentSanitizationService
    ) {}

    /**
     * Obtener datos para la página principal
     */
    public function getHomePageData()
    {
        return Cache::remember('homepage_data', 300, function () {
            // Obtener una sola colección de noticias para carrusel y ticker.
            $ultimasNoticias = $this->noticiaRepository->getPublishedNews(12);

            // Obtener categorías con hasta cinco noticias ya cargadas en eager loading.
            $seccionesCategoria = $this->noticiaRepository->getCategoriesWithNews(5);

            // Procesar una sola vez cada noticia y reutilizar los resultados.
            $ultimasNoticias = $ultimasNoticias->map(function ($noticia) {
                return $this->processNewsItem($noticia);
            });
            $noticias = $ultimasNoticias->take(10);
            
            // Reutilizar la relación eager-loaded para las secciones de portada.
            // Se asigna también la categoría padre para que el acceso a la URL
            // en Blade no genere una consulta adicional por noticia.
            $noticiasPorCategoria = [];
            foreach ($seccionesCategoria as $categoria) {
                $noticiasCategoria = $categoria->noticias
                    ->take(5)
                    ->map(function ($noticia) use ($categoria) {
                        $noticia->setRelation('category', $categoria);
                        return $this->processNewsItem($noticia);
                    });

                $categoria->setRelation('noticias', $noticiasCategoria);
                $noticiasPorCategoria[$categoria->id] = $noticiasCategoria;
            }

            // Reutilizar la lista cacheada de categorías para la navegación.
            $categorias = Cache::remember('all_categories', 600, function () {
                return Category::all();
            });

            // Obtener "más leídas" (noticias más vistas)
            $masLeidas = $this->noticiaRepository->getMostViewedNews(4);
            $masLeidas = $masLeidas->map(function ($noticia) {
                return $this->processNewsItem($noticia);
            });
            
            return [
                'noticias' => $noticias,
                'seccionesCategoria' => $seccionesCategoria,
                'ultimasNoticias' => $ultimasNoticias,
                'noticiasPorCategoria' => $noticiasPorCategoria,
                'masLeidas' => $masLeidas,
                'categorias' => $categorias,
            ];
        });
    }

    /**
     * Obtener datos para página de categoría con paginación
     */
    public function getCategoryPageData($categoryId, $perPage = 10)
    {
        // Obtener la categoría
        $categoria = Category::findOrFail($categoryId);
        
        // Obtener noticias de la categoría con paginación
        $noticiasCategoria = Noticia::where('category_id', $categoryId)
            ->where('publicada', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
        
        // Procesar noticias de la categoría con contenido sanitizado e imágenes seguras
        $noticiasCategoria->getCollection()->transform(function ($noticia) {
            return $this->processNewsItem($noticia);
        });
        
        // Obtener noticias relacionadas (otras categorías)
        $noticias = $this->noticiaRepository->getNewsExcludingCategory($categoryId, 6);
        
        // Procesar noticias relacionadas con contenido sanitizado e imágenes seguras
        $noticias = $noticias->map(function ($noticia) {
            return $this->processNewsItem($noticia);
        });
        
        // Obtener todas las categorías para navegación
        $categorias = Cache::remember('all_categories', 600, function () {
            return Category::all();
        });

        return compact('categoria', 'noticiasCategoria', 'noticias', 'categorias');
    }

    /**
     * Obtener datos para página de detalle de noticia
     */
    public function getNewsDetailData($newsId)
    {
        $noticia = $this->noticiaRepository->getPublishedNewsById($newsId);
        
        // Procesar noticia principal con contenido sanitizado e imagen segura
        $noticia = $this->processNewsItem($noticia);
        $imagenUrl = $noticia->imagenUrl;
        
        // Obtener y procesar noticias relacionadas
        $noticias = $this->noticiaRepository->getRelatedNews($newsId, 5);
        $noticias = $noticias->map(function ($noticia) {
            return $this->processNewsItem($noticia);
        });
        
        $categorias = Cache::remember('all_categories', 600, function () {
            return Category::all();
        });

        return compact('noticia', 'noticias', 'categorias', 'imagenUrl');
    }

    /**
     * Obtener URL segura de imagen (método legacy - usa ImageValidationService internamente)
     * 
     * @deprecated Usar ImageValidationService::getImageUrlOrDefault() directamente
     */
    public function getSecureImageUrl($imagePath)
    {
        return $this->imageValidationService->getImageUrlOrDefault($imagePath);
    }

    /**
     * Buscar noticias
     */
    public function searchNews($term, $limit = 10)
    {
        if (empty(trim($term))) {
            return collect();
        }

        return $this->noticiaRepository->searchNews($term, $limit);
    }

    /**
     * Obtener estadísticas del dashboard
     */
    public function getDashboardStats()
    {
        try {
            return Cache::remember('dashboard_stats', 300, function () {
                return $this->noticiaRepository->getNewsStats();
            });
        } catch (\Exception $e) {
            return [
                'total_published' => 0,
                'total_draft' => 0,
                'total_categories' => 0,
                'recent_news' => 0
            ];
        }
    }

    /**
     * Get sanitized content for display
     */
    public function getSanitizedContent(?string $content): string
    {
        if (empty($content)) {
            return '';
        }
        
        return $this->contentSanitizationService->processRichTextContent($content);
    }
    
    /**
     * Get clean excerpt for previews
     */
    public function getCleanExcerpt(?string $content, int $length = 200): string
    {
        if (empty($content)) {
            return '';
        }
        
        return $this->contentSanitizationService->getCleanExcerpt($content, $length);
    }
    
    /**
     * Process news item with sanitized content and secure image URL
     */
    public function processNewsItem($noticia): object
    {
        $noticia->imagenUrl = $this->imageValidationService->getImageUrlOrDefault($noticia->imagen);
        $noticia->contenidoSanitizado = $this->getSanitizedContent($noticia->contenido ?? '');
        $noticia->excerptLimpio = $this->getCleanExcerpt($noticia->contenido ?? '', 120);
        return $noticia;
    }
    
    /**
     * Limpiar caché relacionado con noticias
     */
    public function clearNewsCache()
    {
        Cache::forget('homepage_data');
        Cache::forget('all_categories');
        Cache::forget('dashboard_stats');
        
        // Limpiar caché de categorías específicas si es necesario
        $categories = Category::all();
        foreach ($categories as $category) {
            Cache::forget("category_{$category->id}_data");
        }
    }
}