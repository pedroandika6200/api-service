<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Locker extends Model
{
    protected $fillable = ["code", "position", "dimension", "wmax"];

    protected $casts = [
        "dimension" => "array",
        "wmax" => "double",
        "capacity" => "integer",
        "amount" => "integer",
    ];

    public function rack ()
    {
        return $this->belongsTo(Rack::class);
    }

    public function product ()
    {
        return $this->belongsTo(Product::class);
    }

    public function lockerables ()
    {
        return $this->hasMany(Lockerable::class);
    }

    public static function resetStore()
    {
        static::query()->update([
            'receive_order_id' => null,
            'product_id' => null,
            'amount' => 0,
            'capacity' => 0,
        ]);
    }

    public function mounting (Product $product, int $amount, $receiveID, $validate = true)
    {
        if (!$this->getAttribute('product_id')) {
            $this->setAttribute('receive_order_id', $receiveID);
            $this->setAttribute('product_id', $product->id);
            $this->setAttribute('capacity', $this->getCapacity($product));
            $this->setAttribute('amount', 0);
            $this->save();
        }
        else if ($this->getAttribute('product_id') !== $product->id) {
            abort(406, "The Product storing invalid!");
        }

        if ($validate && $amount > $this->available) {
            abort(406, "The capacity stored has been overload");
        }

        static::where($this->getKeyName(), $this->getKey())->update(['amount' => app('db')->raw(" (amount + $amount)")]);

        $this->lockerables()->create([
            "product_id" => $product->id,
            "amount" => $amount,
        ]);
    }

    public function getVolumeAttribute() :? int
    {
        $dim = $this->getAttribute('dimension') ?? [];
        return intval($dim[0]) * intval($dim[1]) * intval($dim[2]) ?: null;
    }

    public function getAvailableAttribute():? int
    {
        if (!$this->getAttribute('product_id')) return null;
        return intval($this->capacity) - intval($this->amount);
    }

    public function getCapacity(Product $product) :? int
    {
        if ($this->volume === null || $product->volume === null) return null;
        return intval($this->volume / $product->volume);
    }

    protected function scopeWhereMountable (Builder $query, $productID = null, $receiveID = null)
    {
        return $query->whereNull('product_id')->when($productID,
            fn($q) => $q->orWhere(
                fn($q) => $q->where('product_id', $productID)->whereColumn('amount', '<' , 'capacity')
                            ->when($productID, fn($q) => $q->where('receive_order_id', $receiveID))
            )
        );
    }
}
