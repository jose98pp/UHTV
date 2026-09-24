<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\Category;
use App\Models\Transmision;
use App\Services\NewsService;
use Illuminate\Http\Request;

class PortadaController extends Controller
{
    public function __construct(
        private NewsService $newsService
    ) {}

    public function index(Request $request)
    {
        $data = $this->newsService->getHomePageData();

        try {
            $data['multimediaPortada'] = Transmision::paraPortada()
                ->take(12)
                ->get();
        } catch (\Throwable $e) {
            // La portada debe seguir funcionando si la tabla multimedia no está disponible.
            $data['multimediaPortada'] = collect();
        }
        
        return view('portada', $data);
    }

    

    /**
     * Mostrar el detalle de una noticia con URL amigable /{categoria}/{slug}_{id}
     */
    public function show($category, $slug = null)
    {
        // Si se llama con un solo parámetro numérico (compatibilidad)
        if ($slug === null && is_numeric($category)) {
            return $this->legacyShow($category);
        }

        // Extraer el ID al final del slug: {slug}_{id}
        if (!preg_match('/_(\d+)$/', (string)$slug, $matches)) {
            abort(404);
        }

        $id = (int)$matches[1];

        $noticia = Noticia::with('category')
            ->where('id', $id)
            ->where('publicada', true)
            ->firstOrFail();

        // Verificación canónica: Si la categoría o el slug cambiaron, redirigir 301 a la URL canónica
        // Usamos redirect()->route() para respetar el host y esquema actual (local o producción)
        if ($category !== $noticia->category_slug || $slug !== $noticia->slug_with_id) {
            return redirect()->route('show', [
                'category' => $noticia->category_slug,
                'slug' => $noticia->slug_with_id,
            ], 301);
        }

        // Incrementar contador de vistas
        $noticia->increment('views');

        $data = $this->newsService->getNewsDetailData($id);

        return view('show', $data);
    }

    /**
     * Redirección 301 permanente para URLs antiguas /noticia/{id}
     */
    public function legacyShow($id)
    {
        $noticia = Noticia::with('category')->where('id', $id)->firstOrFail();
        return redirect()->route('show', [
            'category' => $noticia->category_slug,
            'slug' => $noticia->slug_with_id,
        ], 301);
    }

    /**
     * Mostrar noticias por categoría /{categoria}
     */
    public function noticiasPorCategoria($category, Request $request)
    {
        // 1. Buscar por coincidencia exacta en slug
        $categoria = Category::where('slug', $category)->first();

        // 2. Si no se encuentra, buscar por slug generado del nombre o por nombre insensible a mayúsculas
        if (!$categoria) {
            $normalizedCategory = \Illuminate\Support\Str::slug($category);
            $categoria = Category::all()->first(function ($cat) use ($category, $normalizedCategory) {
                return $cat->slug === $category
                    || $cat->slug === $normalizedCategory
                    || \Illuminate\Support\Str::slug($cat->name) === $normalizedCategory
                    || \Illuminate\Support\Str::lower($cat->name) === \Illuminate\Support\Str::lower($category);
            });

            // Si se encontró pero su slug en la BD estaba vacío, auto-repararlo inmediatamente
            if ($categoria) {
                if (empty($categoria->slug)) {
                    $categoria->slug = \Illuminate\Support\Str::slug($categoria->name);
                    $categoria->saveQuietly();
                }

                // Redirigir 301 al slug canónico si difiere del solicitado
                if ($categoria->slug && $category !== $categoria->slug) {
                    return redirect()->route('categoria.noticias', ['category' => $categoria->slug], 301);
                }
            }
        }

        // 3. Si se accede por ID numérico en la ruta /{id}, redirigir 301 a /{slug}
        if (!$categoria && is_numeric($category)) {
            $categoria = Category::find($category);
            if ($categoria) {
                $slug = $categoria->slug ?: \Illuminate\Support\Str::slug($categoria->name);
                if (empty($categoria->slug)) {
                    $categoria->slug = $slug;
                    $categoria->saveQuietly();
                }
                return redirect()->route('categoria.noticias', ['category' => $slug], 301);
            }
        }

        // 4. Si la categoría tiene un slug canónico diferente (ej. URLs antiguas con -1), redirigir 301
        if ($categoria && $categoria->slug && $category !== $categoria->slug) {
            return redirect()->route('categoria.noticias', ['category' => $categoria->slug], 301);
        }

        if (!$categoria) {
            abort(404);
        }

        try {
            $perPage = $request->get('per_page', 10);
            $data = $this->newsService->getCategoryPageData($categoria->id, $perPage);
            
            // Verificar que tenemos los datos necesarios
            if (!isset($data['categoria']) || !isset($data['noticiasCategoria']) || !isset($data['categorias'])) {
                \Log::error('Datos faltantes en getCategoryPageData', $data);
                return redirect()->route('portada')->with('error', 'Error al cargar la categoría.');
            }
            
            return view('categoria.noticias', $data);
            
        } catch (\Exception $e) {
            \Log::error('Error en noticiasPorCategoria', [
                'category' => $category,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('portada')->with('error', 'La categoría solicitada no existe.');
        }
    }

    /**
     * Redirección 301 permanente para URLs antiguas /categoria/{id}
     */
    public function legacyCategory($id)
    {
        $categoria = Category::findOrFail($id);
        $slug = $categoria->slug ?: \Illuminate\Support\Str::slug($categoria->name);
        return redirect()->route('categoria.noticias', ['category' => $slug], 301);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $perPage = $request->get('per_page', 12);
        
        if (empty($query)) {
            return redirect()->route('portada')->with('error', 'Por favor ingresa un término de búsqueda.');
        }

        // Buscar noticias
        $noticias = Noticia::where('publicada', true)
            ->where(function ($q) use ($query) {
                $q->where('titulo', 'LIKE', "%{$query}%")
                  ->orWhere('contenido', 'LIKE', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Procesar noticias con el servicio
        $noticias->getCollection()->transform(function ($noticia) {
            return $this->newsService->processNewsItem($noticia);
        });

        // Obtener categorías para el layout
        $categorias = Category::all();

        return view('search', [
            'noticias' => $noticias,
            'categorias' => $categorias,
            'query' => $query,
            'total' => $noticias->total()
        ]);
    }
}
