<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPartial extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['part_id', 'count'];

    protected $casts = [ 'count' => 'double'];

    public function part()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }
}
