<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar Repository
        $this->app->bind(
            \App\Repositories\NoticiaRepository::class,
            \App\Repositories\NoticiaRepository::class
        );

        // Registrar Service
        $this->app->bind(
            \App\Services\NewsService::class,
            \App\Services\NewsService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartir banners con todas las vistas (solo en peticiones web, no en consola/tests)
        if (!$this->app->runningInConsole()) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('banners')) {
                    $banners = \App\Models\Banner::active()
                        ->orderBy('position')
                        ->get()
                        ->groupBy('location');
                    
                    \Illuminate\Support\Facades\View::share('banners', $banners);
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('transmisiones')) {
                    $liveStream = \App\Models\Transmision::enVivo()->first();
                    $recentStreams = \App\Models\Transmision::activos()->where('en_vivo', false)->latest('fecha_transmision')->take(8)->get();

                    \Illuminate\Support\Facades\View::share('transmisionEnVivo', $liveStream);
                    \Illuminate\Support\Facades\View::share('transmisionesRecientes', $recentStreams);
                }
            } catch (\Exception $e) {
                // Si falla (ej. durante migración), no detener la app
                \Illuminate\Support\Facades\Log::error('Error loading shared data: ' . $e->getMessage());
                \Illuminate\Support\Facades\View::share('banners', collect());
                \Illuminate\Support\Facades\View::share('transmisionEnVivo', null);
                \Illuminate\Support\Facades\View::share('transmisionesRecientes', collect());
            }
        }

        // View Composer para transmisiones en el frontend (layouts.main, portada, modal, etc.)
        \Illuminate\Support\Facades\View::composer(['layouts.main', 'portada', 'partials.live-modal', 'transmisiones.*'], function ($view) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('transmisiones')) {
                    $liveStream = \App\Models\Transmision::enVivo()->first();
                    $recentStreams = \App\Models\Transmision::activos()->where('en_vivo', false)->latest('fecha_transmision')->take(8)->get();

                    $view->with([
                        'transmisionEnVivo' => $liveStream,
                        'transmisionesRecientes' => $recentStreams,
                    ]);
                } else {
                    $view->with([
                        'transmisionEnVivo' => null,
                        'transmisionesRecientes' => collect(),
                    ]);
                }
            } catch (\Exception $e) {
                $view->with([
                    'transmisionEnVivo' => null,
                    'transmisionesRecientes' => collect(),
                ]);
            }
        });

        // View Composer para el panel de administración (layouts.admin y vistas de admin)
        \Illuminate\Support\Facades\View::composer(['layouts.admin', 'admin.*'], function ($view) {
            try {
                $user = auth()->user();
                $isAdmin = $user && ($user->role === 'admin' || $user->role === 'superadmin');

                // Borradores pendientes
                $draftsCount = \App\Models\Noticia::where('publicada', false)->count();

                // Noticia más leída / destacada
                $popularNews = \App\Models\Noticia::where('publicada', true)->orderBy('views', 'desc')->first();

                // Noticias recientes publicadas (últimas 3)
                $recentNews = \App\Models\Noticia::with('category')->where('publicada', true)->latest()->take(3)->get();

                // Banners publicitarios activos
                $activeBannersCount = 0;
                if (\Illuminate\Support\Facades\Schema::hasTable('banners')) {
                    $activeBannersCount = \App\Models\Banner::active()->count();
                }

                // Estadísticas globales para admin
                $totalNewsCount = $isAdmin ? \App\Models\Noticia::count() : ($user ? $user->noticias()->count() : 0);
                $publishedNewsCount = $isAdmin ? \App\Models\Noticia::where('publicada', true)->count() : ($user ? $user->noticias()->where('publicada', true)->count() : 0);

                $notifications = collect();

                if ($draftsCount > 0) {
                    $notifications->push([
                        'id' => 'drafts',
                        'type' => 'warning',
                        'icon' => 'fas fa-file-signature',
                        'title' => 'Noticias en borrador',
                        'message' => "Hay {$draftsCount} noticias pendientes de publicar",
                        'time' => 'Pendiente',
                        'url' => route('admin.noticias.index') . '?status=draft',
                    ]);
                }

                if ($popularNews) {
                    $notifications->push([
                        'id' => 'popular_news',
                        'type' => 'primary',
                        'icon' => 'fas fa-fire',
                        'title' => 'Noticia más leída (' . number_format($popularNews->views) . ' vistas)',
                        'message' => \Illuminate\Support\Str::limit($popularNews->titulo, 50),
                        'time' => 'Tendencia',
                        'url' => route('admin.noticias.edit', $popularNews->id),
                    ]);
                }

                foreach ($recentNews as $rn) {
                    $notifications->push([
                        'id' => 'news_' . $rn->id,
                        'type' => 'info',
                        'icon' => 'fas fa-newspaper',
                        'title' => 'Noticia publicada',
                        'message' => \Illuminate\Support\Str::limit($rn->titulo, 50),
                        'time' => $rn->created_at ? $rn->created_at->diffForHumans() : 'Hoy',
                        'url' => route('admin.noticias.edit', $rn->id),
                    ]);
                }

                if ($activeBannersCount > 0) {
                    $notifications->push([
                        'id' => 'banners',
                        'type' => 'success',
                        'icon' => 'fas fa-ad',
                        'title' => 'Publicidad activa',
                        'message' => "{$activeBannersCount} banners publicitarios en línea",
                        'time' => 'En línea',
                        'url' => route('admin.banners.index'),
                    ]);
                }

                // Notificación y conteo de transmisiones en vivo
                $liveStreamCount = 0;
                if (\Illuminate\Support\Facades\Schema::hasTable('transmisiones')) {
                    $liveStreamCount = \App\Models\Transmision::where('en_vivo', true)->count();
                    if ($liveStreamCount > 0) {
                        $liveTrans = \App\Models\Transmision::where('en_vivo', true)->first();
                        $notifications->push([
                            'id' => 'transmision_live',
                            'type' => 'danger',
                            'icon' => 'fas fa-broadcast-tower',
                            'title' => '¡Transmisión EN VIVO Activa!',
                            'message' => \Illuminate\Support\Str::limit($liveTrans->titulo, 50),
                            'time' => 'En directo',
                            'url' => route('admin.transmisiones.index'),
                        ]);
                    }
                }

                $view->with([
                    'adminNotifications' => $notifications,
                    'headerTotalNews' => $totalNewsCount,
                    'headerPublishedNews' => $publishedNewsCount,
                    'headerLiveStreamCount' => $liveStreamCount,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error in Admin View Composer: ' . $e->getMessage());
                $view->with([
                    'adminNotifications' => collect(),
                    'headerTotalNews' => 0,
                    'headerPublishedNews' => 0,
                    'headerLiveStreamCount' => 0,
                ]);
            }
        });
    }
}
