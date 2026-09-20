<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Noticia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SyncCategorySlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'categories:sync-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza y repara los slugs de las categorías eliminando duplicados vacíos sin perder datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de categorías y slugs...');

        // 1. Identificar y fusionar categorías duplicadas (mismo nombre)
        $categoriesGrouped = Category::all()->groupBy(function ($cat) {
            return Str::lower(trim($cat->name));
        });

        foreach ($categoriesGrouped as $name => $group) {
            if ($group->count() > 1) {
                // Ordenar por cantidad de noticias desc, luego por ID asc
                $sorted = $group->sortByDesc(function ($cat) {
                    return $cat->noticias()->count();
                })->values();

                $primary = $sorted[0];
                $this->warn("Detectado duplicado para categoría '{$name}'. Principal: ID {$primary->id} (Noticias: {$primary->noticias()->count()})");

                for ($i = 1; $i < $sorted->count(); $i++) {
                    $duplicate = $sorted[$i];
                    $dupCount = $duplicate->noticias()->count();

                    if ($dupCount > 0) {
                        $this->info("Reasignando {$dupCount} noticias de ID {$duplicate->id} a ID {$primary->id}...");
                        Noticia::where('category_id', $duplicate->id)->update(['category_id' => $primary->id]);
                    }

                    $this->info("Eliminando categoría duplicada vacía ID {$duplicate->id}...");
                    $duplicate->delete();
                }
            }
        }

        // 2. Asignar slugs canónicos limpios a todas las categorías
        $allCategories = Category::orderBy('id', 'asc')->get();

        foreach ($allCategories as $category) {
            $baseSlug = Str::slug($category->name);
            if (empty($baseSlug)) {
                $baseSlug = 'categoria-' . $category->id;
            }

            $slug = $baseSlug;
            $count = 1;
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }

            $category->slug = $slug;
            $category->saveQuietly();

            $noticiasCount = $category->noticias()->count();
            $this->line("✅ ID {$category->id}: '{$category->name}' -> Slug: '{$slug}' (Noticias: {$noticiasCount})");
        }

        // 3. Limpiar caché global
        Cache::forget('homepage_data');
        Cache::forget('all_categories');
        Cache::forget('sitemap_xml_data');

        $this->info('¡Sincronización completada con éxito y caché limpiada!');
        return Command::SUCCESS;
    }
}
