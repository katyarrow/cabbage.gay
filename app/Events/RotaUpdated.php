<?php

namespace App\Events;

use App\Http\Resources\RotaResourceSimple;
use App\Models\Rota;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RotaUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Rota $rota)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('rota.' . $this->rota->identifier . '.updated'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'rota.updated';
    }

    public function broadcastWith(): array
    {
        return (new RotaResourceSimple($this->rota))->toArray(request());
    }
}
