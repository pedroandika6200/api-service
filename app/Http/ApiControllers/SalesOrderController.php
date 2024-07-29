<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\SalesOrderFilter;
use App\Models\SalesOrder;
use App\Http\Resources\SalesOrderResource;
use Illuminate\Http\Request;

class SalesOrderController extends Controller
{
    public function index (SalesOrderFilter $filter)
    {
        $collection = SalesOrder::filter($filter)->latest()->collective();

        return SalesOrderResource::collection($collection);
    }

    public function show ($id)
    {
        $record = SalesOrder::findOrFail($id);

        return new SalesOrderResource($record);
    }

    public function save (Request $request)
    {
        if (!$request->has('qid')) $request->merge(['qid' => str()->uuid()]);

        $request->validate([
            "id" => "nullable|exists:sales_orders,id",
            "number" => "sometimes|unique:sales_orders,number,". $request->get('id', null) .",id",
            "date" => "required_if:id,null|date",
            "due" => "sometimes|nullable|date",
            "reference" => "sometimes|nullable|string",
            "description" => "sometimes|nullable|string",
            "items" => "sometimes|array",
        ]);


        \App\Jobs\SalesOrderSave::dispatchOrSync($request->all());

        return response()->json([
            "qid" => $request->get('qid'),
            "message" => "The sales-order request on queue processing."
        ]);
    }

    public function orderApproved ($id)
    {
        /** @var \App\Models\SalesOrder $record */
        $record = SalesOrder::findOrFail($id);

        if ($record->state != "OPEN") abort(416, "The Record state hasnot 'OPEN'");

        $record->setAttribute('state', 'APPROVED');
        $record->save();

        return new SalesOrderResource($record);
    }

    public function delete ($id)
    {
        $record = SalesOrder::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record hass been deleted."
        ]);
    }
}
