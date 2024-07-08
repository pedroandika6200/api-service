<?php

namespace App\Models;

use Recordset\Concerns\HasFilterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory, HasFilterable;

    public $timestamps = false;

    protected $fillable = ['name'];
}
