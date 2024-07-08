<?php

namespace App\Models;

use Recordset\Concerns\HasFilterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactable extends Model
{
    use HasFilterable;

    protected $hidden = ['model_type', 'model_id', 'created_at', 'updated_at'];

    protected $fillable = ["name", "email", "phone", "mobile", "street", "city", "zipcode", "option"];

    protected $casts = [
        'option' => 'array',
    ];

    public function model()
    {
        return $this->morphTo();
    }

    public function getAddressAttribute()
    {
        if (!boolval($this->street || $this->city)) return null;

        $address = collect([$this->street, $this->city])
            ->filter(function ($str) {
                return strlen($str);
            })
            ->join("\n");

        if ($this->city) $address .= " - " . $this->zipcode;
        return $address;
    }

    public function scopeWithCategory($query, $category)
    {
        $this->saving(function($model) use ($category) {
            $model->category = $category;
        });

        return $query->category($category);
    }

    public function scopeCategory($query, $category)
    {
        return $query
            // ->withoutGlobalScope('withoutCategory')
            ->where('category', $category);
    }

    public static function booted()
    {
        // static::addGlobalScope('withoutCategory', function ($builder) {
        //     $builder->whereNull('category');
        // });
    }
}
