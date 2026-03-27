<?php

namespace App\Http\Controllers;

use App\Events\KitchenUpdated;
use App\Models\PackageOrder;
use App\Models\PackageOrderItem;
use Illuminate\Http\Request;

class PackageOrderController extends Controller
{
    // ─── Sipariş listesi ────────────────────────────────────────────
    public function index(Request $request)
    {
        $ownerId = auth()->user()->effectiveOwnerId();
        $status  = $request->query('status', 'active');

        $query = PackageOrder::where('user_id', $ownerId)->orderByDesc('created_at');

        if ($status === 'active') {
            $query->whereIn('status', ['new', 'preparing', 'ready', 'on_way']);
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->with('items')->limit(100)->get();

        return response()->json([
            'orders' => $orders->map(fn($o) => $this->toArray($o)),
        ]);
    }

    // ─── Yeni sipariş (manuel) ──────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'platform'         => 'required|in:trendyol,yemeksepeti,getir,telefon,diger',
            'customer_name'    => 'nullable|string|max:100',
            'customer_phone'   => 'nullable|string|max:30',
            'customer_address' => 'nullable|string|max:500',
            'customer_note'    => 'nullable|string|max:500',
            'payment_method'   => 'nullable|in:platform,cash,card',
            'items'            => 'required|array|min:1',
            'items.*.name'     => 'required|string|max:100',
            'items.*.price'    => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note'     => 'nullable|string|max:200',
        ]);

        $ownerId = auth()->user()->effectiveOwnerId();

        $order = PackageOrder::create([
            'user_id'        => $ownerId,
            'platform'       => $request->platform,
            'customer_name'  => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_note'  => $request->customer_note,
            'payment_method' => $request->payment_method ?? 'platform',
            'status'         => 'new',
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $total = $item['price'] * $item['quantity'];
            $subtotal += $total;
            PackageOrderItem::create([
                'package_order_id' => $order->id,
                'name'             => $item['name'],
                'price'            => $item['price'],
                'quantity'         => $item['quantity'],
                'total'            => $total,
                'note'             => $item['note'] ?? null,
            ]);
        }

        $order->update(['subtotal' => $subtotal, 'total' => $subtotal]);

        $this->broadcastKitchen();

        return response()->json([
            'success' => true,
            'order'   => $this->toArray($order->fresh()->load('items')),
        ]);
    }

    // ─── Durum güncelle ─────────────────────────────────────────────
    public function updateStatus(Request $request, PackageOrder $packageOrder)
    {
        $this->authorizeOrder($packageOrder);

        $request->validate([
            'status' => 'required|in:new,preparing,ready,on_way,delivered,cancelled',
        ]);

        $s = $request->status;
        $updates = ['status' => $s];

        if ($s === 'preparing' && !$packageOrder->accepted_at) $updates['accepted_at'] = now();
        if ($s === 'ready')     $updates['ready_at'] = now();
        if ($s === 'delivered') { $updates['delivered_at'] = now(); $updates['is_paid'] = true; }

        $packageOrder->update($updates);
        $this->broadcastKitchen();

        return response()->json([
            'success' => true,
            'order'   => $this->toArray($packageOrder->fresh()->load('items')),
        ]);
    }

    // ─── Sil ────────────────────────────────────────────────────────
    public function destroy(PackageOrder $packageOrder)
    {
        $this->authorizeOrder($packageOrder);
        $packageOrder->items()->delete();
        $packageOrder->delete();

        return response()->json(['success' => true]);
    }

    // ─── İstatistikler ──────────────────────────────────────────────
    public function stats()
    {
        $ownerId = auth()->user()->effectiveOwnerId();
        $today   = now()->startOfDay();

        return response()->json([
            'new'       => PackageOrder::where('user_id', $ownerId)->where('status', 'new')->count(),
            'preparing' => PackageOrder::where('user_id', $ownerId)->where('status', 'preparing')->count(),
            'ready'     => PackageOrder::where('user_id', $ownerId)->where('status', 'ready')->count(),
            'on_way'    => PackageOrder::where('user_id', $ownerId)->where('status', 'on_way')->count(),
            'today_delivered' => PackageOrder::where('user_id', $ownerId)->where('status', 'delivered')
                ->where('delivered_at', '>=', $today)->count(),
            'today_total' => (float) PackageOrder::where('user_id', $ownerId)->where('status', 'delivered')
                ->where('delivered_at', '>=', $today)->sum('total'),
        ]);
    }

    // ─── Platform ayarlarını kaydet ─────────────────────────────────
    public function saveSettings(Request $request)
    {
        $user = auth()->user();
        $allowed = [
            'trendyol_supplier_id', 'trendyol_api_key', 'trendyol_api_secret',
            'ys_restaurant_id', 'ys_api_key', 'ys_api_secret',
            'getir_restaurant_id', 'getir_api_token',
        ];

        $data = $request->only($allowed);
        $user->setSettings($data);

        return response()->json(['success' => true]);
    }

    // ─── Platform ayarlarını getir ──────────────────────────────────
    public function getSettings()
    {
        $user = auth()->user();
        $keys = [
            'trendyol_supplier_id', 'trendyol_api_key', 'trendyol_api_secret',
            'ys_restaurant_id', 'ys_api_key', 'ys_api_secret',
            'getir_restaurant_id', 'getir_api_token',
        ];

        $settings = [];
        foreach ($keys as $key) {
            $val = $user->getSetting($key);
            // API key/secret'leri maskele
            if ($val && str_contains($key, 'secret')) {
                $settings[$key] = str_repeat('•', max(0, strlen($val) - 4)) . substr($val, -4);
            } else {
                $settings[$key] = $val;
            }
        }

        return response()->json($settings);
    }

    // ─── Bağlantı testi ─────────────────────────────────────────────
    public function testConnection(Request $request)
    {
        $platform = $request->input('platform');
        $user     = auth()->user();

        try {
            $count = match ($platform) {
                'trendyol'    => (new \App\Services\TrendyolService())->fetchNewOrders($user),
                'yemeksepeti' => (new \App\Services\YemeksepetiService())->fetchNewOrders($user),
                'getir'       => (new \App\Services\GetirService())->fetchNewOrders($user),
                default       => -1,
            };

            if ($count >= 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Bağlantı başarılı! {$count} yeni sipariş çekildi.",
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Bilinmeyen platform']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
            ]);
        }
    }

    // ─── Webhook: Dış platformlardan sipariş al ────────────────────
    public function webhook(Request $request)
    {
        $token = $request->header('X-API-Token') ?? $request->query('token');
        if (!$token) {
            return response()->json(['error' => 'Token gerekli'], 401);
        }

        $user = \App\Models\User::where('menu_token', $token)->first();
        if (!$user) {
            return response()->json(['error' => 'Geçersiz token'], 401);
        }

        $request->validate([
            'platform'         => 'required|string|max:50',
            'platform_order_id' => 'nullable|string|max:100',
            'customer_name'    => 'nullable|string|max:100',
            'customer_phone'   => 'nullable|string|max:30',
            'customer_address' => 'nullable|string|max:500',
            'customer_note'    => 'nullable|string|max:500',
            'items'            => 'required|array|min:1',
            'items.*.name'     => 'required|string|max:100',
            'items.*.price'    => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note'     => 'nullable|string|max:200',
        ]);

        $order = PackageOrder::create([
            'user_id'           => $user->id,
            'platform'          => $request->platform,
            'platform_order_id' => $request->platform_order_id,
            'customer_name'     => $request->customer_name,
            'customer_phone'    => $request->customer_phone,
            'customer_address'  => $request->customer_address,
            'customer_note'     => $request->customer_note,
            'payment_method'    => 'platform',
            'status'            => 'new',
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $total = $item['price'] * $item['quantity'];
            $subtotal += $total;
            PackageOrderItem::create([
                'package_order_id' => $order->id,
                'name'             => $item['name'],
                'price'            => $item['price'],
                'quantity'         => $item['quantity'],
                'total'            => $total,
                'note'             => $item['note'] ?? null,
            ]);
        }

        $order->update(['subtotal' => $subtotal, 'total' => $subtotal]);

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    // ─── Helpers ────────────────────────────────────────────────────
    private function authorizeOrder(PackageOrder $order): void
    {
        if ($order->user_id !== auth()->user()->effectiveOwnerId()) {
            abort(403);
        }
    }

    private function broadcastKitchen(): void
    {
        try {
            $mutfak = app(\App\Http\Controllers\MutfakController::class);
            broadcast(new KitchenUpdated(
                auth()->user()->effectiveOwnerId(),
                $mutfak->getOrdersPublic()
            ))->toOthers();
        } catch (\Throwable $e) {}
    }

    private function toArray(PackageOrder $o): array
    {
        return [
            'id'                => $o->id,
            'platform'          => $o->platform,
            'platform_label'    => PackageOrder::platformLabel($o->platform),
            'platform_order_id' => $o->platform_order_id,
            'customer_name'     => $o->customer_name,
            'customer_phone'    => $o->customer_phone,
            'customer_address'  => $o->customer_address,
            'customer_note'     => $o->customer_note,
            'status'            => $o->status,
            'status_label'      => PackageOrder::statusLabel($o->status),
            'subtotal'          => (float) $o->subtotal,
            'delivery_fee'      => (float) $o->delivery_fee,
            'discount'          => (float) $o->discount,
            'total'             => (float) $o->total,
            'payment_method'    => $o->payment_method,
            'is_paid'           => $o->is_paid,
            'created_at'        => $o->created_at?->format('H:i'),
            'created_date'      => $o->created_at?->format('d.m.Y'),
            'items'             => $o->items->map(fn($i) => [
                'name'     => $i->name,
                'price'    => (float) $i->price,
                'quantity' => $i->quantity,
                'total'    => (float) $i->total,
                'note'     => $i->note,
            ])->toArray(),
        ];
    }
}
