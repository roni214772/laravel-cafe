<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // ── Banka havalesi bilgi sayfası ─────────────────────────────
    public function checkout(Request $request)
    {
        $validPlans = ['monthly', 'quarterly', 'semi_yearly', 'yearly'];
        $plan = in_array($request->query('plan'), $validPlans)
            ? $request->query('plan')
            : 'monthly';

        $priceMap = [
            'monthly'    => Setting::get('price_monthly',    '299.00'),
            'quarterly'  => Setting::get('price_quarterly',  '799.00'),
            'semi_yearly'=> Setting::get('price_semi_yearly','1490.00'),
            'yearly'     => Setting::get('price_yearly',     '2990.00'),
        ];
        $labelMap = [
            'monthly'    => 'Aylık Abonelik (1 ay)',
            'quarterly'  => '3 Aylık Abonelik',
            'semi_yearly'=> '6 Aylık Abonelik',
            'yearly'     => 'Yıllık Abonelik (12 ay)',
        ];
        $descMap = [
            'monthly'    => 'Aylik',
            'quarterly'  => '3 Aylik',
            'semi_yearly'=> '6 Aylik',
            'yearly'     => 'Yillik',
        ];
        $price         = $priceMap[$plan];
        $label         = $labelMap[$plan];
        $bankName      = Setting::get('bank_name',           'Ziraat Bankası');
        $iban          = Setting::get('bank_iban',           'TR00 0000 0000 0000 0000 0000 00');
        $accountHolder = Setting::get('bank_account_holder', 'Ad Soyad');
        $desc          = auth()->user()->name . ' - ' . $descMap[$plan] . ' Abonelik';

        return view('payment.checkout', compact(
            'plan', 'price', 'label', 'bankName', 'iban', 'accountHolder', 'desc'
        ));
    }

    // ── Kullanıcı "Ödedim" butonuna bastı ────────────────────────
    public function confirmTransfer(Request $request)
    {
        $validPlans = ['monthly', 'quarterly', 'semi_yearly', 'yearly'];
        $plan = in_array($request->input('plan'), $validPlans)
            ? $request->input('plan')
            : 'monthly';

        $user = auth()->user();

        $update = [
            'subscription_status'       => 'pending',
            'subscription_type'         => $plan,
            'subscription_requested_at' => now(),
        ];

        // Hâlâ aktif aboneliği devam ediyorsa expires_at'ı silme
        if (!$user->isSubscriptionActive()) {
            $update['subscription_expires_at'] = null;
        }

        $user->update($update);

        // Admin'e anında bildirim maili gönder
        $planLabels = [
            'monthly'     => 'Aylık',
            'quarterly'   => '3 Aylık',
            'semi_yearly' => '6 Aylık',
            'yearly'      => 'Yıllık',
        ];
        $priceMap = [
            'monthly'     => Setting::get('price_monthly',    '299.00'),
            'quarterly'   => Setting::get('price_quarterly',  '799.00'),
            'semi_yearly' => Setting::get('price_semi_yearly','1490.00'),
            'yearly'      => Setting::get('price_yearly',     '2990.00'),
        ];
        $planLabel  = $planLabels[$plan] ?? $plan;
        $tutar      = $priceMap[$plan];
        $adminEmail = config('mail.from.address');
        $adminUrl   = url('/admin');
        $iban       = Setting::get('bank_iban', '-');

        try {
            Mail::raw(
                "💸 Havale bildirimi geldi!\n\n" .
                "Kullanıcı : {$user->name}\n" .
                "E-posta  : {$user->email}\n" .
                "Plan     : {$planLabel}\n" .
                "Tutar    : ₺{$tutar}\n" .
                "IBAN     : {$iban}\n" .
                "Tarih    : " . now()->format('d.m.Y H:i') . "\n\n" .
                "Havaleyi kontrol edip onaylamak için:\n{$adminUrl}",
                function ($message) use ($adminEmail, $user, $planLabel) {
                    $message->to($adminEmail)
                            ->subject("[💸 Kafe POS] Havale bildirimi — {$user->name} / {$planLabel}");
                }
            );
        } catch (\Throwable $e) {
            Log::warning('Havale bildirim maili gönderilemedi: ' . $e->getMessage());
        }

        return redirect()->route('payment.result')
            ->with('payment_pending', true)
            ->with('plan', $plan);
    }

    // ── Sonuç sayfası ────────────────────────────────────────────
    public function result()
    {
        return view('payment.result');
    }
}
