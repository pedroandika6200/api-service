<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Recordset\Concerns\HasFilterable;

class ReceiveOrderMount extends Model
{
    use HasFilterable;

    protected $fillable = ["locker_id", "amount"];

    protected $casts = [
        "amount" => "integer",
    ];

    public function locker ()
    {
        return $this->belongsTo(Locker::class);
    }

    public function receive_order_item ()
    {
        return $this->belongsTo(ReceiveOrderItem::class);
    }

    public function setMounted()
    {
        $this->setAttribute('mounted_at', app('db')->raw('CURRENT_TIMESTAMP'));
        return $this->save();
    }
}
