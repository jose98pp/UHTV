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
        Schema::table('noticias', function (Blueprint $table) {
            // Índices simples
            $table->index('publicada', 'noticias_publicada_index');
            $table->index('created_at', 'noticias_created_at_index');

            // Índices compuestos para consultas recurrentes en portada y categorías
            $table->index(['category_id', 'publicada'], 'noticias_category_id_publicada_index');
            $table->index(['publicada', 'created_at'], 'noticias_publicada_created_at_index');
            $table->index(['publicada', 'views'], 'noticias_publicada_views_index');
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
