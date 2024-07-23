<?php

namespace App\Http\Resources;

class LockerResource extends Resource
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
            $this->mergeInclude('rack', function () {
                return new RackResource($this->resource->rack);
            }),
        ];
    }
}
