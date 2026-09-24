<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\StreamingEmbedService;

class Transmision extends Model
{
    use HasFactory;

    protected $table = 'transmisiones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'plataforma',
        'url',
        'embed_url',
        'thumbnail_url',
        'en_vivo',
        'activo',
        'destacado',
        'views',
        'duracion',
        'fecha_transmision',
    ];

    protected $casts = [
        'en_vivo' => 'boolean',
        'activo' => 'boolean',
        'destacado' => 'boolean',
        'views' => 'integer',
        'fecha_transmision' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($transmision) {
            $embedService = app(StreamingEmbedService::class);

            // Auto detectar plataforma si es necesario
            if (empty($transmision->plataforma) || $transmision->plataforma === 'otro') {
                $transmision->plataforma = $embedService->detectPlatform($transmision->url);
            }

            // Generar embed_url si está vacío o si la URL cambió
            if (empty($transmision->embed_url) || $transmision->isDirty('url')) {
                $transmision->embed_url = $embedService->generateEmbedUrl($transmision->url, $transmision->plataforma);
            }

            // Generar miniatura automática si está vacía
            if (empty($transmision->thumbnail_url)) {
                $transmision->thumbnail_url = $embedService->generateThumbnailUrl($transmision->url, $transmision->plataforma);
            }

            if (empty($transmision->fecha_transmision)) {
                $transmision->fecha_transmision = now();
            }
        });
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeEnVivo($query)
    {
        return $query->where('en_vivo', true)->where('activo', true);
    }

    public function scopePodcasts($query)
    {
        return $query->where('tipo', 'podcast')->where('activo', true);
    }

    public function scopeClips($query)
    {
        return $query->where('tipo', 'clip')->where('activo', true);
    }

    public function scopeProgramas($query)
    {
        return $query->where('tipo', 'programa')->where('activo', true);
    }

    public function scopeGrabados($query)
    {
        return $query->where('en_vivo', false)->where('activo', true);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    /**
     * Contenidos multimedia visibles en la franja debajo del navbar (clips, podcasts, programas).
     * Las transmisiones en vivo van exclusivamente al botón "En Vivo".
     */
    public function scopeParaPortada($query)
    {
        return $query->where('activo', true)
            ->where('en_vivo', false)
            ->whereIn('tipo', ['podcast', 'clip', 'programa'])
            ->orderByDesc('destacado')
            ->orderByDesc('fecha_transmision')
            ->orderByDesc('created_at');
    }

    // Accessors
    public function getPlataformaIconAttribute(): string
    {
        return match ($this->plataforma) {
            'youtube' => 'fab fa-youtube',
            'facebook' => 'fab fa-facebook',
            'tiktok' => 'fab fa-tiktok',
            'twitch' => 'fab fa-twitch',
            default => 'fas fa-video',
        };
    }

    public function getPlataformaColorAttribute(): string
    {
        return match ($this->plataforma) {
            'youtube' => '#ff0000',
            'facebook' => '#1877f2',
            'tiktok' => '#000000',
            'twitch' => '#9146ff',
            default => '#4b5563',
        };
    }

    public function getPlataformaNombreAttribute(): string
    {
        return match ($this->plataforma) {
            'youtube' => 'YouTube',
            'facebook' => 'Facebook Live',
            'tiktok' => 'TikTok',
            'twitch' => 'Twitch',
            default => 'Video Directo',
        };
    }

    public function getTipoNombreAttribute(): string
    {
        return match ($this->tipo) {
            'en_vivo' => 'En Vivo',
            'podcast' => 'Podcast',
            'clip' => 'Clip / Corto',
            'programa' => 'Programa',
            default => 'Transmisión',
        };
    }

    public function getTipoColorAttribute(): string
    {
        return match ($this->tipo) {
            'en_vivo' => 'danger',
            'podcast' => 'primary',
            'clip' => 'warning',
            'programa' => 'info',
            default => 'secondary',
        };
    }

    public function getEffectiveThumbnailAttribute(): string
    {
        if (!empty($this->thumbnail_url)) {
            return $this->thumbnail_url;
        }

        // Miniatura por defecto de UHTV
        return asset('images/Logo.jpg');
    }
}
