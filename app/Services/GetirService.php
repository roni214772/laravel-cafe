<?php

namespace App\Services;

use App\Models\PackageOrder;
use App\Models\PackageOrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetirService
{
    /**
     * Getir Yemek API'den yeni siparişleri çek
     *
     * Gerekli ayarlar:
     *  - getir_restaurant_id
     *  - getir_api_token
     */
    public function fetchNewOrders(User $user): int
    {
        $restaurantId = $user->getSetting('getir_restaurant_id');
        $apiToken     = $user->getSetting('getir_api_token');

        if (!$restaurantId || !$apiToken) {
            return 0;
        }

        $baseUrl = 'https://food-api.getir.com/v1';

        try {
            $response = Http::withToken($apiToken)
                ->timeout(15)
                ->get($baseUrl . '/restaurants/' . $restaurantId . '/orders', [
                    'status' => 'new',
                    'limit'  => 50,
                ]);

            if (!$response->successful()) {
                Log::warning('Getir API hatası', [
                    'status' => $response->status(),
                    'user'   => $user->id,
                ]);
                return 0;
            }

            $orders = $response->json('orders') ?? $response->json('data') ?? [];
            $count  = 0;

            foreach ($orders as $gOrder) {
                $platformOrderId = (string) ($gOrder['id'] ?? $gOrder['order_id'] ?? '');

                $exists = PackageOrder::where('user_id', $user->id)
                    ->where('platform', 'getir')
                    ->where('platform_order_id', $platformOrderId)
                    ->exists();

                if ($exists) continue;

                $client = $gOrder['client'] ?? $gOrder['customer'] ?? [];

                $order = PackageOrder::create([
                    'user_id'           => $user->id,
                    'platform'          => 'getir',
                    'platform_order_id' => $platformOrderId,
                    'customer_name'     => $client['name'] ?? $client['fullName'] ?? null,
                    'customer_phone'    => $client['phone'] ?? $client['phoneNumber'] ?? null,
                    'customer_address'  => $gOrder['address'] ?? ($client['address'] ?? null),
                    'customer_note'     => $gOrder['note'] ?? $gOrder['clientNote'] ?? null,
                    'status'            => 'new',
                    'payment_method'    => 'platform',
                ]);

                $subtotal = 0;
                $products = $gOrder['products'] ?? $gOrder['items'] ?? [];
                foreach ($products as $product) {
                    $price = (float) ($product['price'] ?? $product['unitPrice'] ?? 0);
                    $qty   = (int) ($product['count'] ?? $product['quantity'] ?? 1);
                    $total = $price * $qty;
                    $subtotal += $total;

                    PackageOrderItem::create([
                        'package_order_id' => $order->id,
                        'name'             => $product['name'] ?? $product['productName'] ?? 'Ürün',
                        'price'            => $price,
                        'quantity'         => $qty,
                        'total'            => $total,
                        'note'             => $product['note'] ?? null,
                    ]);
                }

                $deliveryFee = (float) ($gOrder['courierFee'] ?? $gOrder['deliveryFee'] ?? 0);
                $discount    = (float) ($gOrder['discount'] ?? $gOrder['totalDiscount'] ?? 0);

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
            Log::error('Getir sipariş çekme hatası', [
                'error' => $e->getMessage(),
                'user'  => $user->id,
            ]);
            return 0;
        }
    }
}
