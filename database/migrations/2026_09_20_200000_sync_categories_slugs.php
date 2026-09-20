<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Category;
use App\Models\Noticia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fusionar duplicados vacíos si existen
        $categoriesGrouped = Category::all()->groupBy(function ($cat) {
            return Str::lower(trim($cat->name));
        });

        foreach ($categoriesGrouped as $name => $group) {
            if ($group->count() > 1) {
                $sorted = $group->sortByDesc(function ($cat) {
                    return $cat->noticias()->count();
                })->values();

                $primary = $sorted[0];

                for ($i = 1; $i < $sorted->count(); $i++) {
                    $duplicate = $sorted[$i];
                    if ($duplicate->noticias()->count() > 0) {
                        Noticia::where('category_id', $duplicate->id)->update(['category_id' => $primary->id]);
                    }
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
        }

        // 3. Limpiar caché de categorías y portada
        Cache::forget('homepage_data');
        Cache::forget('all_categories');
        Cache::forget('sitemap_xml_data');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No destructivo
    }
};
