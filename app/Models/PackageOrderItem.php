<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageOrderItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'price'    => 'decimal:2',
        'total'    => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order()
    {
        return $this->belongsTo(PackageOrder::class, 'package_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
