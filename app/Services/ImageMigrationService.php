<?php

namespace App\Services;

use App\Models\Noticia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageMigrationService
{
    protected ImageStorageService $imageStorageService;

    public function __construct(ImageStorageService $imageStorageService)
    {
        $this->imageStorageService = $imageStorageService;
    }

    /**
     * Find legacy images that are not in category folders
     *
     * @return array
     */
    public function findLegacyImages(): array
    {
        $legacyNoticias = Noticia::whereNotNull('imagen')
            ->where('imagen', 'not like', 'noticias/%/%')
            ->get();

        $results = [
            'total' => $legacyNoticias->count(),
            'noticias' => $legacyNoticias,
        ];

        Log::info('Found legacy images for migration', $results);

        return $results;
    }

    /**
     * Migrate a single noticia's image to category folder
     *
     * @param Noticia $noticia
     * @param bool $dryRun
     * @return array
     */
    public function migrateSingle(Noticia $noticia, bool $dryRun = false): array
    {
        $disk = Storage::disk('public');
        $currentPath = $noticia->imagen;
        $result = [
            'noticia_id' => $noticia->id,
            'current_path' => $currentPath,
            'success' => false,
            'dry_run' => $dryRun,
        ];

        try {
            // Check if current file exists
            if (!$disk->exists($currentPath)) {
                $result['error'] = 'Image file not found';
                Log::error('Migration failed: file not found', $result);
                return $result;
            }

            $newCategoryId = $noticia->category_id;
            $destinationFolder = $this->imageStorageService->getCategoryFolder($newCategoryId);
            $filename = basename($currentPath);
            $newPath = $destinationFolder . '/' . $filename;

            $result['new_path'] = $newPath;
            $result['category_id'] = $newCategoryId;

            if (!$dryRun) {
                // Ensure destination folder exists
                $this->imageStorageService->ensureFolderExists($destinationFolder);

                // Move the file
                $moved = $disk->move($currentPath, $newPath);

                if (!$moved) {
                    $result['error'] = 'Failed to move file';
                    Log::error('Migration failed: file move error', $result);
                    return $result;
                }

                // Update the noticia record
                $noticia->update(['imagen' => $newPath]);
            }

            $result['success'] = true;
            Log::info('Image migration successful', $result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            Log::error('Exception during image migration', $result);
        }

        return $result;
    }

    /**
     * Migrate all legacy images
     *
     * @param bool $dryRun
     * @return array
     */
    public function migrateAll(bool $dryRun = false): array
    {
        $legacyData = $this->findLegacyImages();
        $total = $legacyData['total'];
        $noticias = $legacyData['noticias'];

        $results = [
            'total' => $total,
            'migrated' => 0,
            'failed' => 0,
            'dry_run' => $dryRun,
            'details' => [],
        ];

        foreach ($noticias as $noticia) {
            $migrateResult = $this->migrateSingle($noticia, $dryRun);
            $results['details'][] = $migrateResult;

            if ($migrateResult['success']) {
                $results['migrated']++;
            } else {
                $results['failed']++;
            }
        }

        Log::info('Image migration completed', $results);

        return $results;
    }
}
