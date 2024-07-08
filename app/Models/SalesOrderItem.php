<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Recordset\Concerns\HasFilterable;
use Recordset\Concerns\HasOptionProperty;

class SalesOrderItem extends Model
{
    use HasFilterable, HasOptionProperty;

    protected $casts = [
        'quantity' => 'double',
        'price' => 'double',
        'discprice' => 'double',
        'option' => 'array',
        'option.discprice_sen' => 'double',
        'option.taxsen_income' => 'double',
        'option.taxsen_service' => 'double',
    ];

    protected $fillable = ['name', 'quantity', 'price', 'discprice', 'notes'];

    public function product ()
    {
        return $this->belongsTo(Product::class);
    }
}
