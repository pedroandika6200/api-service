<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\ReceiveOrderItemFilter;
use App\Models\ReceiveOrderItem;
use App\Http\Resources\ReceiveOrderItemResource;
use Illuminate\Http\Request;

class ReceiveOrderItemController extends Controller
{
    public function index (ReceiveOrderItemFilter $filter)
    {
        $collection = ReceiveOrderItem::filter($filter)->collective();

        return ReceiveOrderItemResource::collection($collection);
    }

    public function show ($id)
    {
        $record = ReceiveOrderItem::findOrFail($id);

        return new ReceiveOrderItemResource($record);
    }

    public function store (Request $request)
    {
        if (!$request->has('qid')) $request->merge(['qid' => str()->uuid()]);

        $request->validate([
            "pallet" => "required", // |unique:receive_order_items,pallet,null,null,mounted_uid,null",
            "receive_order_id" => "required|exists:receive_orders,id",
            "product_id" => "required|exists:products,id",
            "amount" => "required",
        ]);

        \App\Jobs\ReceivePalleting::dispatchOrSync($request->all());

        return response()->json([
            "qid" => $request->get('qid'),
            "message" => "The receive mounting on queue processing."
        ]);
    }

    /** @var \App\Models\ReceiveOrderItem $record */
    public function storeMounting (Request $request)
    {
        if (!$request->has('qid')) $request->merge(['qid' => str()->uuid()]);

        $request->validate([
            "id" => "required|exists:receive_order_items,id",
            "mounts.*.locker_id" => "required|exists:lockers,id",
            "mounts.*.amount" => "required",
        ]);

        \App\Jobs\ReceiveMounting::dispatchOrSync($request->all());

        return response()->json([
            "qid" => $request->get('qid'),
            "message" => "The receive mounting on queue processing.",
        ]);
    }

    public function delete ($id)
    {
        $record = ReceiveOrderItem::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record has been deleted."
        ]);
    }
}
