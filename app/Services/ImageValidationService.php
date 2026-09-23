<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageValidationService
{
    /**
     * Imagen por defecto cuando no se encuentra la imagen original
     */
    private const DEFAULT_IMAGE = 'images/default-news.svg';

    /**
     * Extensiones de imagen permitidas
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    /**
     * Validar si la ruta de imagen existe y es válida
     *
     * @param string|null $imagePath
     * @return bool
     */
    public function validateImagePath(?string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }

        // Si es una URL externa (ej: https://...), es válida
        if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
            return true;
        }

        // Intentar resolver en disco público o subcarpetas de categorías
        $resolved = \App\Helpers\ImageUrlHelper::resolveImagePath($imagePath);
        if (!$resolved) {
            return false;
        }

        // Verificar que sea un archivo con extensión permitida
        $extension = strtolower(pathinfo($resolved, PATHINFO_EXTENSION));
        return in_array($extension, self::ALLOWED_EXTENSIONS);
    }

    /**
     * Obtener URL de imagen o imagen por defecto como fallback
     *
     * @param string|null $imagePath
     * @return string
     */
    public function getImageUrlOrDefault(?string $imagePath): string
    {
        return \App\Helpers\ImageUrlHelper::getImageUrl($imagePath);
    }

    /**
     * Obtener URL de versión WebP si existe, o null
     */
    public function getWebpUrlIfExists(?string $imagePath): ?string
    {
        if (empty($imagePath)) {
            return null;
        }

        $resolved = \App\Helpers\ImageUrlHelper::resolveImagePath($imagePath) ?? $imagePath;
        $pathInfo = pathinfo($resolved);
        $extension = strtolower($pathInfo['extension'] ?? '');

        if ($extension === 'webp') {
            return $this->getImageUrlOrDefault($resolved);
        }

        $dirname = ($pathInfo['dirname'] !== '.' && $pathInfo['dirname'] !== '') ? $pathInfo['dirname'] . '/' : '';
        $webpPath = $dirname . $pathInfo['filename'] . '.webp';

        if (Storage::disk('public')->exists($webpPath) || file_exists(public_path('storage/' . $webpPath))) {
            return asset('storage/' . $webpPath);
        }

        return null;
    }

    /**
     * Generar URL segura de imagen con validaciones
     *
     * @param string $imagePath
     * @return string
     */
    public function generateSecureImageUrl(string $imagePath): string
    {
        return \App\Helpers\ImageUrlHelper::getImageUrl($imagePath);
    }

    /**
     * Limpiar ruta de imagen de caracteres peligrosos
     *
     * @param string $imagePath
     * @return string
     */
    private function sanitizeImagePath(string $imagePath): string
    {
        // Remover caracteres peligrosos y normalizar la ruta
        $cleanPath = preg_replace('/[^a-zA-Z0-9\/_.-]/', '', $imagePath);
        
        // Remover dobles barras y normalizar
        $cleanPath = preg_replace('/\/+/', '/', $cleanPath);
        
        // Remover barras al inicio y final
        $cleanPath = trim($cleanPath, '/');
        
        return $cleanPath;
    }

    /**
     * Validar múltiples rutas de imagen
     *
     * @param array $imagePaths
     * @return array Array con rutas válidas e inválidas
     */
    public function validateMultipleImagePaths(array $imagePaths): array
    {
        $valid = [];
        $invalid = [];

        foreach ($imagePaths as $path) {
            if ($this->validateImagePath($path)) {
                $valid[] = $path;
            } else {
                $invalid[] = $path;
            }
        }

        return [
            'valid' => $valid,
            'invalid' => $invalid
        ];
    }

    /**
     * Obtener información detallada de una imagen
     *
     * @param string|null $imagePath
     * @return array
     */
    public function getImageInfo(?string $imagePath): array
    {
        if (empty($imagePath)) {
            return [
                'exists' => false,
                'url' => asset(self::DEFAULT_IMAGE),
                'is_default' => true,
                'size' => null,
                'extension' => null
            ];
        }

        $resolvedPath = \App\Helpers\ImageUrlHelper::resolveImagePath($imagePath);
        $exists = !empty($resolvedPath);
        $diskPath = $resolvedPath ?? $imagePath;
        $size = null;

        if ($exists && Storage::disk('public')->exists($diskPath)) {
            try {
                $size = Storage::disk('public')->size($diskPath);
            } catch (\Throwable $e) {}
        } elseif ($exists && file_exists(public_path('storage/' . $diskPath))) {
            try {
                $size = filesize(public_path('storage/' . $diskPath));
            } catch (\Throwable $e) {}
        }

        return [
            'exists' => $exists,
            'url' => \App\Helpers\ImageUrlHelper::getImageUrl($imagePath),
            'is_default' => !$exists,
            'size' => $size,
            'extension' => strtolower(pathinfo($diskPath, PATHINFO_EXTENSION)),
            'path' => $diskPath
        ];
    }

    /**
     * Verificar si una imagen necesita ser reemplazada por la imagen por defecto
     *
     * @param string|null $imagePath
     * @return bool
     */
    public function needsDefaultImage(?string $imagePath): bool
    {
        return !$this->validateImagePath($imagePath);
    }
}