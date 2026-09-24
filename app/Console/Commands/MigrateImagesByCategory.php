<?php

namespace App\Console\Commands;

use App\Services\ImageMigrationService;
use Illuminate\Console\Command;

class MigrateImagesByCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:migrate-by-category {--dry-run : Simular la migración sin mover archivos} {--force : Ejecutar en producción sin confirmación}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra imágenes existentes de noticias a carpetas organizadas por categoría';

    protected ImageMigrationService $imageMigrationService;

    public function __construct(ImageMigrationService $imageMigrationService)
    {
        parent::__construct();
        $this->imageMigrationService = $imageMigrationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Check if we're in production
        if (app()->environment('production') && !$force && !$dryRun) {
            if (!$this->confirm('Estás en entorno de producción. ¿Seguro que quieres continuar?')) {
                $this->info('Migración cancelada.');
                return 0;
            }
        }

        $this->info('Iniciando migración de imágenes...');

        if ($dryRun) {
            $this->warn('Modo DRY-RUN activado: no se moverán archivos ni actualizarán registros.');
        }

        $results = $this->imageMigrationService->migrateAll($dryRun);

        $this->newLine();
        $this->info('=== Resumen de Migración ===');
        $this->info("Total de noticias encontradas: {$results['total']}");
        $this->info("Migraciones exitosas: {$results['migrated']}");
        $this->info("Migraciones fallidas: {$results['failed']}");

        if ($results['failed'] > 0) {
            $this->newLine();
            $this->warn('Detalles de fallos:');
            foreach ($results['details'] as $detail) {
                if (!$detail['success']) {
                    $this->error("- Noticia ID {$detail['noticia_id']}: {$detail['error']}");
                }
            }
        }

        $this->newLine();
        $this->info('Migración completada!');
        return 0;
    }
}
