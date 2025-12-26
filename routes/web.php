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

// Ruta para mostrar las noticias por categoría
Route::get('/categoria/{id}', [PortadaController::class, 'noticiasPorCategoria'])->name('categoria.noticias');

// Ruta para mostrar el detalle de una noticia
Route::get('/noticia/{id}', [PortadaController::class, 'show'])->name('show');

// Ruta para búsqueda de noticias
Route::get('/buscar', [PortadaController::class, 'search'])->name('search');

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
