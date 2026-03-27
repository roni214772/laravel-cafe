<?php

namespace App\Services;

use App\Models\PackageOrder;
use App\Models\PackageOrderItem;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrendyolService
{
    /**
     * Trendyol Go API'den yeni siparişleri çek
     *
     * Gerekli ayarlar (users tablosu veya settings):
     *  - trendyol_supplier_id
     *  - trendyol_api_key
     *  - trendyol_api_secret
     */
    public function fetchNewOrders(User $user): int
    {
        $supplierId = $user->getSetting('trendyol_supplier_id');
        $apiKey     = $user->getSetting('trendyol_api_key');
        $apiSecret  = $user->getSetting('trendyol_api_secret');

        if (!$supplierId || !$apiKey || !$apiSecret) {
            return 0;
        }

        $baseUrl = 'https://api.trendyol.com/sapigw/suppliers/' . $supplierId;

        try {
            // Son 1 saatteki Created/Received siparişleri çek
            $startDate = now()->subHour()->timestamp * 1000;
            $endDate   = now()->timestamp * 1000;

            $response = Http::withBasicAuth($apiKey, $apiSecret)
                ->timeout(15)
                ->get($baseUrl . '/orders', [
                    'status'        => 'Created',
                    'startDate'     => $startDate,
                    'endDate'       => $endDate,
                    'orderByField'  => 'CreatedDate',
                    'orderByDirection' => 'DESC',
                    'size'          => 50,
                ]);

            if (!$response->successful()) {
                Log::warning('Trendyol API hatası', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                    'user'   => $user->id,
                ]);
                return 0;
            }

            $data   = $response->json();
            $orders = $data['content'] ?? [];
            $count  = 0;

            foreach ($orders as $tOrder) {
                $platformOrderId = (string) ($tOrder['orderNumber'] ?? $tOrder['id'] ?? '');

                // Daha önce eklenmiş mi kontrol et
                $exists = PackageOrder::where('user_id', $user->id)
                    ->where('platform', 'trendyol')
                    ->where('platform_order_id', $platformOrderId)
                    ->exists();

                if ($exists) continue;

                // Müşteri bilgileri
                $shipping = $tOrder['shipmentAddress'] ?? [];
                $customerName  = trim(($shipping['firstName'] ?? '') . ' ' . ($shipping['lastName'] ?? ''));
                $customerPhone = $shipping['phone'] ?? $shipping['mobilePhone'] ?? null;
                $customerAddress = $shipping['fullAddress'] ?? null;

                // Sipariş oluştur
                $order = PackageOrder::create([
                    'user_id'           => $user->id,
                    'platform'          => 'trendyol',
                    'platform_order_id' => $platformOrderId,
                    'customer_name'     => $customerName ?: null,
                    'customer_phone'    => $customerPhone,
                    'customer_address'  => $customerAddress,
                    'customer_note'     => $tOrder['customerNote'] ?? null,
                    'status'            => 'new',
                    'payment_method'    => 'platform',
                ]);

                $subtotal = 0;
                $lines = $tOrder['lines'] ?? [];
                foreach ($lines as $line) {
                    $price = (float) ($line['price'] ?? $line['amount'] ?? 0);
                    $qty   = (int) ($line['quantity'] ?? 1);
                    $total = $price * $qty;
                    $subtotal += $total;

                    PackageOrderItem::create([
                        'package_order_id' => $order->id,
                        'name'             => $line['productName'] ?? 'Ürün',
                        'price'            => $price,
                        'quantity'         => $qty,
                        'total'            => $total,
                    ]);
                }

                $order->update([
                    'subtotal' => $subtotal,
                    'total'    => $subtotal,
                ]);

                $count++;
            }

            return $count;

        } catch (\Exception $e) {
            Log::error('Trendyol sipariş çekme hatası', [
                'error' => $e->getMessage(),
                'user'  => $user->id,
            ]);
            return 0;
        }
    }
}
