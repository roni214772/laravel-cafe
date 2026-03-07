<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'menu_token',
        'ui_settings',
        'subscription_status',
        'subscription_type',
        'subscription_requested_at',
        'subscription_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'          => 'datetime',
            'password'                   => 'hashed',
            'subscription_requested_at'  => 'datetime',
            'subscription_expires_at'    => 'datetime',
        ];
    }

    public function isSubscriptionActive(): bool
    {
        return $this->subscription_status === 'active'
            && ($this->subscription_expires_at === null || $this->subscription_expires_at->isFuture());
    }

    public function rooms()
    {
        return $this->hasMany(\App\Models\Room::class);
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }
}
