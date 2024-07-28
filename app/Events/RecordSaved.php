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
        $labelRecord =  "[". ($this->model->labelRecord ?? $this->model->number ?? $this->model->name ?? $this->model->id) ."]";
        return [
            "key" => $this->key,
            "message" => "The record $labelRecord saved.",
            "data" => $this->model?->toArray(),
        ];
    }
    public static function dispatchUnconsole(...$arguments)
    {
        if (! app()->runningInConsole()) {
            return event(new static(...$arguments));
        }
    }

}
