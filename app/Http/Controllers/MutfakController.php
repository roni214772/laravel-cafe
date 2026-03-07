<?php

namespace App\Http\Controllers;

use App\Events\KitchenUpdated;
use App\Models\OrderItem;
use App\Models\Room;
use Illuminate\Http\Request;

class MutfakController extends Controller
{
    public function index(Request $request)
    {
        $orders = $this->getOrders();
        return view('mutfak.index', compact('orders'));
    }

    /**
     * AJAX polling endpoint — JSON döner, sayfa yenilemez
     */
    public function poll(Request $request)
    {
        return response()->json($this->getOrders());
    }

    public function markReady(Request $request)
    {
        $orderId = $request->input('table_id');

        // Sadece bu adisyondaki 'pending' kalemleri 'ready' yap
        // Siparişi ve masayı kapatma — masa açık kalsın
        OrderItem::where('pos_order_id', $orderId)
            ->where('kitchen_status', 'pending')
            ->update(['kitchen_status' => 'ready']);

        // WebSocket: mutfak ekranını anlık güncelle (Reverb çalışmıyorsa sessizce atla)
        try {
            broadcast(new KitchenUpdated(
                auth()->id(),
                $this->getOrders()
            ))->toOthers();
        } catch (\Throwable $e) {
            // Reverb sunucusu kapalıysa ana işlemi engelleme
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('mutfak.index');
    }

    /**
     * Public wrapper — AdisyonController'dan broadcast için kullanılır
     */
    public function getOrdersPublic(): array
    {
        return $this->getOrders();
    }

    private function getOrders(): array
    {
        $rooms = Room::with(['pos_orders' => function ($q) {
            $q->where('status', 'open')
              ->with(['order_items' => function ($qi) {
                  $qi->where('kitchen_status', 'pending');
              }]);
        }])
        ->where('status', 'open')
        ->where('user_id', auth()->id())
        ->get();

        $orders = [];

        foreach ($rooms as $room) {
            $openOrder = $room->pos_orders->first();
            if (!$openOrder || $openOrder->order_items->isEmpty()) {
                continue;
            }

            $items = $openOrder->order_items->map(fn($item) => [
                'id'     => $item->id,
                'qty'    => fmod((float)$item->quantity, 1) == 0 ? (int)$item->quantity : (float)$item->quantity,
                'name'   => $item->name,
                'cat'    => $item->category,
                'status' => $item->kitchen_status,
                'note'   => $item->note ?? '',
            ])->toArray();

            $orders[] = [
                'id'     => $openOrder->id,
                'name'   => $room->name,
                'opened' => $openOrder->opened_at?->format('H:i') ?? $openOrder->created_at->format('H:i'),
                'note'   => $openOrder->note ?? null,
                'items'  => $items,
            ];
        }

        return $orders;
    }
}
