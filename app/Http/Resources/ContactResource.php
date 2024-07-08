<?php

namespace App\Http\Resources;

class ContactResource extends Resource
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
            $this->mergeField('address', function () {
                return $this->resource->address;
            }),
        ];
    }
}
