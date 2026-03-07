<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public    $incrementing = false;
    protected $keyType      = 'string';

    protected $fillable = ['key', 'value'];

    // Kolay okuma: Setting::get('price_monthly', '299.00')
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 60, function () use ($key, $default) {
            $row = static::find($key);
            return $row ? $row->value : $default;
        });
    }

    // Kolay yazma: Setting::set('price_monthly', '399.00')
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }
}
