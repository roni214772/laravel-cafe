<?php

namespace App\Http\Controllers;

use App\Events\AdisyonUpdated;
use App\Events\KitchenUpdated;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PosOrder;
use App\Models\Product;
use App\Models\Room;
use Illuminate\Http\Request;

class AdisyonController extends Controller
{
    // ─── Main Page ──────────────────────────────────────────────────
    public function index()
    {
        $ownerId    = auth()->user()->effectiveOwnerId();
        $rooms      = Room::where('user_id', $ownerId)->orderBy('id')->get();
        $products   = Product::where('user_id', $ownerId)->orderBy('name')->get()->groupBy('category');
        $uiSettings = json_decode(auth()->user()->ui_settings ?? '{}', true) ?: [];
        $userRole   = auth()->user()->role;
        return response()
            ->view('adisyon.index', compact('rooms', 'products', 'uiSettings', 'userRole'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    // ─── Get room order data (AJAX) ─────────────────────────────────
    public function masaData(Room $room)
    {
        $this->authorizeRoom($room);
        $order = PosOrder::where('room_id', $room->id)
            ->where('status', 'open')
            ->first();

        if (!$order) {
            return response()->json([
                'room'  => $this->roomArray($room),
                'order' => null,
                'items' => [],
            ]);
        }

        $items = OrderItem::where('pos_order_id', $order->id)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'room'  => $this->roomArray($room),
            'order' => $this->orderArray($order),
            'items' => $items->map(fn($i) => $this->itemArray($i))->values(),
        ]);
    }

    // ─── Add item (AJAX) ────────────────────────────────────────────
    public function ekle(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'adet'       => 'nullable|integer|min:1',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('user_id', auth()->user()->effectiveOwnerId())
            ->firstOrFail();
        $adet    = (int) ($request->adet ?? 1);
        $order   = $this->getOrCreateOrder($room);

        $existing = OrderItem::where('pos_order_id', $order->id)
            ->where('product_id', $product->id)
            ->where('kitchen_status', 'draft')
            ->first();

        if ($existing) {
            $existing->quantity += $adet;
            $existing->total     = $existing->quantity * $existing->price;
            $existing->save();
        } else {
            OrderItem::create([
                'pos_order_id'   => $order->id,
                'product_id'     => $product->id,
                'name'           => $product->name,
                'category'       => $product->category,
                'price'          => $product->price,
                'quantity'       => $adet,
                'total'          => $product->price * $adet,
                'kitchen_status' => 'draft',
            ]);
        }

        $this->recalcOrder($order);
        $this->broadcastAdisyon($room, 'item_added');
        return $this->masaData($room);
    }

    // ─── Remove single item (AJAX) ──────────────────────────────────
    public function silItem(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $request->validate(['item_id' => 'required|exists:order_items,id']);
        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if ($order) {
            OrderItem::where('id', $request->item_id)
                ->where('pos_order_id', $order->id)
                ->delete();
            $this->recalcOrder($order);
        }
        $this->broadcastAdisyon($room, 'item_removed');
        return $this->masaData($room);
    }

    // ─── Update item quantity (AJAX) ────────────────────────────────
    public function updateQty(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'qty'     => 'required|numeric|min:0.5',
        ]);
        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if ($order) {
            $item = OrderItem::where('id', $request->item_id)
                ->where('pos_order_id', $order->id)
                ->first();
            if ($item) {
                $item->quantity = (float) $request->qty;
                $item->total    = $item->price * $item->quantity;
                $item->save();
                $this->recalcOrder($order);
            }
        }
        $this->broadcastAdisyon($room, 'qty_updated');
        return $this->masaData($room);
    }

    // ─── Clear order items (AJAX) ───────────────────────────────────
    public function temizle(Room $room)
    {
        $this->authorizeRoom($room);
        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if ($order) {
            OrderItem::where('pos_order_id', $order->id)->delete();
            $this->recalcOrder($order);
        }
        $this->broadcastAdisyon($room, 'order_cleared');
        return $this->masaData($room);
    }

    // ─── Process payment (AJAX) ─────────────────────────────────────
    public function odemeAl(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if (!$order) {
            return response()->json(['error' => 'Acik siparis bulunamadi.'], 404);
        }

        $subtotal     = (float) $order->subtotal;
        $kdvPct       = (float) ($request->kdv ?? 0);
        $servisPct    = (float) ($request->servis ?? 0);
        $indirimTipi  = $request->indirim_tipi ?? 'Yok';
        $indirimDeger = (float) ($request->indirim ?? 0);
        $paidNow      = (float) ($request->paid ?? 0);

        $paymentTypeMap = ['Nakit' => 'cash', 'Kredi Kartı' => 'card', 'Havale' => 'mixed'];
        $paymentType    = $paymentTypeMap[$request->payment_type] ?? 'cash';

        $indirim = 0;
        if ($indirimTipi === 'Tutar')  $indirim = $indirimDeger;
        if ($indirimTipi === 'Yuzde')  $indirim = $subtotal * $indirimDeger / 100;

        $servisAmount = $subtotal * $servisPct / 100;
        $kdvAmount    = ($subtotal + $servisAmount - $indirim) * $kdvPct / 100;
        $total        = $subtotal + $servisAmount - $indirim + $kdvAmount;

        // Daha önce ödenen tutarı al
        $previouslyPaid = (float) $order->paid;

        // Alınan tutar boşsa (0) → kalan tamamını al
        if ($paidNow <= 0) {
            $paidNow = max(0, $total - $previouslyPaid);
        }

        $totalPaid = $previouslyPaid + $paidNow;
        $due       = max(0, round($total - $totalPaid, 2));

        // Her ödemeyi payments tablosuna kaydet
        Payment::create([
            'pos_order_id' => $order->id,
            'amount'       => $paidNow,
            'type'         => $paymentType,
        ]);

        // Siparişi güncelle
        $orderData = [
            'subtotal'     => $subtotal,
            'vat'          => $kdvAmount,
            'service'      => $servisAmount,
            'discount'     => $indirim,
            'total'        => $total,
            'paid'         => $totalPaid,
            'due'          => $due,
            'payment_type' => $paymentType,
            'note'         => $request->nota ?? $order->note,
        ];

        // Kalan 0 ise masayı kapat, değilse açık bırak
        if ($due <= 0) {
            $orderData['status']    = 'closed';
            $orderData['closed_at'] = now();
            $order->update($orderData);
            $room->update(['status' => 'closed', 'closed_at' => now()]);

            $this->broadcastAdisyon($room, 'payment_closed');
            return response()->json([
                'success'  => true,
                'closed'   => true,
                'paid_now' => $paidNow,
                'total_paid' => $totalPaid,
                'due'      => 0,
                'room'     => $this->roomArray($room->fresh()),
            ]);
        } else {
            $order->update($orderData);

            $this->broadcastAdisyon($room, 'payment_partial');
            return response()->json([
                'success'    => true,
                'closed'     => false,
                'paid_now'   => $paidNow,
                'total_paid' => $totalPaid,
                'due'        => $due,
                'room'       => $this->roomArray($room->fresh()),
                'order'      => $this->orderArray($order->fresh()),
            ]);
        }
    }
    // ─── Fire draft items to kitchen (AJAX + WebSocket broadcast) ──────────
    public function fireTokitchen(Room $room)
    {
        $this->authorizeRoom($room);
        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if (!$order) {
            return response()->json(['error' => 'Aktif sipariş yok'], 404);
        }
        $count = OrderItem::where('pos_order_id', $order->id)
            ->where('kitchen_status', 'draft')
            ->update(['kitchen_status' => 'pending']);

        // Masa kapalıysa aç (mutfak ekranı sadece açık masaları gösterir)
        if ($room->status !== 'open') {
            $room->update(['status' => 'open', 'opened_at' => now()]);
        }

        // WebSocket: mutfak ekranını anlık güncelle (Reverb çalışmıyorsa sessizce atla)
        if ($count > 0) {
            try {
                $mutfak = app(MutfakController::class);
                broadcast(new KitchenUpdated(
                    auth()->user()->effectiveOwnerId(),
                    $mutfak->getOrdersPublic()
                ))->toOthers();
            } catch (\Throwable $e) {
                // Reverb sunucusu kapalıysa ana işlemi engelleme
            }
        }

        return response()->json(['success' => true, 'fired' => $count, ...$this->masaData($room)->original]);
    }

    // ─── Transfer order to another table (AJAX) ─────────────────────────────
    public function transferMasa(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $request->validate(['target_room_id' => 'required|exists:rooms,id']);
        $targetRoomId = (int) $request->target_room_id;
        if ($targetRoomId === $room->id) {
            return response()->json(['error' => 'Aynı masa seçili'], 400);
        }
        $sourceOrder = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if (!$sourceOrder) {
            return response()->json(['error' => 'Aktif sipariş bulunamadı'], 404);
        }
        $targetRoom = Room::where('id', $targetRoomId)->where('user_id', auth()->user()->effectiveOwnerId())->firstOrFail();
        $targetOrder = $this->getOrCreateOrder($targetRoom);
        // Move all items
        OrderItem::where('pos_order_id', $sourceOrder->id)->update(['pos_order_id' => $targetOrder->id]);
        $this->recalcOrder($targetOrder);
        // Close source order + room
        $sourceOrder->update(['status' => 'closed', 'closed_at' => now()]);
        $room->update(['status' => 'closed']);
        return response()->json([
            'success'     => true,
            'source_room' => $this->roomArray($room->fresh()),
            'target_room' => $this->roomArray($targetRoom->fresh()),
        ]);
    }
    // ─── Create table (AJAX) ────────────────────────────────────────
    public function masaOlustur(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);
        $room = Room::create(['user_id' => auth()->user()->effectiveOwnerId(), 'name' => $request->name, 'status' => 'closed']);
        return response()->json(['success' => true, 'room' => $this->roomArray($room)]);
    }

    // ─── Delete table (AJAX) ────────────────────────────────────────
    public function masaSil(Room $room)
    {
        $this->authorizeRoom($room);
        $room->delete();
        return response()->json(['success' => true]);
    }

    // ─── Rename table (AJAX) ────────────────────────────────────────
    public function masaRename(Request $request, Room $room)
    {
        $this->authorizeRoom($room);
        $request->validate(['name' => 'required|string|max:50']);
        $room->update(['name' => $request->name]);
        return response()->json(['success' => true, 'room' => $this->roomArray($room)]);
    }

    // ─── Toggle table open/closed (AJAX) ────────────────────────────
    public function masaToggle(Room $room)
    {
        $this->authorizeRoom($room);
        if ($room->status === 'open') {
            $room->update(['status' => 'closed', 'closed_at' => now()]);
        } else {
            $room->update(['status' => 'open', 'opened_at' => now()]);
        }
        $this->broadcastAdisyon($room, 'room_toggled');
        return response()->json(['success' => true, 'room' => $this->roomArray($room)]);
    }

    // ─── Ready items check (AJAX polling) ───────────────────────────
    public function readyCheck(Room $room)
    {
        $this->authorizeRoom($room);
        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if (!$order) {
            return response()->json(['ready_items' => []]);
        }
        // Only return 'ready' items — 'notified' ones are excluded
        $items = OrderItem::where('pos_order_id', $order->id)
            ->where('kitchen_status', 'ready')
            ->get()
            ->map(fn($i) => $this->itemArray($i))
            ->values();
        return response()->json([
            'ready_items' => $items,
            'room_name'   => $room->name,
        ]);
    }

    // ─── Bulk ready check for ALL rooms (single request instead of N) ──
    public function readyCheckAll()
    {
        $ownerId = auth()->user()->effectiveOwnerId();
        $rooms   = Room::where('user_id', $ownerId)->get();
        $roomIds = $rooms->pluck('id');

        // Get all open orders for these rooms in one query
        $openOrders = PosOrder::whereIn('room_id', $roomIds)
            ->where('status', 'open')
            ->get()
            ->keyBy('room_id');

        // Get all ready items for these orders in one query
        $orderIds = $openOrders->pluck('id');
        $readyItems = $orderIds->isNotEmpty()
            ? OrderItem::whereIn('pos_order_id', $orderIds)
                ->where('kitchen_status', 'ready')
                ->get()
                ->groupBy('pos_order_id')
            : collect();

        $result = [];
        foreach ($rooms as $room) {
            $order = $openOrders->get($room->id);
            $items = $order ? ($readyItems->get($order->id) ?? collect()) : collect();
            $result[] = [
                'room_id'     => $room->id,
                'room_name'   => $room->name,
                'ready_items' => $items->map(fn($i) => $this->itemArray($i))->values(),
            ];
        }

        return response()->json(['rooms' => $result]);
    }

    // ─── Mark notified (Tamam clicked) ───────────────────────────────
    public function markNotified(Request $request, Room $room)
    {        $this->authorizeRoom($room);        $ids = $request->input('item_ids', []);
        if (!empty($ids)) {
            OrderItem::whereIn('id', $ids)
                ->where('kitchen_status', 'ready')
                ->update(['kitchen_status' => 'notified']);
        }
        return response()->json(['success' => true]);
    }

    // ─── Günlük satış raporu ───────────────────────────────────────────
    public function rapor(Request $request)
    {
        $date  = $request->query('date', now()->format('Y-m-d'));
        $start = \Carbon\Carbon::parse($date)->startOfDay();
        $end   = \Carbon\Carbon::parse($date)->endOfDay();

        $myRoomIds = Room::where('user_id', auth()->user()->effectiveOwnerId())->pluck('id');
        $orders = PosOrder::where('status', 'closed')
            ->whereIn('room_id', $myRoomIds)
            ->whereBetween('closed_at', [$start, $end])
            ->get();

        $total = (float) $orders->sum('total');
        $count = $orders->count();

        $items = [];
        $masalar = [];
        if ($count > 0) {
            $items = OrderItem::whereIn('pos_order_id', $orders->pluck('id'))
                ->selectRaw('name, category, SUM(quantity) as qty, SUM(total) as total')
                ->groupBy('name', 'category')
                ->orderByDesc('total')
                ->get();

            // N+1 sorgusu önleme: tüm room'ları tek seferde çek
            $roomMap = Room::whereIn('id', $orders->pluck('room_id'))->pluck('name', 'id');
            foreach ($orders as $o) {
                $masalar[] = [
                    'id'           => $o->id,
                    'room_name'    => $roomMap[$o->room_id] ?? 'Silinmiş Masa',
                    'total'        => (float) $o->total,
                    'payment_type' => $o->payment_type ?? 'Nakit',
                    'closed_at'    => $o->closed_at?->format('H:i'),
                ];
            }
        }

        return response()->json([
            'date'        => $start->format('d.m.Y'),
            'order_count' => $count,
            'total'       => $total,
            'items'       => $items,
            'masalar'     => $masalar,
        ]);
    }

    // ─── Ödeme geçmişi ──────────────────────────────────────────────
    public function odemeGecmisi(Request $request)
    {
        $date  = $request->query('date', now()->format('Y-m-d'));
        $start = \Carbon\Carbon::parse($date)->startOfDay();
        $end   = \Carbon\Carbon::parse($date)->endOfDay();

        $myRoomIds = Room::where('user_id', auth()->user()->effectiveOwnerId())->pluck('id');
        $orders = PosOrder::where('status', 'closed')
            ->whereIn('room_id', $myRoomIds)
            ->whereBetween('closed_at', [$start, $end])
            ->orderByDesc('closed_at')
            ->get();

        // N+1 sorgusu önleme: tüm room'ları tek seferde çek
        $roomMap = Room::whereIn('id', $orders->pluck('room_id')->unique())->pluck('name', 'id');
        $orders = $orders->map(function ($o) use ($roomMap) {
                return [
                    'id'           => $o->id,
                    'room_name'    => $roomMap[$o->room_id] ?? 'Silinmiş Masa',
                    'total'        => (float) $o->total,
                    'payment_type' => $o->payment_type ?? 'Nakit',
                    'closed_at'    => $o->closed_at?->format('H:i'),
                ];
            });

        return response()->json([
            'orders' => $orders,
            'date'   => $start->format('d.m.Y'),
            'total'  => (float) $orders->sum('total'),
        ]);
    }

    // ─── Save note (AJAX) ───────────────────────────────────────────
    public function saveNote(Request $request, Room $room)
    {        $this->authorizeRoom($room);        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if ($order) {
            $order->update(['note' => $request->note ?? '']);
        }
        $room->update(['note' => $request->note ?? '']);
        return response()->json(['success' => true]);
    }

    // ─── Sipariş Sil ────────────────────────────────────────────────
    public function deleteOrder(PosOrder $order)
    {        // Güvenlik: sadece kendi masalarına ait siparişler silinebilir
        $room = Room::find($order->room_id);
        if (!$room || $room->user_id !== auth()->user()->effectiveOwnerId()) {
            abort(403);
        }        OrderItem::where('pos_order_id', $order->id)->delete();
        Payment::where('pos_order_id', $order->id)->delete();
        $order->delete();
        return response()->json(['success' => true]);
    }

    // ─── Helpers ────────────────────────────────────────────────────
    private function broadcastAdisyon(Room $room, string $action, array $extraPayload = []): void
    {
        try {
            broadcast(new AdisyonUpdated(
                $room->user_id,
                $room->id,
                $action,
                $extraPayload
            ))->toOthers();
        } catch (\Throwable $e) {
            // Reverb sunucusu kapalıysa ana işlemi engelleme
        }
    }

    private function authorizeRoom(Room $room): void
    {
        if ($room->user_id !== auth()->user()->effectiveOwnerId()) {
            abort(403);
        }
    }

    private function getOrCreateOrder(Room $room): PosOrder
    {
        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if (!$order) {
            $order = PosOrder::create([
                'room_id'   => $room->id,
                'status'    => 'open',
                'opened_at' => now(),
                'subtotal'  => 0,
                'total'     => 0,
                'paid'      => 0,
                'due'       => 0,
            ]);
        }
        // Masa kapalıysa otomatik aç
        if ($room->status !== 'open') {
            $room->update(['status' => 'open', 'opened_at' => now()]);
        }
        return $order;
    }

    private function recalcOrder(PosOrder $order): void
    {
        $subtotal = (float) OrderItem::where('pos_order_id', $order->id)->sum('total');
        $order->update(['subtotal' => $subtotal, 'total' => $subtotal]);
    }

    private function roomArray(Room $room, ?PosOrder $cachedOrder = null): array
    {
        $order = $cachedOrder ?? PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if ($order) {
            // Single query instead of 3 separate ones
            $stats = OrderItem::where('pos_order_id', $order->id)
                ->selectRaw("COUNT(*) as item_count, COALESCE(SUM(total),0) as total_sum, SUM(CASE WHEN kitchen_status = 'ready' THEN 1 ELSE 0 END) as ready_count")
                ->first();
            $itemCount = (int) $stats->item_count;
            $total     = (float) $stats->total_sum;
            $hasReady  = $stats->ready_count > 0;
        } else {
            $itemCount = 0;
            $total     = 0;
            $hasReady  = false;
        }
        return [
            'id'         => $room->id,
            'name'       => $room->name,
            'status'     => $room->status,
            'note'       => $room->note ?? '',
            'opened_at'  => $room->opened_at?->format('d.m.Y H:i:s'),
            'item_count' => $itemCount,
            'total'      => $total,
            'has_ready'  => $hasReady,
            'order_id'   => $order?->id,
        ];
    }

    private function orderArray(PosOrder $order): array
    {
        return [
            'id'           => $order->id,
            'status'       => $order->status,
            'subtotal'     => (float) $order->subtotal,
            'service'      => (float) $order->service,
            'discount'     => (float) $order->discount,
            'vat'          => (float) $order->vat,
            'total'        => (float) $order->total,
            'paid'         => (float) $order->paid,
            'due'          => (float) $order->due,
            'payment_type' => $order->payment_type ?? 'Nakit',
            'note'         => $order->note ?? '',
            'opened_at'    => $order->opened_at?->format('d.m.Y H:i:s'),
        ];
    }

    private function itemArray(OrderItem $item): array
    {
        return [
            'id'             => $item->id,
            'product_id'     => $item->product_id,
            'name'           => $item->name,
            'category'       => $item->category,
            'price'          => (float) $item->price,
            'quantity'       => (float) $item->quantity,
            'total'          => (float) $item->total,
            'kitchen_status' => ($item->kitchen_status === null || $item->kitchen_status === '' ? 'draft' : $item->kitchen_status),
            'note'           => $item->note ?? '',
        ];
    }

    // ─── Update item note (AJAX) ────────────────────────────────────
    public function updateItemNote(Request $request, Room $room)
    {        $this->authorizeRoom($room);        $request->validate([
            'item_id' => 'required|exists:order_items,id',
            'note'    => 'nullable|string|max:200',
        ]);
        $order = PosOrder::where('room_id', $room->id)->where('status', 'open')->first();
        if ($order) {
            OrderItem::where('id', $request->item_id)
                ->where('pos_order_id', $order->id)
                ->update(['note' => $request->note ?: null]);
        }
        return $this->masaData($room);
    }
}