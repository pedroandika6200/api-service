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
    private Request $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->generateKey($request);
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

        \App\Events\RecordSaved::dispatchUnconsole($this->qid, $record); //->withDelay(now()->addSeconds(10));

        app('db')->commit();
    }
}
