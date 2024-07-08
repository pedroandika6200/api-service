<?php

namespace App\Http\Resources;

class SalesOrderItemResource extends Resource
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
        ];
    }
}
