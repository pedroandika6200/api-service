<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;

class ProductSeeder extends Seeder
{

    const UNITS = [
        'Pcs' => ['Box', 'Palet'],
        'Kg' => ['sack', 'Box', 'Palet'],
        'Expl' => ['Box', 'Palet'],
    ];

    const CONVERTS = [
        5 => [
            [5,1,1]
        ],
        10 => [
            [5,2,1]
        ],
        12 => [
            [6,2,1], [4,3,1]
        ],
        20 => [
            [10,2,1], [5,4,1]
        ],
        24 => [
            [6,4,1], [6,2,2], [4,3,2]
        ],
    ];

    protected \Faker\Generator $fake;

    public function run(): void
    {
        $this->fake = fake('ID_id');
        $this->fakerProduct(20);
    }

    protected function fakerProduct($count = 10)
    {
        $cats = \App\Models\ProductCategory::all();
        $nominal = rand(5, 10);

        for ($i=0; $i < $count; $i++) {
            $unit = collect(static::UNITS)->keys()->shuffle()->first();
            $weight = $unit == "Kg" ? 1000 : (rand(2,50) * 100);
            $dimension = $this->getVolume();

            $request = new Request([
                'type' => \App\Enums\ProductType::ITEM->value,
                'sku' => $this->fake->word . $this->fake->unique()->numerify('-###-###-##'),
                'name' => join(' ', $this->fake->words(rand(2,4))),
                'unit' => $unit,
                'sale_price' => 2500 * $nominal,
                'purchase_price' => 2000 * $nominal,
                'category_id' => $cats->shuffle()->first()->id,
                'dimension' => $dimension,
                'weight' => $weight,
                'description' => $this->fake->sentence,
                'option' => [
                    'taxsen_income' => rand(0,4) > 3 ? null : 11,
                    'taxsen_serivce' => rand(0,4) > 3 ? null : 11,
                ]
            ]);


            if (rand(0,9) > 6) {
                $collect = $this->fakerProductPartials();
                $request->merge(['partials' => $collect->map(fn($e) => ['part_id' => $e->id, 'count' => rand(1,5)])->toArray()]);
            }

            app(\App\Http\ApiControllers\ProductController::class)->save($request);

            if (rand(0,9) > 4) {
                $this->fakerProductConverts($request);
            }
        }

    }

    protected function fakerProductConverts(Request $baseRequest)
    {
        $request =  new Request($baseRequest->all());

        $converts = collect(static::UNITS)->firstWhere(fn($e, $key) => $key === $request->unit);

        foreach ($converts as $unit) {
            $rate = collect(static::CONVERTS)->keys()->shuffle()->first();
            $weight = intval($baseRequest->get('weight')) * $rate;
            $dimensions = collect(static::CONVERTS[$rate] ?? [])->shuffle()->first();
            $dimension = $this->getVolume(...($dimensions ?? []));

            $newSKU = $baseRequest->get('sku') . $this->fake->unique()->numerify('-####');
            $request->merge([
                'sku' => $newSKU,
                'name' => $baseRequest->get('name'),
                'unit' => $unit,
                'sale_price' => $request->get('sale_price') * $rate,
                'purchase_price' => $request->get('purchase_price') * $rate,
                'dimension' => $dimension,
                'weight' => $weight,
                'convertable' => [
                    ['point_id' => $baseRequest->get('sku'), 'rate' => $rate]
                ]
            ]);

            app(\App\Http\ApiControllers\ProductController::class)->save($request);
        }
    }

    protected function fakerProductPartials()
    {

        $cats = \App\Models\ProductCategory::all();
        $collect = collect();
        for ($i=0; $i < rand(2, 4); $i++) {
            $unit = collect(static::UNITS)->keys()->shuffle()->first();
            $weight = $unit == "Kg" ? 1000 : (rand(2,50) * 100);
            $dimension = $this->getVolume();

            $nominal = rand(5, 20);
            $collect->push(Product::create([
                'sku' => $this->fake->word ."-". $this->fake->unique()->numerify('-####'),
                'name' => join(' ', $this->fake->words(rand(2,4))),
                'unit' => collect(static::UNITS)->keys()->shuffle()->first(),
                'sale_price' => 2500 * $nominal,
                'purchase_price' => 2000 * $nominal,
                'dimension' => $dimension,
                'weight' => $weight,
                'category_id' => $cats->shuffle()->first()->id,
                'description' => $this->fake->sentence,
            ]));
        }

        return $collect;
    }

    protected function getVolume ($x = 1, $y = 1, $t = 1)
    {
        $fnMax = fn ($n) => $n > 100 ? 100 : $n;
        $nx = rand(1, 5) * ($x * 2);
        $ny = rand(1, 5) * ($y * 5);
        $nt = rand(1, 5) * ($t * 5);

        return [$fnMax($nx), $fnMax($ny), $fnMax($nt)];
    }
}
