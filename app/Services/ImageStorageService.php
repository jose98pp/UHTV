<?php

namespace App\Services;

use App\Models\Category;
use Carbon\Carbon;
use FilesystemIterator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ImageStorageService
{
    /**
     * Directorio base para imágenes de noticias
     */
    private const BASE_DIRECTORY = 'noticias';

    /**
     * Extensiones de imagen permitidas
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Tamaño máximo de archivo en bytes (5MB)
     */
    private const MAX_FILE_SIZE = 5 * 1024 * 1024;

    protected ImageValidationService $imageValidationService;

    public function __construct(ImageValidationService $imageValidationService)
    {
        $this->imageValidationService = $imageValidationService;
    }

    /**
     * Almacenar imagen organizándola por categoría
     */
    public function storeImageByCategory(UploadedFile $file, int $categoryId): ?string
    {
        try {
            $this->validateImageFile($file);

            $category = Category::find($categoryId);
            if (!$category) {
                throw new \Exception('Categoría no encontrada');
            }

            $categorySlug = Str::slug($category->name);
            $directory = self::BASE_DIRECTORY . '/' . $categorySlug;
            $fileName = $this->generateUniqueFileName($file, $categoryId);
            $storedPath = $file->storeAs($directory, $fileName, 'public');

            if (!$storedPath) {
                throw new \Exception('Error al almacenar el archivo');
            }

            if (!$this->imageValidationService->validateImagePath($storedPath)) {
                Storage::disk('public')->delete($storedPath);
                throw new \Exception('Error en la validación post-almacenamiento');
            }

            Log::info('Imagen almacenada exitosamente', [
                'path' => $storedPath,
                'category' => $category->name,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);

            // Optimizar imagen (redimensionar si supera 1600px, compresión calidad 85) y generar versión WebP
            $this->optimizeImage($storedPath);

            return $storedPath;
        } catch (\Exception $e) {
            Log::error('Error al almacenar imagen por categoría', [
                'error' => $e->getMessage(),
                'category_id' => $categoryId,
                'file_name' => $file->getClientOriginalName() ?? 'unknown',
            ]);
            throw $e;
        }
    }

    /**
     * Optimizar una imagen para web:
     * - Redimensionar si supera el ancho máximo (1600px) sin distorsionar
     * - Comprimir a calidad óptima (85) que no pierde calidad visual
     * - Generar automáticamente versión WebP (85% calidad)
     */
    public function optimizeImage(string $relativePublicPath, int $maxWidth = 1600, int $maxHeight = 1200, int $quality = 85): bool
    {
        if (!function_exists('imagecreatefromstring')) {
            return false;
        }

        try {
            $fullPath = Storage::disk('public')->path($relativePublicPath);
            if (!file_exists($fullPath)) {
                return false;
            }

            $content = @file_get_contents($fullPath);
            if (!$content) {
                return false;
            }

            $img = @imagecreatefromstring($content);
            if (!$img) {
                return false;
            }

            $origWidth = imagesx($img);
            $origHeight = imagesy($img);

            // Si excede las dimensiones máximas recomendadas para web, redimensionar proporcionalmente
            if ($origWidth > $maxWidth || $origHeight > $maxHeight) {
                $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
                $newWidth = (int) max(1, round($origWidth * $ratio));
                $newHeight = (int) max(1, round($origHeight * $ratio));

                $resized = imagecreatetruecolor($newWidth, $newHeight);

                $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

                // Preservar transparencia para PNG y GIF
                if (in_array($ext, ['png', 'gif'])) {
                    imagecolortransparent($resized, imagecolorallocatealpha($resized, 0, 0, 0, 127));
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                }

                imagecopyresampled($resized, $img, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                imagedestroy($img);
                $img = $resized;

                // Guardar la versión optimizada en su formato original
                if ($ext === 'png') {
                    imagepng($img, $fullPath, 6);
                } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                    imagejpeg($img, $fullPath, $quality);
                } elseif ($ext === 'webp' && function_exists('imagewebp')) {
                    imagewebp($img, $fullPath, $quality);
                }
            }

            imagedestroy($img);

            // Generar o actualizar versión WebP complementaria
            $this->generateWebpVersion($relativePublicPath, $quality);

            return true;
        } catch (\Throwable $e) {
            Log::warning('No se pudo optimizar la imagen ' . $relativePublicPath . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generar versión WebP si la extensión GD/Imagick está disponible
     */
    public function generateWebpVersion(string $relativePublicPath, int $quality = 85): ?string
    {
        if (!function_exists('imagewebp') || !function_exists('imagecreatefromstring')) {
            return null;
        }

        try {
            $fullPath = Storage::disk('public')->path($relativePublicPath);
            if (!file_exists($fullPath)) {
                return null;
            }

            $pathInfo = pathinfo($fullPath);
            if (strtolower($pathInfo['extension'] ?? '') === 'webp') {
                return $relativePublicPath;
            }

            $webpFullPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
            $imageContent = file_get_contents($fullPath);
            $image = @imagecreatefromstring($imageContent);

            if ($image) {
                if (function_exists('imagepalettetotruecolor')) {
                    imagepalettetotruecolor($image);
                }
                imagealphablending($image, true);
                imagesavealpha($image, true);

                imagewebp($image, $webpFullPath, $quality);
                imagedestroy($image);

                $dirname = ($pathInfo['dirname'] !== '.' && $pathInfo['dirname'] !== '') ? dirname($relativePublicPath) . '/' : '';
                return $dirname . $pathInfo['filename'] . '.webp';
            }
        } catch (\Throwable $e) {
            Log::warning('No se pudo generar versión WebP de la imagen: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Mover imagen existente a nueva estructura de categorías
     */
    public function moveImageToCategory(string $currentPath, int $categoryId): ?string
    {
        try {
            $resolvedPath = \App\Helpers\ImageUrlHelper::resolveImagePath($currentPath);

            if ($resolvedPath && $resolvedPath !== $currentPath) {
                Log::info('Imagen ya resolvible en la nueva estructura, se reutiliza la ruta encontrada', [
                    'original' => $currentPath,
                    'resolved' => $resolvedPath,
                    'category_id' => $categoryId,
                ]);
                return $resolvedPath;
            }

            if (!$this->imageValidationService->validateImagePath($currentPath)) {
                Log::warning('Imagen no encontrada para mover', ['path' => $currentPath]);
                return null;
            }

            $category = Category::find($categoryId);
            if (!$category) {
                throw new \Exception('Categoría no encontrada');
            }

            $categorySlug = Str::slug($category->name);
            $directory = self::BASE_DIRECTORY . '/' . $categorySlug;
            $fileName = basename($currentPath);
            $newPath = $directory . '/' . $fileName;

            if (Storage::disk('public')->exists($newPath)) {
                $pathInfo = pathinfo($fileName);
                $fileName = $pathInfo['filename'] . '_' . time() . '.' . $pathInfo['extension'];
                $newPath = $directory . '/' . $fileName;
            }

            if (Storage::disk('public')->move($currentPath, $newPath)) {
                Log::info('Imagen movida exitosamente', [
                    'from' => $currentPath,
                    'to' => $newPath,
                    'category' => $category->name,
                ]);
                return $newPath;
            }

            throw new \Exception('Error al mover el archivo');
        } catch (\Exception $e) {
            Log::error('Error al mover imagen a categoría', [
                'error' => $e->getMessage(),
                'current_path' => $currentPath,
                'category_id' => $categoryId,
            ]);
            return null;
        }
    }

    /**
     * Eliminar imagen y sus derivados (WebP, formatos alternativos) de forma segura
     */
    public function deleteImage(string $imagePath): bool
    {
        try {
            if (empty($imagePath)) {
                return false;
            }

            // Normalizar ruta: quitar slashes iniciales, 'storage/', 'public/'
            $normalizedPath = trim(str_replace('\\', '/', $imagePath), '/');
            $normalizedPath = preg_replace('#^(storage|public)/#i', '', $normalizedPath) ?? $normalizedPath;
            $normalizedPath = ltrim($normalizedPath, '/');

            if (empty($normalizedPath)) {
                return false;
            }

            $deleted = false;

            if (Storage::disk('public')->exists($normalizedPath)) {
                $deleted = Storage::disk('public')->delete($normalizedPath);
            }

            // Eliminar versión WebP complementaria si existe
            $pathInfo = pathinfo($normalizedPath);
            $ext = strtolower($pathInfo['extension'] ?? '');
            $dirname = ($pathInfo['dirname'] !== '.' && !empty($pathInfo['dirname'])) ? $pathInfo['dirname'] . '/' : '';

            if ($ext !== 'webp') {
                $webpPath = $dirname . $pathInfo['filename'] . '.webp';
                if (Storage::disk('public')->exists($webpPath)) {
                    Storage::disk('public')->delete($webpPath);
                    Log::info('Versión WebP eliminada exitosamente', ['path' => $webpPath]);
                }
            } else {
                // Si la imagen era WebP, buscar si había un jpg o png con el mismo nombre
                foreach (['jpg', 'jpeg', 'png'] as $altExt) {
                    $altPath = $dirname . $pathInfo['filename'] . '.' . $altExt;
                    if (Storage::disk('public')->exists($altPath)) {
                        Storage::disk('public')->delete($altPath);
                    }
                }
            }

            if ($deleted) {
                Log::info('Imagen eliminada exitosamente del storage', ['path' => $normalizedPath]);
            } else {
                Log::warning('No se encontró archivo en storage para eliminar', ['path' => $normalizedPath]);
            }

            return $deleted;
        } catch (\Exception $e) {
            Log::error('Error al eliminar imagen del storage', [
                'error' => $e->getMessage(),
                'path' => $imagePath,
            ]);
            return false;
        }
    }

    /**
     * Eliminar todas las imágenes asociadas a una noticia (imagen principal, galería, webp y contenido)
     */
    public function deleteAllNoticiaImages(\App\Models\Noticia $noticia): int
    {
        $deletedCount = 0;

        try {
            // 1. Eliminar imagen principal y su versión WebP
            if (!empty($noticia->imagen)) {
                if ($this->deleteImage($noticia->imagen)) {
                    $deletedCount++;
                }
            }

            // 2. Eliminar todas las imágenes de la galería multimedia
            if (!empty($noticia->galeria) && is_array($noticia->galeria)) {
                foreach ($noticia->galeria as $foto) {
                    if (!empty($foto) && is_string($foto)) {
                        if ($this->deleteImage($foto)) {
                            $deletedCount++;
                        }
                    }
                }
            }

            // 3. Eliminar imágenes locales insertadas dentro del contenido de la noticia
            if (!empty($noticia->contenido)) {
                if (preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $noticia->contenido, $matches)) {
                    foreach ($matches[1] as $src) {
                        if (str_contains($src, '/storage/noticias/') || str_contains($src, 'storage/noticias/')) {
                            $extractedPath = preg_replace('/^.*storage\//i', '', $src);
                            if (!empty($extractedPath)) {
                                if ($this->deleteImage($extractedPath)) {
                                    $deletedCount++;
                                }
                            }
                        }
                    }
                }
            }

            Log::info('Limpieza completa de imágenes para noticia finalizada', [
                'noticia_id' => $noticia->id,
                'titulo' => $noticia->titulo,
                'archivos_eliminados' => $deletedCount,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error en deleteAllNoticiaImages: ' . $e->getMessage(), [
                'noticia_id' => $noticia->id ?? null,
            ]);
        }

        return $deletedCount;
    }

    /**
     * Obtener todas las imágenes de una categoría
     */
    public function getImagesByCategory(int $categoryId): array
    {
        try {
            $category = Category::find($categoryId);
            if (!$category) {
                return [];
            }

            $categorySlug = Str::slug($category->name);
            $categoryDirectory = self::BASE_DIRECTORY . '/' . $categorySlug;
            $files = [];

            $this->streamNewsFiles(function (string $relativePath) use (&$files, $categoryDirectory): void {
                if (strpos($relativePath, $categoryDirectory) !== 0) {
                    return;
                }

                $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
                if (in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
                    $files[] = $relativePath;
                }
            });

            return $files;
        } catch (\Exception $e) {
            Log::error('Error al obtener imágenes por categoría', [
                'error' => $e->getMessage(),
                'category_id' => $categoryId,
            ]);
            return [];
        }
    }

    /**
     * Iterar los archivos de noticias sin cargar todo el árbol en memoria.
     */
    public function streamNewsFiles(callable $callback): void
    {
        $basePath = storage_path('app/public/' . self::BASE_DIRECTORY);
        if (!is_dir($basePath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, FilesystemIterator::SKIP_DOTS)
        );

        $prefixLength = strlen(storage_path('app/public/'));

        foreach ($iterator as $fileInfo) {
            if (!$fileInfo->isFile()) {
                continue;
            }

            $fullPath = $fileInfo->getPathname();
            $relativePath = str_replace('\\', '/', substr($fullPath, $prefixLength));
            $relativePath = ltrim($relativePath, '/');
            $callback($relativePath);
        }
    }

    /**
     * Crear backup de imagen antes de operaciones críticas
     */
    public function createImageBackup(string $imagePath): ?string
    {
        try {
            if (!$this->imageValidationService->validateImagePath($imagePath)) {
                return null;
            }

            $backupDirectory = 'backups/images/' . Carbon::now()->format('Y/m/d');
            $fileName = basename($imagePath);
            $backupPath = $backupDirectory . '/' . time() . '_' . $fileName;

            if (Storage::disk('public')->copy($imagePath, $backupPath)) {
                Log::info('Backup de imagen creado', [
                    'original' => $imagePath,
                    'backup' => $backupPath,
                ]);
                return $backupPath;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error al crear backup de imagen', [
                'error' => $e->getMessage(),
                'path' => $imagePath,
            ]);
            return null;
        }
    }

    /**
     * Migrar todas las imágenes existentes a la nueva estructura
     */
    public function migrateExistingImages(bool $dryRun = false): array
    {
        $results = [
            'migrated' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        try {
            $categorySlugs = Category::all()->mapWithKeys(function ($category) {
                return [$category->id => Str::slug($category->name)];
            })->toArray();

            \App\Models\Noticia::whereNotNull('imagen')
                ->where('imagen', '!=', '')
                ->with('category')
                ->chunk(200, function ($noticias) use (&$results, $categorySlugs, $dryRun): void {
                    foreach ($noticias as $noticia) {
                        try {
                            if (!$noticia->category) {
                                $results['skipped']++;
                                continue;
                            }

                            $categorySlug = $categorySlugs[$noticia->category_id] ?? Str::slug($noticia->category->name);
                            if (Str::contains($noticia->imagen, $categorySlug)) {
                                $results['skipped']++;
                                continue;
                            }

                            $basename = basename($noticia->imagen);
                            $candidates = [
                                $noticia->imagen,
                                self::BASE_DIRECTORY . '/' . $basename,
                                self::BASE_DIRECTORY . '/' . $categorySlug . '/' . $basename,
                            ];

                            $found = null;
                            foreach ($candidates as $candidate) {
                                if (Storage::disk('public')->exists($candidate)) {
                                    $found = $candidate;
                                    break;
                                }
                            }

                            if ($found) {
                                if ($dryRun) {
                                    $results['migrated']++;
                                } else {
                                    $noticia->update(['imagen' => $found]);
                                    $results['migrated']++;
                                }
                                continue;
                            }

                            if ($dryRun) {
                                $results['failed']++;
                                $results['errors'][] = "Imagen no encontrada (dry-run) para noticia ID: {$noticia->id} - {$noticia->imagen}";
                                continue;
                            }

                            $this->createImageBackup($noticia->imagen);
                            $newPath = $this->moveImageToCategory($noticia->imagen, $noticia->category_id);

                            if ($newPath) {
                                $noticia->update(['imagen' => $newPath]);
                                $results['migrated']++;
                            } else {
                                $results['failed']++;
                                $results['errors'][] = "Error al mover imagen de noticia ID: {$noticia->id}";
                            }
                        } catch (\Exception $e) {
                            $results['failed']++;
                            $results['errors'][] = "Error en noticia ID {$noticia->id}: " . $e->getMessage();
                        }
                    }
                });
        } catch (\Exception $e) {
            $results['errors'][] = 'Error general en migración: ' . $e->getMessage();
        }

        Log::info('Migración de imágenes completada', $results);
        return $results;
    }

    /**
     * Validar archivo de imagen
     */
    private function validateImageFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new \Exception('El archivo no es válido');
        }

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('El archivo es demasiado grande. Máximo 5MB permitido.');
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \Exception('Tipo de archivo no permitido. Solo se permiten: ' . implode(', ', self::ALLOWED_EXTENSIONS));
        }

        $imageInfo = getimagesize($file->getPathname());
        if (!$imageInfo) {
            throw new \Exception('El archivo no es una imagen válida');
        }
    }

    /**
     * Generar nombre único para el archivo
     */
    private function generateUniqueFileName(UploadedFile $file, int $categoryId): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $timestamp = Carbon::now()->format('YmdHis');
        $random = Str::random(8);

        return "cat{$categoryId}_{$timestamp}_{$random}.{$extension}";
    }
}