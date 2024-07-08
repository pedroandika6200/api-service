<?php

namespace App\Http\Resources;

class ProductConvertableResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // dd($this->resource);
        return [
            // $this->mergeAttributes(),
            $this->mergeInclude('product', function () {
                return new ProductResource($this->resource['product']);
            }),
            $this->mergeField('rate', fn() => $this->resource['rate']),
            $this->mergeField('point_id', fn() => $this->resource['point_id'] ?? null),
            $this->mergeField('base_id', fn() => $this->resource['base_id'] ?? null),

        ];
    }
}
