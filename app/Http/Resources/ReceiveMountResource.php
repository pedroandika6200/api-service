<?php

namespace App\Http\Resources;

class ReceiveMountResource extends Resource
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
            $this->mergeInclude('receive_order_item', function () {
                return new ReceiveOrderItemResource($this->resource->receive_order_item);
            }),
            $this->mergeInclude('receive_order', function () {
                return new ReceiveOrderResource($this->resource->receive_order);
            }),
        ];
    }
}
