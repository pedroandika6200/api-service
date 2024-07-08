<?php

namespace App\Http\Resources;

class ProductPartialResource extends Resource
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
            $this->mergeInclude('part', function () {
                return new ProductResource($this->resource->part);
            }),
        ];
    }
}
