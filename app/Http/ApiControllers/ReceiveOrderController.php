<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\ReceiveOrderFilter;
use App\Models\ReceiveOrder;
use App\Http\Resources\ReceiveOrderResource;
use Illuminate\Http\Request;

class ReceiveOrderController extends Controller
{
    public function index (ReceiveOrderFilter $filter)
    {
        $collection = ReceiveOrder::filter($filter)->collective();

        return ReceiveOrderResource::collection($collection);
    }

    public function show ($id)
    {
        $record = ReceiveOrder::findOrFail($id);

        return new ReceiveOrderResource($record);
    }

    public function save (Request $request)
    {
        $request->validate([
            "id" => "nullable|exists:receive_orders,id",
            "number" => "required_if:id,null|string|unique:receive_orders,id,$request->id,id",
            "date" => "required|date",
            "reference" => "required",
        ]);

        $row = $request->only([
            "number", "date", "reference",
        ]);

        app('db')->beginTransaction();

        /** @var ReceiveOrder $record*/
        $record = ReceiveOrder::firstOrNew(['id' => $request->id]);

        $record->fill($row);

        $record->save();

        app('db')->commit();

        $message = "The record has been saved.";

        return (new ReceiveOrderResource($record))->additional([
            "message" => $message,
        ]);
    }

    public function delete ($id)
    {
        $record = ReceiveOrder::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record has been deleted."
        ]);
    }
}
