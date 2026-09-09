<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('categories', 'slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->string('slug')->nullable()->unique()->after('name');
            });
        }

        // Poblado inicial seguro de slugs para categorías existentes
        $categories = DB::table('categories')->get();
        foreach ($categories as $category) {
            if (empty($category->slug)) {
                $baseSlug = Str::slug($category->name);
                if (empty($baseSlug)) {
                    $baseSlug = 'categoria-' . $category->id;
                }

                $slug = $baseSlug;
                $count = 1;
                while (DB::table('categories')->where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                    $slug = $baseSlug . '-' . $count;
                    $count++;
                }

                DB::table('categories')->where('id', $category->id)->update(['slug' => $slug]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('categories', 'slug')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
