<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'status',
        'opened_at',
        'closed_at',
        'note',
        'vat_rate',
        'service_rate',
        'discount_type',
        'discount_value',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'vat_rate' => 'decimal:2',
        'service_rate' => 'decimal:2',
        'discount_value' => 'decimal:2',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(PosOrder::class);
    }
    
    public function pos_orders(): HasMany
    {
        return $this->hasMany(PosOrder::class);
    }
}

