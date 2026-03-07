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

    /**
     * Get all order items for this product
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

