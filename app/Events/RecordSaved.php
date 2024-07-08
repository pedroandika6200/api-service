<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class RecordSaved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $model;
    public $key;

    public function __construct($key, $model)
    {
        $this->key = $key;
        $this->model = $model;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel("app-channel")
        ];
    }

    public function broadcastAs()
    {
        return "action";
    }

    public function broadcastWith(): array
    {

        return [
            "key" => $this->key,
            "message" => "The record [". $this->model->name ."] saved.",
            "data" => $this->model->toArray(),
        ];
    }
}
