<?php

namespace App\Http\Controllers;

use App\Models\Transmision;
use Illuminate\Http\Request;

class TransmisionPublicController extends Controller
{
    /**
     * Página principal de UHTV En Vivo, Podcasts y Clips
     */
    public function index(Request $request)
    {
        $selectedTipo = $request->get('tipo', 'todos');
        $selectedPlataforma = $request->get('plataforma', 'todas');

        // 1. Obtener transmisión activa para el reproductor principal (Cinema Player)
        if ($selectedTipo === 'en_vivo') {
            $activeStream = Transmision::enVivo()->first();
        } elseif (in_array($selectedTipo, ['podcast', 'clip', 'programa'])) {
            $activeStream = Transmision::activos()->where('tipo', $selectedTipo)->orderBy('destacado', 'desc')->latest('fecha_transmision')->first();
        } else {
            // Todos: priorizar si hay emisión en vivo activa; sino el último destacado o reciente
            $activeStream = Transmision::enVivo()->first() ?? Transmision::activos()->orderBy('destacado', 'desc')->latest('created_at')->first();
        }

        // 2. Filtros para la galería
        $query = Transmision::activos();

        if ($selectedTipo !== 'todos' && in_array($selectedTipo, ['en_vivo', 'podcast', 'clip', 'programa'])) {
            $query->where('tipo', $selectedTipo);
        }

        if ($selectedPlataforma !== 'todas' && in_array($selectedPlataforma, ['youtube', 'facebook', 'tiktok', 'twitch'])) {
            $query->where('plataforma', $selectedPlataforma);
        }

        $transmisiones = $query->orderBy('en_vivo', 'desc')
                               ->orderBy('destacado', 'desc')
                               ->latest('fecha_transmision')
                               ->paginate(12)
                               ->withQueryString();

        // 3. Obtener colecciones específicas por tipo para carruseles o secciones
        $podcasts = Transmision::podcasts()->latest('fecha_transmision')->take(6)->get();
        $clips = Transmision::clips()->latest('fecha_transmision')->take(6)->get();
        $programas = Transmision::programas()->latest('fecha_transmision')->take(6)->get();

        return view('transmisiones.index', compact(
            'activeStream',
            'transmisiones',
            'podcasts',
            'clips',
            'programas',
            'selectedTipo',
            'selectedPlataforma'
        ));
    }

    /**
     * Obtener datos de una transmisión en formato JSON (para cambio dinámico en Modal/Reproductor)
     */
    public function showJson($id)
    {
        $transmision = Transmision::activos()->findOrFail($id);

        // Incrementar vistas
        $transmision->increment('views');

        return response()->json([
            'id' => $transmision->id,
            'titulo' => $transmision->titulo,
            'descripcion' => $transmision->descripcion,
            'embed_url' => $transmision->embed_url,
            'url' => $transmision->url,
            'plataforma' => $transmision->plataforma,
            'plataforma_nombre' => $transmision->plataforma_nombre,
            'plataforma_icon' => $transmision->plataforma_icon,
            'plataforma_color' => $transmision->plataforma_color,
            'tipo' => $transmision->tipo,
            'tipo_nombre' => $transmision->tipo_nombre,
            'en_vivo' => $transmision->en_vivo,
            'duracion' => $transmision->duracion,
            'fecha' => $transmision->fecha_transmision ? $transmision->fecha_transmision->diffForHumans() : '',
        ]);
    }
}
