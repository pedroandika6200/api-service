<?php

namespace App\Http\Resources;

class ReceiveOrderItemResource extends Resource
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
            $this->mergeInclude('product', function () {
                return new ProductResource($this->resource->product);
            }),
            $this->mergeInclude('receive_order', function () {
                return new ReceiveOrderResource($this->resource->receive_order);
            }),
        ];
    }
}
