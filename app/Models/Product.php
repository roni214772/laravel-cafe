<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'price',
        'quantity',
        'category',
        'sku',
        'image_url',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
    ];

    protected $appends = ['thumb_url'];

    /**
     * Thumbnail URL — ana görselin thumb_ önekli versiyonu
     */
    public function getThumbUrlAttribute(): ?string
    {
        if (!$this->image_url) return null;
        if (!str_starts_with($this->image_url, '/storage/')) return $this->image_url;
        $dir  = dirname($this->image_url);
        $name = basename($this->image_url);
        $thumbPath = $dir . '/thumb_' . $name;
        // Thumbnail varsa onu döndür, yoksa orijinali
        $storagePath = str_replace('/storage/', '', $thumbPath);
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storagePath)) {
            return $thumbPath;
        }
        return $this->image_url;
    }

    /**
     * Get all order items for this product
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

