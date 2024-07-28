<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ReceiveOrderItem;
use Illuminate\Http\Request;

class ReceiveMounting extends Job
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->generateKey($request);
    }

    public function handle()
    {
        $record = ReceiveOrderItem::findOrFail($this->request->get('id'));

        app('db')->beginTransaction();

        $rows = collect($this->request->get('mounts'))->select(['locker_id', 'amount']);

        $record->setAttribute('mounts', $rows->all());
        $record->save();

        $rows = collect($record->mounts ?? []);
        if ($rows->count())
        {
        $rows->each(function($e) use ($record) {
            \App\Models\Locker::find($e['locker_id'])->mounting($record->product, $e['amount'], $record->receive_order_id);
        });

        }
        else abort(406, "The record has not been mounted.");

        $record->product->instock($record->amount);

        \App\Events\RecordSaved::dispatchUnconsole($this->qid, $record); //->withDelay(now()->addSeconds(10));

        app('db')->commit();

        return $record;
    }

}
