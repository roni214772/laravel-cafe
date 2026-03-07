<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnly
{
    // Sadece bu e-posta admin paneline girebilir
    private const ADMIN_EMAIL = 'bruskefrin47@gmail.com';

    public function handle(Request $request, Closure $next)
    {
        // Eğer şu an impersonation aktifse oturumu geri yükle, sonra devam et
        $originalId = $request->session()->get('impersonating_original');
        if ($originalId) {
            $original = \App\Models\User::find($originalId);
            if ($original && $original->email === self::ADMIN_EMAIL) {
                \Illuminate\Support\Facades\Auth::loginUsingId($originalId);
                $request->session()->forget('impersonating_original');
                // Admin paneline yönlendir (döngü olmasın diye next ile devam et)
                return $next($request);
            }
        }

        if (! auth()->check() || auth()->user()->email !== self::ADMIN_EMAIL) {
            abort(403, 'Bu sayfaya erişim yetkiniz yok.');
        }

        return $next($request);
    }
}
