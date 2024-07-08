<?php

namespace App\Http\Resources;

class ProductResource extends Resource
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
            $this->mergeInclude('convertable', function () {
                return ProductConvertableResource::collection($this->resource->convertable);
            }),
            $this->mergeInclude('partials', function () {
                return ProductPartialResource::collection($this->resource->partials);
            }),
            $this->mergeInclude('category', function () {
                return new Resource($this->resource->category);
            }),
        ];
    }
}
