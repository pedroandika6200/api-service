<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Recordset\Concerns\HasFilterable;
use Recordset\Concerns\HasGenerateNumber;
use Recordset\Concerns\HasOptionProperty;

class SalesOrder extends Model
{
    use HasFilterable, HasOptionProperty, HasGenerateNumber;

    protected $fillable = ['number', 'date', 'due', 'reference', 'description', 'customer_id'];

    protected $casts = [
        'option' => 'array',
        'option.taxable' => 'bool',
        'option.tax_inclusive' => 'bool',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function setNumber()
    {
        if (!str($this->number)->startsWith('DRAFT-')) return;

        $prefix = 'SO';
        $digits = 5;
        $separator = '/';

        $date = date('Y-m-d');
        $strtime = $this->getNumberPeriod($date, ['ROMAN:m', 'Y'], $separator);
        $strset = "{number}" . $separator . $prefix . $separator . $strtime;

        \App\Jobs\GenerateNumber::dispatchSync($this, $strset, $digits);
    }

    protected static function booted()
    {
        static::creating(function (self $model) {
            if(!$model->number) $model->number = "DRAFT-". str()->uuid();
        });

    }
}
