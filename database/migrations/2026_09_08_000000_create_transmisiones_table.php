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
        Schema::create('transmisiones', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['en_vivo', 'podcast', 'clip', 'programa'])->default('en_vivo');
            $table->enum('plataforma', ['youtube', 'facebook', 'tiktok', 'twitch', 'otro'])->default('youtube');
            $table->text('url');
            $table->text('embed_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->boolean('en_vivo')->default(false)->index();
            $table->boolean('activo')->default(true)->index();
            $table->boolean('destacado')->default(false)->index();
            $table->unsignedBigInteger('views')->default(0);
            $table->string('duracion', 50)->nullable();
            $table->timestamp('fecha_transmision')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transmisiones');
    }
};
