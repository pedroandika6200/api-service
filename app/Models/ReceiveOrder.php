<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Recordset\Concerns\HasFilterable;

class ReceiveOrder extends Model
{
    use HasFilterable;
    protected $fillable = ["number", "date", "reference"];

    public function items ()
    {
        return $this->hasMany(ReceiveOrderItem::class);
    }
}
