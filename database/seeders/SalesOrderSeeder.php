<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SalesOrderSeeder extends Seeder
{

    protected \Faker\Generator $fake;

    public function run(): void
    {
        $this->fake = fake('ID_id');
        $this->fakerSalesOrder(55);
    }

    protected function fakerSalesOrder($count = 10)
    {
        $customers = \App\Models\Customer::all();

        for ($i=0; $i < $count; $i++) {
            $customer = $customers->shuffle()->first();

            $discount = rand() < 5 ? null : rand(5, 10) * 5000;
            $taxable = (rand(0,9) < 7 ? null : boolval($customer->getOption('tax_no', null)));
            $taxInclusive = rand(0,9) < 7 ? null : boolval($customer->getOption('tax_inclusive', null));

            $custInfo = [
                "customer_name" => $customer->name,
                "customer_phone" => $customer->contact?->phone ?? null,
                "customer_address" => $customer->contact?->address ?? null,
            ];

            $request = new Request([
                "customer_id" => $customer->id,
                "number" => null,
                "date" => $this->fake->dateTimeBetween('-1 week'),
                "due" => rand(0, 9) < 7 ? null : $this->fake->dateTimeBetween('now', '1 week'),
                "reference" => rand(0, 9) < 7 ? null : $this->fake->word . $this->fake->numerify("-##-####"),
                "description" => rand(0, 9) < 7 ? null : $this->fake->sentence(),
                "items" => $this->fakerSalesOrderItems($customer, rand(1, 8))->toArray(),
                "discount" => $discount,
                "option" => [
                    "taxable" => $taxable,
                    "tax_inclusive" => $taxInclusive,
                    "shipcust" => rand(0,9) < 7 ? null : $custInfo,
                    "paycust" => rand(0,9) < 7 ? null : $custInfo,
                ]
            ]);

            app(\App\Http\ApiControllers\SalesOrderController::class)->save($request);
        }

    }

    protected function fakerSalesOrderItems($customer, $count = 5): Collection
    {
        $products = \App\Models\Product::all();
        $taxable = $customer->getOption('tax_no');

        $collect = collect();
        for ($i=0; $i < $count; $i++) {
            $product = $products->shuffle()->first();
            $discsen = rand(0,9) < 5 ? null : intval(rand(2, 5) * 5);
            $discprice = boolval($discsen) ? 0 : $discsen * $product->sale_price;



            $collect->push([
                "name" => $product->name,
                "unit" => $product->unit,
                "quantity" => rand(1, 5),
                "price" => $product->sale_price,
                "discprice" => $discprice,
                "notes" => rand() < 7 ? null : $this->fake->sentence(),
                "product_id" => $product->getKey(),
                "seq" => $i+1,
                "option" => [
                    "discprice_sen" => rand(0,9) < 5 ? null : $discsen,
                    "taxsen_income"  => $taxable ? $product->getOption('taxsen_income') : null,
                    "taxsen_service" => $taxable ? $product->getOption('taxsen_service') : null,
                ]
            ]);
        }

        return $collect;
    }
}
