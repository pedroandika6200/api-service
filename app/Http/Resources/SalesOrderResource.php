<?php

namespace App\Http\Resources;

class SalesOrderResource extends Resource
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
                return SalesOrderItemResource::collection($this->resource->items);
            }),
            $this->mergeInclude('customer', function () {
                return new CustomerResource($this->resource->customer);
            }),
        ];
    }
}
