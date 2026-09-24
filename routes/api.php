<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImageUploadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Compatibilidad con editores que todavía usan la ruta /api/upload-image.
// Se conserva la URL, pero se carga el stack web para usar la sesión del
// administrador, cookies y verificación CSRF.
Route::middleware(['web', 'auth', 'admin'])
    ->post('/upload-image', [ImageUploadController::class, 'upload'])
    ->name('legacy.images.upload');
