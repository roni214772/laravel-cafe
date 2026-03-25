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
        'role',
        'owner_id',
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

    // ─── Rol Yardımcıları ───────────────────────────────────────────
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isWaiter(): bool
    {
        return $this->role === 'waiter';
    }

    /**
     * Garson ise sahibini, sahipse kendisini döndürür.
     * Rooms ve Products sorguları bu ID ile yapılır.
     */
    public function effectiveOwnerId(): int
    {
        return $this->isWaiter() ? (int) $this->owner_id : (int) $this->id;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function waiters()
    {
        return $this->hasMany(User::class, 'owner_id');
    }
}
