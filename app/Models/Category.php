<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug', 'descripcion'];

    protected static function booted()
    {
        static::saving(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });

        static::saved(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('homepage_data');
            \Illuminate\Support\Facades\Cache::forget('all_categories');
            \Illuminate\Support\Facades\Cache::forget('sitemap_xml_data');
            if ($category->id) {
                \Illuminate\Support\Facades\Cache::forget("category_{$category->id}_data");
            }
        });

        static::deleted(function ($category) {
            \Illuminate\Support\Facades\Cache::forget('homepage_data');
            \Illuminate\Support\Facades\Cache::forget('all_categories');
            \Illuminate\Support\Facades\Cache::forget('sitemap_xml_data');
        });
    }

    public function noticias()
    {
        return $this->hasMany(Noticia::class, 'category_id');
    }

    public function getUrlAttribute(): string
    {
        $slug = $this->slug ?: \Illuminate\Support\Str::slug($this->name);
        return route('categoria.noticias', ['category' => $slug ?: $this->id]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
