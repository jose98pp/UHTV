<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NewsService;
use App\Models\Noticia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private NewsService $newsService
    ) {}

    public function index()
    {
        // Obtener estadísticas
        $stats = $this->newsService->getDashboardStats();
        
        // Obtener noticias recientes
        $recentNews = Noticia::with('category')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recentNews'));
    }
}
