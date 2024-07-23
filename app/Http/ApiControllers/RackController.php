<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\RackFilter;
use App\Models\Rack;
use App\Http\Resources\RackResource;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function index (RackFilter $filter)
    {
        $collection = Rack::filter($filter)->collective();

        return RackResource::collection($collection);
    }

    public function show ($id)
    {
        $record = Rack::findOrFail($id);

        return new RackResource($record);
    }

    public function save (Request $request)
    {

        $request->validate([
            "code" => "sometimes|required_if:id,null|string|unique:racks,id,$request->id,id",
            "position" => "nullable",
        ]);

        $row = $request->only([
            "code", "position",
        ]);

        app('db')->beginTransaction();

        /** @var Rack $record*/
        $record = Rack::firstOrNew(['id' => intval($request->id)]);

        $record->fill($row);

        $record->save();

        app('db')->commit();

        $message = "The record has been saved.";

        return (new RackResource($record))->additional([
            "message" => $message,
        ]);
    }

    public function delete ($id)
    {
        $record = Rack::findOrFail($id);

        $record->delete();

        return response()->json([
            "message" => "The record has been deleted."
        ]);
    }
}
