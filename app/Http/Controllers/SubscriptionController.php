<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    public function select()
    {
        // Aktif kullanıcılar da uzatmak için bu sayfayı görebilir
        return view('subscription.select');
    }

    public function request(Request $request)
    {
        $request->validate([
            'type' => 'required|in:monthly,quarterly,semi_yearly,yearly',
        ]);

        $user = auth()->user();

        $update = [
            'subscription_status'       => 'pending',
            'subscription_type'         => $request->type,
            'subscription_requested_at' => now(),
        ];

        // Eğer hâlâ aktif aboneliği devam ediyorsa expires_at'ı silme
        if (!$user->isSubscriptionActive()) {
            $update['subscription_expires_at'] = null;
        }

        $user->update($update);

        // Admin'e anında bildirim maili gönder
        $planLabels = [
            'monthly'    => 'Aylık',
            'quarterly'  => '3 Aylık',
            'semi_yearly'=> '6 Aylık',
            'yearly'     => 'Yıllık',
        ];
        $planLabel = $planLabels[$request->type] ?? $request->type;
        $adminEmail = config('mail.from.address');
        $adminUrl   = url('/admin');

        try {
            Mail::raw(
                "Yeni abonelik talebi!\n\n" .
                "Kullanıcı : {$user->name}\n" .
                "E-posta  : {$user->email}\n" .
                "Plan     : {$planLabel}\n" .
                "Tarih    : " . now()->format('d.m.Y H:i') . "\n\n" .
                "Admin panel: {$adminUrl}",
                function ($message) use ($adminEmail, $user) {
                    $message->to($adminEmail)
                            ->subject("[✉️ Kafe POS] Yeni abonelik talebi — {$user->name}");
                }
            );
        } catch (\Throwable $e) {
            // Mail gönderilemese bile akış bozulmasın
            \Illuminate\Support\Facades\Log::warning('Abonelik mail gönderilemedi: ' . $e->getMessage());
        }

        return redirect()->route('subscription.pending');
    }

    public function pending()
    {
        $user = auth()->user();

        // Aktifse ya da hâlâ geçerli aboneliği varken yenileme bekliyorsa geçir
        if ($user->isSubscriptionActive()) {
            return redirect()->route('adisyon.index');
        }

        if ($user->subscription_status === 'pending'
            && $user->subscription_expires_at
            && $user->subscription_expires_at->isFuture()) {
            return redirect()->route('adisyon.index');
        }

        return view('subscription.pending', compact('user'));
    }
}
