<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\ReceiveOrderItemFilter;
use App\Models\ReceiveOrderItem;
use App\Http\Resources\ReceiveOrderItemResource;
use App\Jobs\ReceiveMounted;
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
        $request->validate([
            "pallet" => "required", // |unique:receive_order_items,pallet,null,null,mounted_uid,null",
            "receive_order_id" => "required|exists:receive_orders,id",
            "product_id" => "required|exists:products,id",
            "amount" => "required",
        ]);

        $row = $request->only([
            "receive_order_id", "pallet", "product_id", "amount",
        ]);

        app('db')->beginTransaction();

        /** @var ReceiveOrderItem $record*/
        $record = new ReceiveOrderItem($row);

        $record->save();

        app('db')->commit();

        $message = "The record has been created.";

        return (new ReceiveOrderItemResource($record))->additional([
            "message" => $message,
        ]);
    }

    /** @var \App\Models\ReceiveOrderItem $record */
    public function storeMounts (ReceiveOrderItem $record, Request $request)
    {
        $request->validate([
            "*.locker_id" => "required|exists:lockers,id",
            "*.amount" => "required",
        ]);

        app('db')->beginTransaction();

        $rows = collect($request->all())->select(['locker_id', 'amount']);

        $record->setAttribute('mounts', $rows->all());
        $record->save();

        ReceiveMounted::dispatchSync($record);

        app('db')->commit();

        $message = "The record has been mounted.";

        return (new ReceiveOrderItemResource($record))->additional([
            "message" => $message,
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
