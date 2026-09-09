<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\NoticiaController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PortadaController;
use App\Http\Controllers\ProfileController;

// ---------------------------------
// Rutas Públicas (Sin autenticación)
// ---------------------------------

// Ruta principal (Portada)
Route::get('/', [PortadaController::class, 'index'])->name('portada');

// Redirecciones 301 legacy para mantener SEO y compatibilidad con enlaces antiguos
Route::get('/noticia/{id}', [PortadaController::class, 'legacyShow'])->where('id', '[0-9]+')->name('noticia.legacy');
Route::get('/categoria/{id}', [PortadaController::class, 'legacyCategory'])->where('id', '[0-9]+')->name('categoria.legacy');

// Ruta para búsqueda de noticias
Route::get('/buscar', [PortadaController::class, 'search'])->name('search');

// Rutas para Transmisiones En Vivo, Podcasts y Clips
Route::get('/en-vivo', [\App\Http\Controllers\TransmisionPublicController::class, 'index'])->name('transmisiones.en-vivo');
Route::get('/transmisiones/data/{id}', [\App\Http\Controllers\TransmisionPublicController::class, 'showJson'])->name('transmisiones.json');

// Ruta de prueba para imágenes (solo en desarrollo)
if (app()->environment('local')) {
    Route::get('/test-images', function() {
        $imageService = app(\App\Services\ImageValidationService::class);
        
        return response()->json([
            'default_image_info' => $imageService->getImageInfo(null),
            'storage_link_exists' => is_link(public_path('storage')),
            'images_directory_exists' => is_dir(public_path('images')),
            'default_svg_exists' => file_exists(public_path('images/default-news.svg')),
        ]);
    });
}

// Dashboard para usuarios normales (redirige a portada)
Route::get('/dashboard', function () {
    return redirect()->route('portada');
})->middleware(['auth'])->name('dashboard');

// ---------------------------------
// Rutas de Administrador (Protegidas por autenticación y middleware)
// ---------------------------------

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard del administrador
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas del perfil del administrador
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
    Route::patch('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Rutas para el CRUD de noticias
    Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias.index');
    Route::get('/noticias/filter', [NoticiaController::class, 'filter'])->name('noticias.filter');
    Route::get('/noticias/create', [NoticiaController::class, 'create'])->name('noticias.create');
    Route::post('/noticias', [NoticiaController::class, 'store'])->name('noticias.store');
    Route::get('/noticias/{id}/edit', [NoticiaController::class, 'edit'])->name('noticias.edit');
    Route::put('/noticias/{id}', [NoticiaController::class, 'update'])->name('noticias.update');
    Route::delete('/noticias/{id}', [NoticiaController::class, 'destroy'])->name('noticias.destroy');

    // Rutas para el CRUD de categorías
    Route::resource('categorias', CategoryController::class);

    // Rutas para el CRUD de banners
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);

    // Rutas para el CRUD de transmisiones en vivo, podcasts y clips
    Route::post('transmisiones/preview', [\App\Http\Controllers\Admin\TransmisionController::class, 'preview'])->name('transmisiones.preview');
    Route::post('transmisiones/{id}/toggle-live', [\App\Http\Controllers\Admin\TransmisionController::class, 'toggleLive'])->name('transmisiones.toggle-live');
    Route::post('transmisiones/{id}/toggle-active', [\App\Http\Controllers\Admin\TransmisionController::class, 'toggleActive'])->name('transmisiones.toggle-active');
    Route::resource('transmisiones', \App\Http\Controllers\Admin\TransmisionController::class);

    // Ruta para cerrar sesión (logout)
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// ---------------------------------
// Ruta de Login exclusivo para administrador
// ---------------------------------

// Rutas de login para admin
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthenticatedSessionController::class, 'store'])->name('admin.login.store');
});
    
Route::middleware('auth')->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Incluye las rutas de autenticación generadas automáticamente por Laravel
require __DIR__ . '/auth.php';

// ---------------------------------
// Rutas Públicas SEO para Noticias y Categorías
// Colocadas al final para no colisionar con rutas estáticas ni de administración
// ---------------------------------

// Detalle de noticia con URL amigable: /{categoria}/{slug}_{id} (ej: /pais/gobierno-anuncia-nuevas-medidas_125)
Route::get('/{category}/{slug}', [PortadaController::class, 'show'])
    ->where('slug', '.*_[0-9]+')
    ->name('show');

// Noticias por categoría con URL amigable: /{categoria} (ej: /pais, /politica, /economia)
Route::get('/{category}', [PortadaController::class, 'noticiasPorCategoria'])
    ->name('categoria.noticias');
