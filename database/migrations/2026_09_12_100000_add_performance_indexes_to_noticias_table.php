<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            $existingIndexes = collect(\Illuminate\Support\Facades\DB::select("SHOW INDEX FROM noticias"))
                ->pluck('Key_name')
                ->unique()
                ->all();
        } catch (\Throwable $e) {
            $existingIndexes = [];
        }

        Schema::table('noticias', function (Blueprint $table) use ($existingIndexes) {
            if (!in_array('noticias_publicada_index', $existingIndexes)) {
                $table->index('publicada', 'noticias_publicada_index');
            }
            if (!in_array('noticias_created_at_index', $existingIndexes)) {
                $table->index('created_at', 'noticias_created_at_index');
            }
            if (!in_array('noticias_category_id_publicada_index', $existingIndexes)) {
                $table->index(['category_id', 'publicada'], 'noticias_category_id_publicada_index');
            }
            if (!in_array('noticias_publicada_created_at_index', $existingIndexes)) {
                $table->index(['publicada', 'created_at'], 'noticias_publicada_created_at_index');
            }
            if (!in_array('noticias_publicada_views_index', $existingIndexes)) {
                $table->index(['publicada', 'views'], 'noticias_publicada_views_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropIndex('noticias_publicada_index');
            $table->dropIndex('noticias_created_at_index');
            $table->dropIndex('noticias_category_id_publicada_index');
            $table->dropIndex('noticias_publicada_created_at_index');
            $table->dropIndex('noticias_publicada_views_index');
        });
    }
};
