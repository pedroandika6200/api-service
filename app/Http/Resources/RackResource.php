<?php

namespace App\Http\Resources;

class RackResource extends Resource
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
            $this->mergeInclude('lockers', function () {
                return LockerResource::collection($this->resource->rack);
            }),
        ];
    }
}
