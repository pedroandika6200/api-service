<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\LockerFilter;
use App\Models\Locker;
use App\Http\Resources\LockerResource;
use Illuminate\Http\Request;

class LockerController extends Controller
{
    public function index (LockerFilter $filter)
    {
        $collection = Locker::filter($filter)->collective();

        return LockerResource::collection($collection);
    }

    public function show ($id)
    {
        $record = Locker::findOrFail($id);

        return new LockerResource($record);
    }

    public function save (Request $request)
    {

        $request->validate([
            "rack_id" => "sometimes|required_if:id,null|exists:racks,id",
            "code" => "sometimes|required_if:id,null|string|unique:racks,id,$request->id,id",
            "position" => "nullable",
            "dimension" => "required|array",
            "wmax" => "required|numeric",
        ]);

        $row = $request->only([
            "code", "position", "dimension", "wmax",
        ]);

        app('db')->beginTransaction();

        /** @var \App\Models\Rack $rack */
        $rack = \App\Models\Rack::find($request->rack_id);

        /** @var Locker $record */
        $record = $rack->lockers()->firstOrNew(['id' => intval($request->id)]);

        $record->fill($row);

        $record->save();

        $record->save();

        app('db')->commit();

        $message = "The record has been saved.";

        return (new LockerResource($record))->additional([
            "message" => $message,
        ]);
    }

    public function delete ($id)
    {
        $record = Locker::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record has been deleted."
        ]);
    }
}
