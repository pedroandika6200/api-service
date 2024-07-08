<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Recordset\Concerns\HasFilterable;
use Illuminate\Database\Eloquent\Model;
use Recordset\Concerns\HasOptionProperty;

class Customer extends Model
{
    use HasFilterable, HasOptionProperty;

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = ['name', 'code'];

    protected $casts = [
        'disabled' => 'boolean',
        'option' => 'array',
        'option.tax_no' => 'string',
        'option.tax_inclusive' => 'boolean',
    ];

    public function contact()
    {
        return $this->morphOne(Contactable::class, 'model')->withCategory('contact');
    }

    public function payment_contact()
    {
        return $this->morphOne(Contactable::class, 'model')->withCategory('payment_contact');
    }

    public function setContact(array $values)
    {
        if ($this->contact) {
            $this->contact->update($values);
            return $this->contact;
        }

        return $this->contact()->create($values);
    }

    public function setPaymentContact(array $values)
    {
        if ($this->payment_contact) {
            $this->payment_contact->update($values);
            return $this->payment_contact;
        }

        return $this->payment_contact()->create($values);
    }

    public function scopeSearchKey(Builder $query, $skuOrId)
    {
        return $query->where('id', $skuOrId)->orWhere('code', $skuOrId)->limit(1);
    }
}
