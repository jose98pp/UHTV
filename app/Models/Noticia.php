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

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
