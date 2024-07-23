<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ReceiveOrderItem;

class ReceiveMounted extends Job
{
    private ReceiveOrderItem $record;

    public function __construct(ReceiveOrderItem $record)
    {
        $this->record = $record;
    }

    public function handle()
    {
        app('db')->beginTransaction();

        $rows = collect($this->record->mounts ?? []);
        if ($rows->count())
        {
            $rows = $rows->map(fn($e) => [...$e, 'product_id' => $this->record->product_id]);
            $this->record->lockerables()->createMany($rows->all());

        }
        else abort(406, "The record has not been mounted.");

        $this->record->product->instock($this->record->amount);

        app('db')->commit();
    }

}
