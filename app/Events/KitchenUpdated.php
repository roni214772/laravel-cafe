<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KitchenUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int   $userId,
        public readonly array $orders
    ) {}

    /**
     * Mutfak ekranı private channel'da dinler: kitchen.{userId}
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('kitchen.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'updated';
    }

    public function broadcastWith(): array
    {
        return ['orders' => $this->orders];
    }
}
