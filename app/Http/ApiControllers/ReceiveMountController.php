<?php

namespace App\Http\ApiControllers;

use App\Http\Filters\ReceiveMountFilter;
use App\Models\ReceiveMount;
use App\Http\Resources\ReceiveMountResource;
use Illuminate\Http\Request;

class ReceiveMountController extends Controller
{
    public function index (ReceiveMountFilter $filter)
    {
        $collection = ReceiveMount::filter($filter)->collective();

        return ReceiveMountResource::collection($collection);
    }

    public function show ($id)
    {
        $record = ReceiveMount::findOrFail($id);

        return new ReceiveMountResource($record);
    }
}
