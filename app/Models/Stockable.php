<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Recordset\Concerns\HasFilterable;

class Stockable extends Model
{
    public $timestamps = false;

    protected $fillable = ["type", "amount"];

    public function product ()
    {
        return $this->belongsTo(Product::class);
    }
}
