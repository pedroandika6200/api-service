<?php

namespace App\Models;

use Recordset\Concerns\HasFilterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Recordset\Concerns\HasOptionProperty;

class Product extends Model
{
    use HasFactory, HasFilterable, HasOptionProperty;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'sku', 'type', 'name', 'unit', 'sale_price', 'purchase_price', 'description', 'category_id'
    ];

    protected $casts = [
        'type' => \App\Enums\ProductType::class,
        'disabled' => 'boolean',
        'published' => 'boolean',
        'sale_price' => 'double',
        'purchase_price' => 'double',
        'option.tax_income' => 'double',
        'option.tax_service' => 'double',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function converts($revese = false)
    {
        if ($revese)
        return $this->belongsToMany(static::class, 'product_converts', 'point_id', 'base_id');
        return $this->belongsToMany(static::class, 'product_converts', 'base_id', 'point_id');
    }

    public function partials()
    {
        return $this->hasMany(ProductPartial::class);
    }

    public function getConvertableAttribute()
    {
        return ProductConvert::where('base_id', $this->getKey())->orWhere('point_id', $this->getKey())->get()
            ->map(fn($e) => $e->base_id == $this->getKey()
                ? ['product' => $e->point, 'point_id' => $e->point_id, 'rate' => $e->rate]
                : ['product' => $e->base, 'base_id' => $e->base_id, 'rate' => $e->rate]
        );
    }

    public function scopeSearchKey(Builder $query, $skuOrId)
    {
        return $query->where('id', $skuOrId)->orWhere('sku', $skuOrId)->limit(1);
    }
}
