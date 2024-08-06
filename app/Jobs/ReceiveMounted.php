<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ReceiveOrderItem;
use Illuminate\Http\Request;

class ReceiveMounted extends Job
{

    public function __construct($request)
    {
        $this->setQueueRequest($request);
    }

    public function handle()
    {
        $record = ReceiveOrderItem::findOrFail($this->request->get('id'));

        app('db')->beginTransaction();

        if (!$record->mounts?->count()) abort(406, "The record has not been mounted.");

        $record->mounts->each(function($e) use ($record) {
            \App\Models\Locker::find($e['locker_id'])->mounted($record, $e['amount'], $record->receive_order_id);
            $e->setMounted();
        });

        $record->product->instock($record->amount);

        \App\Events\RecordSaved::dispatchUnconsole($this->qid, $record); //->withDelay(now()->addSeconds(10));

        app('db')->commit();

        return $record;
    }

}
