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
        'views',
    ];

    protected static function booted(): void
    {
        static::saved(function ($noticia) {
            static::clearNewsCache($noticia);
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
}
