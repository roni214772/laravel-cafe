<?php

namespace App\Services;

use App\Models\PackageOrder;
use App\Models\PackageOrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YemeksepetiService
{
    /**
     * Yemeksepeti API'den yeni siparişleri çek
     *
     * Gerekli ayarlar:
     *  - ys_restaurant_id
     *  - ys_api_key
     *  - ys_api_secret
     */
    public function fetchNewOrders(User $user): int
    {
        $restaurantId = $user->getSetting('ys_restaurant_id');
        $apiKey       = $user->getSetting('ys_api_key');
        $apiSecret    = $user->getSetting('ys_api_secret');

        if (!$restaurantId || !$apiKey || !$apiSecret) {
            return 0;
        }

        // Yemeksepeti API base URL
        $baseUrl = 'https://api.yemeksepeti.com/v1';

        try {
            // Token al
            $tokenResponse = Http::timeout(10)
                ->post($baseUrl . '/auth/token', [
                    'api_key'    => $apiKey,
                    'api_secret' => $apiSecret,
                ]);

            if (!$tokenResponse->successful()) {
                Log::warning('Yemeksepeti auth hatası', [
                    'status' => $tokenResponse->status(),
                    'user'   => $user->id,
                ]);
                return 0;
            }

            $token = $tokenResponse->json('access_token');

            // Yeni siparişleri çek
            $response = Http::withToken($token)
                ->timeout(15)
                ->get($baseUrl . '/restaurants/' . $restaurantId . '/orders', [
                    'status' => 'new',
                    'limit'  => 50,
                ]);

            if (!$response->successful()) {
                Log::warning('Yemeksepeti sipariş çekme hatası', [
                    'status' => $response->status(),
                    'user'   => $user->id,
                ]);
                return 0;
            }

            $orders = $response->json('orders') ?? $response->json('data') ?? [];
            $count  = 0;

            foreach ($orders as $ysOrder) {
                $platformOrderId = (string) ($ysOrder['id'] ?? $ysOrder['order_id'] ?? '');

                $exists = PackageOrder::where('user_id', $user->id)
                    ->where('platform', 'yemeksepeti')
                    ->where('platform_order_id', $platformOrderId)
                    ->exists();

                if ($exists) continue;

                $order = PackageOrder::create([
                    'user_id'           => $user->id,
                    'platform'          => 'yemeksepeti',
                    'platform_order_id' => $platformOrderId,
                    'customer_name'     => $ysOrder['customer_name'] ?? $ysOrder['customerName'] ?? null,
                    'customer_phone'    => $ysOrder['customer_phone'] ?? $ysOrder['customerPhone'] ?? null,
                    'customer_address'  => $ysOrder['address'] ?? $ysOrder['delivery_address'] ?? null,
                    'customer_note'     => $ysOrder['note'] ?? $ysOrder['customer_note'] ?? null,
                    'status'            => 'new',
                    'payment_method'    => 'platform',
                ]);

                $subtotal = 0;
                $items = $ysOrder['items'] ?? $ysOrder['products'] ?? [];
                foreach ($items as $item) {
                    $price = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
                    $qty   = (int) ($item['quantity'] ?? $item['count'] ?? 1);
                    $total = $price * $qty;
                    $subtotal += $total;

                    PackageOrderItem::create([
                        'package_order_id' => $order->id,
                        'name'             => $item['name'] ?? $item['product_name'] ?? 'Ürün',
                        'price'            => $price,
                        'quantity'         => $qty,
                        'total'            => $total,
                        'note'             => $item['note'] ?? null,
                    ]);
                }

                $deliveryFee = (float) ($ysOrder['delivery_fee'] ?? 0);
                $discount    = (float) ($ysOrder['discount'] ?? 0);

                $order->update([
                    'subtotal'     => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'discount'     => $discount,
                    'total'        => $subtotal + $deliveryFee - $discount,
                ]);

                $count++;
            }

            return $count;

        } catch (\Exception $e) {
            Log::error('Yemeksepeti sipariş çekme hatası', [
                'error' => $e->getMessage(),
                'user'  => $user->id,
            ]);
            return 0;
        }
    }
}
