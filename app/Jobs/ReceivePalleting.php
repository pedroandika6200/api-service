<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ReceiveOrderItem;
use Illuminate\Http\Request;

class ReceivePalleting extends Job
{
    public function __construct($request)
    {
        $this->setQueueRequest($request);
    }

    public function handle()
    {
        $row = $this->request->only([
            "receive_order_id", "pallet", "product_id", "amount",
        ]);

        app('db')->beginTransaction();

        /** @var ReceiveOrderItem $record*/
        $record = new ReceiveOrderItem($row);

        $record->save();

        $mounts = $record->getPrepareMounting();
        if ($mounts->sum('amount') == $record->amount) {
            \App\Jobs\ReceiveMounting::dispatchSync([
                "id" => $record->id,
                "mounts" => $mounts->toArray(),
            ]);
        }

        \App\Events\RecordSaved::dispatchUnconsole($this->qid, $record);

        app('db')->commit();
    }
}
