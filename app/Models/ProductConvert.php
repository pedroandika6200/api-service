<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductConvert extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $casts = [
        'rate' => 'double',
    ];

    public function base()
    {
        return $this->belongsTo(Product::class);
    }

    public function point()
    {
        return $this->belongsTo(Product::class);
    }
}
