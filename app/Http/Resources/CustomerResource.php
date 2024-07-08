<?php

namespace App\Http\Resources;

class CustomerResource extends Resource
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
            $this->mergeInclude('contact', function () {
                return new ContactResource($this->resource->contact);
            }),
            $this->mergeInclude('payment_contact', function () {
                return new ContactResource($this->resource->payment_contact);
            }),
        ];
    }
}
