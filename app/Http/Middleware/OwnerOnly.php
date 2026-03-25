<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OwnerOnly
{
    /**
     * Garsonların erişemeyeceği rotaları korur (ödeme, ürün CRUD, rapor, ayarlar vb.)
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && $user->role === 'waiter') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Bu işlem için yetkiniz yok.'], 403);
            }
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        return $next($request);
    }
}
