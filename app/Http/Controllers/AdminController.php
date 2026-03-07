<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::withCount(['rooms', 'products'])
            ->with(['rooms' => function ($q) {
                $q->withCount(['pos_orders as order_count']);
            }])
            ->orderByRaw("CASE subscription_status WHEN 'pending' THEN 0 WHEN 'active' THEN 1 WHEN 'rejected' THEN 2 WHEN 'expired' THEN 3 ELSE 4 END")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                $user->total_orders = $user->rooms->sum('order_count');
                return $user;
            });

        $pendingCount = $users->where('subscription_status', 'pending')->count();
        $totalUsers   = $users->count();
        $totalRooms   = $users->sum('rooms_count');
        $totalOrders  = $users->sum('total_orders');

        $priceMonthly   = Setting::get('price_monthly',    '299.00');
        $priceQuarterly = Setting::get('price_quarterly',  '799.00');
        $priceSemi      = Setting::get('price_semi_yearly','1490.00');
        $priceYearly    = Setting::get('price_yearly',     '2990.00');
        $bankName       = Setting::get('bank_name',           'Ziraat Bankası');
        $bankIban       = Setting::get('bank_iban',           'TR00 0000 0000 0000 0000 0000 00');
        $bankHolder     = Setting::get('bank_account_holder', 'Ad Soyad');

        return view('admin.index', compact('users', 'totalUsers', 'totalRooms', 'totalOrders', 'pendingCount', 'priceMonthly', 'priceQuarterly', 'priceSemi', 'priceYearly', 'bankName', 'bankIban', 'bankHolder'));
    }

    public function approveSubscription(User $user, \Illuminate\Http\Request $request)
    {
        // extend_type form'dan geliyorsa onu kullan, yoksa kullanıcının mevcut tipine bak
        $validTypes = ['monthly', 'quarterly', 'semi_yearly', 'yearly'];
        $type = in_array($request->input('extend_type'), $validTypes)
                    ? $request->input('extend_type')
                    : (in_array($user->subscription_type, $validTypes) ? $user->subscription_type : 'monthly');

        $monthsMap = ['monthly' => 1, 'quarterly' => 3, 'semi_yearly' => 6, 'yearly' => 12];
        $months    = $monthsMap[$type];
        $labelMap  = ['monthly' => 'Aylık', 'quarterly' => '3 Aylık', 'semi_yearly' => '6 Aylık', 'yearly' => 'Yıllık'];

        // Mevcut aktif abonelik varsa üzerine ekle
        $base = ($user->subscription_expires_at && $user->subscription_expires_at->isFuture())
            ? $user->subscription_expires_at
            : now();

        $user->update([
            'subscription_status'      => 'active',
            'subscription_type'        => $type,
            'subscription_expires_at'  => $base->copy()->addMonths($months),
        ]);

        return back()->with('status', "{$user->name} aboneliği onaylandı ({$labelMap[$type]}).");
    }

    public function updatePrices(Request $request)
    {
        $request->validate([
            'price_monthly'    => 'required|numeric|min:1',
            'price_quarterly'  => 'required|numeric|min:1',
            'price_semi_yearly'=> 'required|numeric|min:1',
            'price_yearly'     => 'required|numeric|min:1',
        ]);

        Setting::set('price_monthly',    number_format((float) $request->price_monthly,    2, '.', ''));
        Setting::set('price_quarterly',  number_format((float) $request->price_quarterly,  2, '.', ''));
        Setting::set('price_semi_yearly',number_format((float) $request->price_semi_yearly, 2, '.', ''));
        Setting::set('price_yearly',     number_format((float) $request->price_yearly,     2, '.', ''));

        return back()->with('status', 'Abonelik fiyatları güncellendi.');
    }

    public function updateBankSettings(Request $request)
    {
        $request->validate([
            'bank_name'           => 'required|string|max:100',
            'bank_iban'           => 'required|string|max:40',
            'bank_account_holder' => 'required|string|max:100',
        ]);

        Setting::set('bank_name',           $request->bank_name);
        Setting::set('bank_iban',           strtoupper(preg_replace('/\s+/', ' ', trim($request->bank_iban))));
        Setting::set('bank_account_holder', $request->bank_account_holder);

        return back()->with('status', 'Banka bilgileri güncellendi.');
    }

    public function rejectSubscription(User $user)
    {
        $user->update([
            'subscription_status' => 'rejected',
        ]);

        return back()->with('status', "{$user->name} abonelik talebi reddedildi.");
    }

    public function cancelSubscription(User $user)
    {
        // Kullanıcının süresi devam ediyorsa sadece talebi iptal et,
        // mevcut aktif aboneliğe dokunma
        if ($user->subscription_expires_at && $user->subscription_expires_at->isFuture()) {
            $user->update([
                'subscription_status' => 'active',
                // subscription_type ve expires_at değişmez — kalan süre korunur
            ]);
            return back()->with('status', "{$user->name} yenileme talebi iptal edildi. Mevcut abonelik süresi devam ediyor.");
        }

        // Süresi dolmuş veya hiç yoksa tamamen sıfırla
        $user->update([
            'subscription_status'     => 'none',
            'subscription_type'       => null,
            'subscription_expires_at' => null,
        ]);

        return back()->with('status', "{$user->name} aboneliği iptal edildi.");
    }

    public function changePassword(User $user, Request $request)
    {
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password),
        ]);

        return back()->with('status', "{$user->name} şifresi başarıyla değiştirildi.");
    }

    public function impersonate(User $user)
    {
        // Kendine bürünmeyi engelle
        if ($user->id === auth()->id()) {
            return back();
        }

        // Orijinal admin id'sini session'a kaydet
        session(['impersonating_original' => auth()->id()]);

        Auth::loginUsingId($user->id);

        return redirect()->route('adisyon.index')
            ->with('status', "{$user->name} hesabına geçiş yapıldı.");
    }

    public function stopImpersonate(Request $request)
    {
        $originalId = session('impersonating_original');

        if (! $originalId) {
            return redirect()->route('adisyon.index');
        }

        session()->forget('impersonating_original');
        Auth::loginUsingId($originalId);

        return redirect()->route('admin.index')
            ->with('status', 'Kendi hesabınıza geri döndünüz.');
    }

    public function deleteUser(User $user)
    {
        // Admin kendini silemez
        if ($user->email === auth()->user()->email) {
            return back()->withErrors(['msg' => 'Kendi hesabınızı silemezsiniz.']);
        }

        $name = $user->name;

        // Kullanıcının tüm maları, ürünleri ve siparişleri temizle
        foreach ($user->rooms as $room) {
            foreach ($room->pos_orders as $order) {
                $order->order_items()->delete();
                $order->payments()->delete();
                $order->delete();
            }
            $room->delete();
        }
        $user->products()->delete();
        $user->delete();

        return back()->with('status', "{$name} hesabı ve tüm verileri silindi.");
    }
}
