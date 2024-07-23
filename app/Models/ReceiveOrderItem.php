<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Recordset\Concerns\HasFilterable;

class ReceiveOrderItem extends Model
{
    use HasFilterable;

    protected $fillable = ["pallet", "product_id", "amount"];

    protected $casts = [
        "amount" => "integer",
        "mounts" => "array",
    ];

    public function lockerables ()
    {
        return $this->morphMany(Lockerable::class, 'model');
    }

    public function receive_order ()
    {
        return $this->belongsTo(ReceiveOrder::class);
    }

    public function product ()
    {
        return $this->belongsTo(Product::class);
    }
}
