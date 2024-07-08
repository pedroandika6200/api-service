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

    const CONVERTS = [5, 10, 12, 20, 24];

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
            $request = new Request([
                'type' => \App\Enums\ProductType::ITEM->value,
                'sku' => $this->fake->word . $this->fake->unique()->numerify('-###-###-##'),
                'name' => join(' ', $this->fake->words(rand(2,4))),
                'unit' => collect(static::UNITS)->keys()->shuffle()->first(),
                'sale_price' => 2500 * $nominal,
                'purchase_price' => 2000 * $nominal,
                'category_id' => $cats->shuffle()->first()->id,
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
            $rate = collect(static::CONVERTS)->shuffle()->first();
            $newSKU = $baseRequest->get('sku') . $this->fake->unique()->numerify('-####');
            $request->merge([
                'sku' => $newSKU,
                'name' => $baseRequest->get('name') ." [". $unit ."]",
                'unit' => $unit,
                'sale_price' => $request->get('sale_price') * $rate,
                'purchase_price' => $request->get('purchase_price') * $rate,
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
            $nominal = rand(5, 20);
            $collect->push(Product::create([
                'sku' => $this->fake->word ."-". $this->fake->unique()->numerify('-####'),
                'name' => join(' ', $this->fake->words(rand(2,4))),
                'unit' => collect(static::UNITS)->keys()->shuffle()->first(),
                'sale_price' => 2500 * $nominal,
                'purchase_price' => 2000 * $nominal,
                'category_id' => $cats->shuffle()->first()->id,
                'description' => $this->fake->sentence,
            ]));
        }

        return $collect;
    }
}
