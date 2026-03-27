<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageOrder extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount'     => 'decimal:2',
        'total'        => 'decimal:2',
        'is_paid'      => 'boolean',
        'accepted_at'  => 'datetime',
        'ready_at'     => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PackageOrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Platform etiketleri ──
    public static function platformLabel(string $platform): string
    {
        return match ($platform) {
            'trendyol'    => 'Trendyol Go',
            'yemeksepeti' => 'Yemeksepeti',
            'getir'       => 'Getir Yemek',
            'telefon'     => 'Telefon',
            default       => 'Diğer',
        };
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'new'       => 'Yeni',
            'preparing' => 'Hazırlanıyor',
            'ready'     => 'Hazır',
            'on_way'    => 'Yolda',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal',
            default     => $status,
        };
    }
}
