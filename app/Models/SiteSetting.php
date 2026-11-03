<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];
    public $timestamps = true;

    public static function value(string $key, $default = null)
    {
        return Cache::remember('uhtv.setting.'.$key, 60, fn () => static::where('key', $key)->value('value') ?? $default);
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        Cache::forget('uhtv.setting.'.$key);
    }
}
