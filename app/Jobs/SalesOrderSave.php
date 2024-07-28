<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;

class SalesOrderSave extends Job
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->generateKey($request);
    }

    public function handle()
    {
        $fillable = ['number', 'date', 'due', 'reference', 'description'];

        if (!$this->request->get('id')) $fillable = array_merge($fillable, ['customer_id']);

        $row = $this->request->only($fillable);

        app('db')->beginTransaction();

        /** @var SalesOrder $record*/
        $record = SalesOrder::firstOrNew(['id' => $this->request->get('id')], $row);

        $record->fill($row);


        $requestOption = new Request($this->request->get('option'));
        $record->setOptions(
            $requestOption->only(['taxable', 'tax_inclusive', 'shipcust', 'paycust' ])
        );

        $record->save();

        if ($this->request->has('items'))
        {
            $deleteItems = collect($this->request->get('items'))->whereNotNull('id')->pluck('id');

            if ($deleteItems->count()) $record->items()->whereNotIn('id', $deleteItems->toArray())->delete();

            foreach ($this->request->get('items') as $requestItem) {

                $requestItem = new Request($requestItem);

                $fillItem = ['name', 'quantity', 'price', 'discprice', 'notes', 'seq', 'group_seq'];

                if (!$requestItem->get('id')) $fillItem = array_merge($fillItem, ['product_id', 'unit']);

                $rowItem = $requestItem->only($fillItem);

                /** @var SalesOrderItem $recordItem */
                $recordItem = $record->items()->firstOrNew(['id' => $requestItem->get('id')], $rowItem);

                $recordItem->fill($rowItem);

                $requestItemOption = new Request($requestItem->get('option'));
                $recordItem->setOptions(
                    $requestItemOption->only(['discprice_sen', 'taxsen_income', 'taxsen_service'])
                );

                $recordItem->save();
            }

            $record->subtotal = $record->items()->sum(app('db')->raw("(`quantity` * `price`) - `discprice`"));

            $record->save();

        }

        app('db')->commit();

        $record->setNumber();

        \App\Events\RecordSaved::dispatchUnless(app()->runningInConsole(), $this->qid, $record); //->withDelay(now()->addSeconds(10));

        return $record;
    }

}
