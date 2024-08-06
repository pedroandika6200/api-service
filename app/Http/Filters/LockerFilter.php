<?php

namespace App\Http\Filters;

class LockerFilter extends Filter
{
    public function mountableProductId($id)
    {
        $receiveID = $this->request->get('mountableReceiveOrderId')
            ?? $this->request->get('mountable_receive_order_id') ?? null;
        return $this->whereMounting($id, $receiveID);
    }
}
