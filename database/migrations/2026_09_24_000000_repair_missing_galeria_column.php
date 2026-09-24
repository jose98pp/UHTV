<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repara instalaciones donde la migración original figura aplicada pero la
     * columna no existe físicamente en la tabla noticias.
     */
    public function up(): void
    {
        if (!Schema::hasTable('noticias') || Schema::hasColumn('noticias', 'galeria')) {
            return;
        }

        Schema::table('noticias', function (Blueprint $table) {
            $table->json('galeria')->nullable()->after('imagen');
        });
    }

    /**
     * La migración original conserva la columna al revertir esta reparación.
     * Eliminarla aquí podría destruir galerías ya guardadas.
     */
    public function down(): void
    {
        // No-op intencional: la galería es parte del esquema funcional actual.
    }
};
