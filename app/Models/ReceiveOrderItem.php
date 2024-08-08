<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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

    public function mounts ()
    {
        return $this->hasMany(ReceiveOrderMount::class);
    }

    public function receive_order ()
    {
        return $this->belongsTo(ReceiveOrder::class);
    }

    public function product ()
    {
        return $this->belongsTo(Product::class);
    }

    public function locker()
    {
        return $this->hasOne(Locker::class, 'receive_order_id', 'receive_order_id')
                    ->where('product_id', $this->product_id);
    }

    public function getPrepareMounting () : Collection
    {
        $rows = collect();
        $lockers = \App\Models\Locker::whereMounting($this->product_id, $this->receive_order_id)->get();

        if ($lockers->count() <= 0) {
            return $rows;
        }

        $total = $this->amount;
        $lockerNames = collect();

        foreach ($lockers as $n => $locker) {
            if ($total <= 0 || $locker->available === 0) break;
            $available = $locker->available ?: $locker->getCapacity($this->product);
            $amount = $total > $available ? $available : $total;

            $rows->push([
                "locker_id" => $locker->id,
                "amount" => $amount,
            ]);

            $total = $total - $amount;
            $lockerNames->push("($locker->code => $amount)");
        }

        return $rows;
    }
}
