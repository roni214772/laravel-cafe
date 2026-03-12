<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdisyonUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $userId,
        public readonly int    $roomId,
        public readonly string $action,
        public readonly array  $payload = []
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('adisyon.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'updated';
    }

    public function broadcastWith(): array
    {
        return [
            'room_id' => $this->roomId,
            'action'  => $this->action,
            'payload' => $this->payload,
        ];
    }
}
