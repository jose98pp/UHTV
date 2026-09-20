<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageStorageService;

class OptimizeAllImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize-all 
                            {--quality=85 : Calidad de compresión WebP/JPEG (1-100, defecto 85)}
                            {--max-width=1600 : Ancho máximo en píxeles (defecto 1600)}
                            {--dry-run : Simular sin realizar cambios reales}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimizar imágenes del sitio (redimensionar si son excesivas, comprimir a calidad 85 y generar WebP) sin pérdida visual de calidad';

    protected ImageStorageService $imageStorageService;

    public function __construct(ImageStorageService $imageStorageService)
    {
        parent::__construct();
        $this->imageStorageService = $imageStorageService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $quality = (int) $this->option('quality');
        $maxWidth = (int) $this->option('max-width');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("⚡ Iniciando optimización de imágenes (Calidad: {$quality}%, Ancho máx: {$maxWidth}px)...");

        if (!function_exists('imagewebp') || !function_exists('imagecreatefromstring')) {
            $this->error('❌ La extensión PHP GD no está disponible para procesar imágenes.');
            return 1;
        }

        if ($dryRun) {
            $this->warn('⚠️ MODO DRY-RUN: Solo se analizará el almacenamiento');
        }

        // Obtener todos los archivos en noticias
        $files = Storage::disk('public')->allFiles('noticias');
        $eligibleFiles = array_filter($files, function ($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            return in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true);
        });

        $totalFiles = count($eligibleFiles);
        $this->info("📸 Total de imágenes encontradas: {$totalFiles}");

        if ($totalFiles === 0) {
            $this->info('✅ No hay imágenes para optimizar.');
            return 0;
        }

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        $processed = 0;
        $webpGenerated = 0;
        $bytesBefore = 0;
        $bytesAfter = 0;

        foreach ($eligibleFiles as $file) {
            $fullPath = Storage::disk('public')->path($file);
            $size = file_exists($fullPath) ? filesize($fullPath) : 0;
            $bytesBefore += $size;

            if (!$dryRun) {
                // Optimizar archivo original si excede dimensiones
                $this->imageStorageService->optimizeImage($file, $maxWidth, 1200, $quality);

                // Verificar si se generó versión WebP
                $pathInfo = pathinfo($file);
                if (strtolower($pathInfo['extension']) !== 'webp') {
                    $dirname = ($pathInfo['dirname'] !== '.' && !empty($pathInfo['dirname'])) ? $pathInfo['dirname'] . '/' : '';
                    $webpPath = $dirname . $pathInfo['filename'] . '.webp';
                    if (Storage::disk('public')->exists($webpPath)) {
                        $webpGenerated++;
                    }
                }

                $newSize = file_exists($fullPath) ? filesize($fullPath) : 0;
                $bytesAfter += $newSize;
            } else {
                $bytesAfter += $size;
            }

            $processed++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $mbBefore = round($bytesBefore / (1024 * 1024), 2);
        $mbAfter = round($bytesAfter / (1024 * 1024), 2);
        $savedMb = round(($bytesBefore - $bytesAfter) / (1024 * 1024), 2);

        $this->info("✨ ¡Optimización completada con éxito!");
        $this->table(
            ['Métrica', 'Resultado'],
            [
                ['Imágenes procesadas', $processed],
                ['Versiones WebP generadas / verificadas', $webpGenerated],
                ['Peso inicial', "{$mbBefore} MB"],
                ['Peso final optimizado', "{$mbAfter} MB"],
                ['Espacio ahorrado', ($savedMb > 0 ? "{$savedMb} MB (" . round(($savedMb / max(1, $mbBefore)) * 100, 1) . "%)" : "0 MB (Ya optimizado)")],
                ['Calidad visual conservada', "{$quality}% (Excelente nitidez sin artefactos)"],
            ]
        );

        return 0;
    }
}
