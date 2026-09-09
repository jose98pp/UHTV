<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transmision;
use App\Services\StreamingEmbedService;
use Illuminate\Http\Request;

class TransmisionController extends Controller
{
    public function __construct(
        private StreamingEmbedService $embedService
    ) {}

    /**
     * Listado de transmisiones, podcasts y clips
     */
    public function index(Request $request)
    {
        $query = Transmision::query();

        // Filtro por búsqueda
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'LIKE', "%{$search}%")
                  ->orWhere('descripcion', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por plataforma
        if ($request->filled('plataforma')) {
            $query->where('plataforma', $request->plataforma);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            if ($request->estado === 'en_vivo') {
                $query->where('en_vivo', true);
            } elseif ($request->estado === 'activo') {
                $query->where('activo', true);
            } elseif ($request->estado === 'inactivo') {
                $query->where('activo', false);
            }
        }

        $transmisiones = $query->orderBy('en_vivo', 'desc')
                               ->orderBy('destacado', 'desc')
                               ->orderBy('created_at', 'desc')
                               ->paginate(15)
                               ->withQueryString();

        $stats = [
            'total' => Transmision::count(),
            'en_vivo' => Transmision::where('en_vivo', true)->count(),
            'podcasts' => Transmision::where('tipo', 'podcast')->count(),
            'clips' => Transmision::where('tipo', 'clip')->count(),
        ];

        return view('admin.transmisiones.index', compact('transmisiones', 'stats'));
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        return view('admin.transmisiones.create');
    }

    /**
     * Almacenar nueva transmisión o video
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:en_vivo,podcast,clip,programa',
            'plataforma' => 'required|in:youtube,facebook,tiktok,twitch,otro',
            'url' => 'required|string',
            'descripcion' => 'nullable|string',
            'thumbnail_url' => 'nullable|string|max:500',
            'duracion' => 'nullable|string|max:50',
            'fecha_transmision' => 'nullable|date',
        ]);

        $validated['en_vivo'] = $request->boolean('en_vivo');
        $validated['activo'] = $request->boolean('activo', true);
        $validated['destacado'] = $request->boolean('destacado');

        // Si se marca como "en vivo", desactivar otras transmisiones en vivo previas
        if ($validated['en_vivo']) {
            Transmision::where('en_vivo', true)->update(['en_vivo' => false]);
        }

        $transmision = Transmision::create($validated);

        return redirect()->route('admin.transmisiones.index')
            ->with('success', 'Transmisión registrada con éxito.' . ($transmision->en_vivo ? ' ¡Actualmente EN VIVO en el portal!' : ''));
    }

    /**
     * Formulario de edición
     */
    public function edit($id)
    {
        $transmision = Transmision::findOrFail($id);
        return view('admin.transmisiones.edit', compact('transmision'));
    }

    /**
     * Actualizar transmisión
     */
    public function update(Request $request, $id)
    {
        $transmision = Transmision::findOrFail($id);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|in:en_vivo,podcast,clip,programa',
            'plataforma' => 'required|in:youtube,facebook,tiktok,twitch,otro',
            'url' => 'required|string',
            'descripcion' => 'nullable|string',
            'thumbnail_url' => 'nullable|string|max:500',
            'duracion' => 'nullable|string|max:50',
            'fecha_transmision' => 'nullable|date',
        ]);

        $validated['en_vivo'] = $request->boolean('en_vivo');
        $validated['activo'] = $request->boolean('activo', true);
        $validated['destacado'] = $request->boolean('destacado');

        // Si se marca como en vivo y antes no lo estaba, desmarcar otras
        if ($validated['en_vivo'] && !$transmision->en_vivo) {
            Transmision::where('id', '!=', $id)->where('en_vivo', true)->update(['en_vivo' => false]);
        }

        $transmision->update($validated);

        return redirect()->route('admin.transmisiones.index')
            ->with('success', 'Transmisión actualizada exitosamente.');
    }

    /**
     * Eliminar transmisión
     */
    public function destroy($id)
    {
        $transmision = Transmision::findOrFail($id);
        $transmision->delete();

        return redirect()->route('admin.transmisiones.index')
            ->with('success', 'Transmisión eliminada correctamente.');
    }

    /**
     * Alternar estado "En Vivo Ahora"
     */
    public function toggleLive($id)
    {
        $transmision = Transmision::findOrFail($id);
        
        if ($transmision->en_vivo) {
            $transmision->en_vivo = false;
            $transmision->save();
            $message = "Transmisión '{$transmision->titulo}' marcada como FINALIZADA.";
        } else {
            // Desactivar cualquier otra transmisión activa
            Transmision::where('id', '!=', $id)->where('en_vivo', true)->update(['en_vivo' => false]);
            $transmision->en_vivo = true;
            $transmision->activo = true; // Asegurar que esté activa
            $transmision->save();
            $message = "Transmisión '{$transmision->titulo}' ¡ACTIVADA EN VIVO AHORA!";
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'en_vivo' => $transmision->en_vivo,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Alternar estado Activo/Inactivo
     */
    public function toggleActive($id)
    {
        $transmision = Transmision::findOrFail($id);
        $transmision->activo = !$transmision->activo;
        
        // Si se desactiva y estaba en vivo, apagar vivo
        if (!$transmision->activo && $transmision->en_vivo) {
            $transmision->en_vivo = false;
        }

        $transmision->save();

        $message = $transmision->activo 
            ? "Transmisión habilitada correctamente."
            : "Transmisión deshabilitada y oculta en el portal.";

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'activo' => $transmision->activo,
                'message' => $message,
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Endpoint API para previsualizar el reproductor en tiempo real desde el formulario
     */
    public function preview(Request $request)
    {
        $url = $request->input('url', '');
        $platform = $request->input('plataforma');

        if (empty($url)) {
            return response()->json(['success' => false, 'message' => 'URL requerida']);
        }

        $detectedPlatform = $this->embedService->detectPlatform($url);
        $embedUrl = $this->embedService->generateEmbedUrl($url, $platform ?: $detectedPlatform);
        $thumbnailUrl = $this->embedService->generateThumbnailUrl($url, $platform ?: $detectedPlatform);

        return response()->json([
            'success' => true,
            'detected_platform' => $detectedPlatform,
            'embed_url' => $embedUrl,
            'thumbnail_url' => $thumbnailUrl,
        ]);
    }
}
