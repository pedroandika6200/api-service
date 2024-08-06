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
            'premount' => 0,
        ]);
    }

    public function mounting (Model $model, int $premount, $receiveID, $validate = true)
    {
        if (!$this->getAttribute('product_id')) {
            $this->setAttribute('receive_order_id', $receiveID);
            $this->setAttribute('product_id', $model->product->id);
            $this->setAttribute('capacity', $this->getCapacity($model->product));
            $this->setAttribute('amount', 0);
            $this->setAttribute('premount', 0);
            $this->save();
        }
        else if ($this->getAttribute('product_id') !== $model->product->id) {
            abort(406, "The Product storing invalid!");
        }

        if ($validate && $premount > $this->available) {
            abort(406, "The capacity stored has been overload");
        }

        static::where($this->getKeyName(), $this->getKey())->update(['premount' => app('db')->raw(" (premount + $premount)")]);
    }

    public function mounted (Model $model, int $amount, $validate = true)
    {
        if ($validate && $this->premount < $amount) {
            abort(406, "The mounted stored has been more than premount");
        }

        static::where($this->getKeyName(), $this->getKey())->update([
            'premount' => app('db')->raw(" (premount - $amount)"),
            'amount' => app('db')->raw(" (amount + $amount)"),
        ]);

        $model->lockerables()->create([
            "locker_id" => $this->id,
            "product_id" => $model->product->id,
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
        return intval($this->capacity) - intval($this->premount) - intval($this->amount);
    }

    public function getCapacity(Product $product) :? int
    {
        if ($this->volume === null || $product->volume === null) return null;
        return intval($this->volume / $product->volume);
    }

    protected function scopeWhereMounting (Builder $query, $productID = null, $receiveID = null)
    {
        return $query->whereNull('product_id')->when($productID,
            fn($q) => $q->orWhere(
                fn($q) => $q->where('product_id', $productID)->whereColumn(app('db')->raw('(amount + premount)'), '<' , 'capacity')
                            ->when($productID, fn($q) => $q->where('receive_order_id', $receiveID))
            )
        );
    }
}
