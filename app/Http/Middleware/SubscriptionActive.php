<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SubscriptionActive
{

    // Abonelik kontrolü yapılmayan rotalar
    private const EXEMPT_ROUTES = [
        'subscription.select',
        'subscription.request',
        'subscription.pending',
        'payment.checkout',
        'payment.confirm-transfer',
        'payment.result',
        'logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Admin muaf
        if ($user->email === config('app.admin_email')) {
            return $next($request);
        }

        // Muaf rotalar
        if (in_array($request->route()?->getName(), self::EXEMPT_ROUTES)) {
            return $next($request);
        }

        // Garson ise sahibinin aboneliğini kontrol et
        if ($user->role === 'waiter' && $user->owner_id) {
            $owner = \App\Models\User::find($user->owner_id);
            if ($owner && $owner->isSubscriptionActive()) {
                return $next($request);
            }
            // Sahibinin aboneliği yoksa giriş engelle
            \Illuminate\Support\Facades\Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'İşletme sahibinin aboneliği aktif değil.']);
        }

        // Aboneliği aktif mi?
        if ($user->isSubscriptionActive()) {
            return $next($request);
        }

        // Yenileme talebi bekliyor ama eski abonelik süresi hâlâ geçerliyse → geçir
        if ($user->subscription_status === 'pending'
            && $user->subscription_expires_at
            && $user->subscription_expires_at->isFuture()) {
            return $next($request);
        }

        // Hiç abonelik talebi göndermemiş → seçim sayfasına
        if ($user->subscription_status === 'none') {
            return redirect()->route('subscription.select');
        }

        // Beklemede ya da reddedildi → bilgi sayfasına
        return redirect()->route('subscription.pending');
    }
}
