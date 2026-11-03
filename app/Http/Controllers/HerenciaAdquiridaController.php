<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Transmision;
use App\Services\YouTubeChannelFeedService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HerenciaAdquiridaController extends Controller
{
    public function __construct(
        private YouTubeChannelFeedService $youTubeService
    ) {}

    /**
     * Vista principal de la sección "Herencia Adquirida"
     * Centraliza todos los videos, podcasts, clips y transmisiones en vivo.
     */
    public function index(Request $request)
    {
        $selectedTipo = $request->get('tipo', 'todos');
        $searchQuery = trim($request->get('q', ''));
        $selectedVideoId = $request->get('v');
        $selectedYouTubeId = $request->get('yt');

        // 1. Categorías para la barra de navegación del layout
        $categorias = Category::orderBy('name')->get();

        // 2. Transmisión en vivo activa (si existe)
        $liveStream = null;
        try {
            $liveStream = Transmision::enVivo()->first();
        } catch (\Throwable $e) {
            $liveStream = null;
        }

        // 3. Obtener videos del feed oficial de YouTube
        $youtubeVideos = $this->youTubeService->latestVideos(20);

        // 4. Obtener transmisiones y podcasts registrados en la base de datos
        $dbItemsQuery = Transmision::activos();

        if ($searchQuery !== '') {
            $dbItemsQuery->where(function ($q) use ($searchQuery) {
                $q->where('titulo', 'LIKE', "%{$searchQuery}%")
                  ->orWhere('descripcion', 'LIKE', "%{$searchQuery}%");
            });
        }

        if ($selectedTipo !== 'todos' && in_array($selectedTipo, ['en_vivo', 'podcast', 'programa', 'clip'], true)) {
            $dbItemsQuery->where('tipo', $selectedTipo);
        }

        $dbItems = $dbItemsQuery->orderBy('en_vivo', 'desc')
                                ->orderBy('destacado', 'desc')
                                ->latest('fecha_transmision')
                                ->get();

        // 5. Evitar duplicar videos de YouTube si ya fueron registrados manualmente por el admin
        $registeredYoutubeIds = $dbItems->map(fn ($item) => $this->youTubeService->extractVideoId($item->url ?? ''))
                                        ->filter()
                                        ->all();

        $uniqueYoutubeVideos = $youtubeVideos->reject(function ($yt) use ($registeredYoutubeIds) {
            return in_array($yt->youtube_id ?? '', $registeredYoutubeIds, true);
        });

        // Filtrar videos de YouTube por búsqueda y tipo si corresponde
        if ($searchQuery !== '') {
            $uniqueYoutubeVideos = $uniqueYoutubeVideos->filter(function ($yt) use ($searchQuery) {
                return Str::contains(Str::lower($yt->titulo ?? ''), Str::lower($searchQuery))
                    || Str::contains(Str::lower($yt->descripcion ?? ''), Str::lower($searchQuery));
            });
        }

        if ($selectedTipo === 'podcast') {
            // Los videos de YouTube se consideran videos/programas/clips; en podcasts solo mostramos los explícitos
            $filteredYoutube = collect();
        } elseif ($selectedTipo === 'en_vivo') {
            // Solo en vivo explícitos
            $filteredYoutube = collect();
        } else {
            $filteredYoutube = $uniqueYoutubeVideos;
        }

        // 6. Colección unificada de contenidos
        // Prioridad: transmisiones en vivo primero, luego contenidos destacados de BD, luego videos de YouTube y resto de BD
        $allContents = $dbItems->where('en_vivo', true)
            ->concat($dbItems->where('en_vivo', false)->where('destacado', true))
            ->concat($filteredYoutube)
            ->concat($dbItems->where('en_vivo', false)->where('destacado', false))
            ->unique(function ($item) {
                return isset($item->id) ? 'db_' . $item->id : 'yt_' . ($item->youtube_id ?? uniqid());
            })
            ->values();

        // 7. Determinar el video o emisión a reproducir en el Cinema Player principal
        $activeStream = null;

        if ($selectedVideoId) {
            $activeStream = $dbItems->firstWhere('id', (int) $selectedVideoId);
        } elseif ($selectedYouTubeId) {
            $activeStream = $uniqueYoutubeVideos->firstWhere('youtube_id', $selectedYouTubeId);
        }

        if (!$activeStream) {
            // Si hay emisión en vivo activa, es el foco principal del Cinema Player
            $activeStream = $liveStream ?? $allContents->first();
        }

        // 8. Contadores estadísticos para las pestañas de filtro
        $allDbItems = Transmision::activos()->get();
        $counts = [
            'todos' => $allDbItems->count() + $uniqueYoutubeVideos->count(),
            'en_vivo' => $allDbItems->where('en_vivo', true)->count(),
            'podcast' => $allDbItems->where('tipo', 'podcast')->count(),
            'programa' => $allDbItems->where('tipo', 'programa')->count() + $uniqueYoutubeVideos->count(),
            'clip' => $allDbItems->where('tipo', 'clip')->count(),
        ];

        return view('herencia.index', compact(
            'categorias',
            'activeStream',
            'liveStream',
            'allContents',
            'selectedTipo',
            'searchQuery',
            'counts'
        ));
    }
}
