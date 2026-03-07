<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use App\Models\PosOrder;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        // ── Masaları oluştur ─────────────────────────────────────
        $tables = [
            ['name' => 'Masa 1',   'status' => 'open'],
            ['name' => 'Masa 2',   'status' => 'open'],
            ['name' => 'Masa 3',   'status' => 'open'],
            ['name' => 'Masa 4',   'status' => 'open'],
            ['name' => 'Masa 5',   'status' => 'open'],
            ['name' => 'Masa 6',   'status' => 'closed'],
            ['name' => 'Masa 7',   'status' => 'closed'],
            ['name' => 'Masa 8',   'status' => 'closed'],
            ['name' => 'Teras 1',  'status' => 'open'],
            ['name' => 'Teras 2',  'status' => 'open'],
            ['name' => 'Bahçe 1',  'status' => 'closed'],
            ['name' => 'Bar',      'status' => 'open'],
        ];

        foreach ($tables as $tbl) {
            Room::create($tbl);
        }

        // ── Ürünleri al ─────────────────────────────────────────
        $products = Product::all()->keyBy('name');

        // ── Yardımcı: sipariş oluştur ───────────────────────────
        $makeOrder = function (int $roomId, array $items, int $openedMinutesAgo = 30) use ($products): void {
            $order = PosOrder::create([
                'room_id'    => $roomId,
                'status'     => 'open',
                'subtotal'   => 0,
                'vat'        => 0,
                'total'      => 0,
                'paid'       => 0,
                'due'        => 0,
                'opened_at'  => Carbon::now()->subMinutes($openedMinutesAgo),
            ]);

            $subtotal = 0;
            foreach ($items as [$productName, $qty]) {
                $p = $products[$productName] ?? null;
                if (!$p) continue;
                $lineTotal = $p->price * $qty;
                OrderItem::create([
                    'pos_order_id' => $order->id,
                    'product_id'   => $p->id,
                    'name'         => $p->name,
                    'category'     => $p->category,
                    'price'        => $p->price,
                    'quantity'     => $qty,
                    'total'        => $lineTotal,
                ]);
                $subtotal += $lineTotal;
            }

            $vat = round($subtotal * 0.10, 2);
            $total = round($subtotal + $vat, 2);
            $order->update([
                'subtotal' => $subtotal,
                'vat'      => $vat,
                'total'    => $total,
                'due'      => $total,
            ]);
        };

        // ── Aktif adisyonlar ────────────────────────────────────

        // Masa 1: Kahvaltı masası
        $makeOrder(1, [
            ['Serpme Kahvaltı', 2],
            ['Çay',             4],
        ], 45);

        // Masa 2: İş toplantısı
        $makeOrder(2, [
            ['Cappuccino',   3],
            ['Latte',        1],
            ['Türk Kahvesi', 1],
            ['Croissant',    2],
            ['Cheesecake',   2],
        ], 20);

        // Masa 3: Öğle yemeği
        $makeOrder(3, [
            ['Club Sandviç', 2],
            ['Sezar Salata', 1],
            ['Ayran',        2],
            ['Brownie',      1],
        ], 35);

        // Masa 4: Hafif sipariş
        $makeOrder(4, [
            ['Iced Latte',      2],
            ['Waffle',          1],
            ['Muffin Çikolata', 2],
        ], 10);

        // Masa 5: Akşam yemeği
        $makeOrder(5, [
            ['Izgara Köfte', 2],
            ['Makarna',      1],
            ['Tavuk Şiş',    1],
            ['Ayran',        3],
            ['Künefe',       2],
        ], 55);

        // Teras 1
        $makeOrder(9, [
            ['Limonata', 2],
            ['Frappe',   1],
            ['Poğaça',   3],
        ], 15);

        // Teras 2
        $makeOrder(10, [
            ['Americano', 2],
            ['Tost',      2],
        ], 8);

        // Bar
        $makeOrder(12, [
            ['Espresso',      4],
            ['Türk Kahvesi',  2],
            ['Tiramisu',      2],
        ], 5);
    }
}
