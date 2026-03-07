<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'order_items';

    protected $fillable = [
        'pos_order_id',
        'product_id',
        'name',
        'category',
        'price',
        'quantity',
        'total',
        'kitchen_status',
        'note',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'quantity' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(PosOrder::class, 'pos_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
