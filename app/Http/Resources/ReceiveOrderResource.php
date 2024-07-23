<?php

namespace App\Http\Resources;

class ReceiveOrderResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            $this->mergeAttributes(),
            $this->mergeInclude('items', function () {
                return ReceiveOrderItemResource::collection($this->resource->items);
            }),
            $this->mergeInclude('mounts', function () {
                return ReceiveMountResource::collection($this->resource->mounts);
            }),
        ];
    }
}
