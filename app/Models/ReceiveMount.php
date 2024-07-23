<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiveMount extends Model
{
    protected $fillable = ["locker_id", "product_id", "amount", "unit"];

    public function receive_order_item ()
    {
        return $this->belongsTo(ReceiveOrderItem::class);
    }

    public function receive_order ()
    {
        return $this->newBelongsTo(
            ReceiveOrder::query(), $this->receive_order_item, 'receive_order_id', 'id', null
        );
    }

    public function product ()
    {
        return $this->belongsTo(Product::class);
    }
}
