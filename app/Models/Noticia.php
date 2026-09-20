<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasImages;

class Noticia extends Model
{
    use HasFactory, HasImages;

    protected $fillable = [
        'titulo',
        'contenido',
        'category_id',
        'user_id',
        'publicada',
        'video_youtube',
        'imagen',
        'galeria',
        'views',
    ];

    protected $casts = [
        'galeria' => 'array',
        'publicada' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function ($noticia) {
            static::clearNewsCache($noticia);
        });

        static::deleting(function ($noticia) {
            try {
                app(\App\Services\ImageStorageService::class)->deleteAllNoticiaImages($noticia);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Error al eliminar imágenes asociadas a la noticia: ' . $e->getMessage(), [
                    'noticia_id' => $noticia->id ?? null
                ]);
            }
        });

        static::deleted(function ($noticia) {
            static::clearNewsCache($noticia);
        });
    }

    public static function clearNewsCache(?Noticia $noticia = null): void
    {
        \Illuminate\Support\Facades\Cache::forget('homepage_data');
        \Illuminate\Support\Facades\Cache::forget('all_categories');
        \Illuminate\Support\Facades\Cache::forget('sitemap_xml_data');
        if ($noticia && $noticia->category_id) {
            \Illuminate\Support\Facades\Cache::forget("category_{$noticia->category_id}_data");
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSlugAttribute(): string
    {
        $slug = \Illuminate\Support\Str::slug($this->titulo);
        return !empty($slug) ? $slug : 'noticia';
    }

    public function getSlugWithIdAttribute(): string
    {
        return $this->slug . '_' . $this->id;
    }

    public function getCategorySlugAttribute(): string
    {
        if ($this->category) {
            return $this->category->slug ?: \Illuminate\Support\Str::slug($this->category->name);
        }
        return 'general';
    }

    public function getUrlAttribute(): string
    {
        return route('show', [
            'category' => $this->category_slug,
            'slug' => $this->slug_with_id,
        ]);
    }

    public function getUrlParamsAttribute(): array
    {
        return [
            'category' => $this->category_slug,
            'slug' => $this->slug_with_id,
        ];
    }

    public function getContenidoSanitizadoAttribute(): string
    {
        if (empty($this->contenido)) {
            return '';
        }

        try {
            $sanitizer = app(\App\Services\ContentSanitizationService::class);
            return $sanitizer->sanitizeContent($this->contenido);
        } catch (\Throwable $e) {
            return $this->contenido;
        }
    }

    public function getGaleriaUrlsAttribute(): array
    {
        if (empty($this->galeria) || !is_array($this->galeria)) {
            return [];
        }

        $urls = [];
        foreach ($this->galeria as $item) {
            if (is_string($item) && !empty($item)) {
                $urls[] = str_starts_with($item, 'http') ? $item : \App\Helpers\ImageUrlHelper::getImageUrl($item);
            }
        }
        return $urls;
    }

    public function getWebpUrlAttribute(): ?string
    {
        return \App\Helpers\ImageUrlHelper::getWebpUrl($this->imagen);
    }

    public function getOptimizedImageUrlAttribute(): string
    {
        return $this->webp_url ?? $this->imageUrl;
    }
}
