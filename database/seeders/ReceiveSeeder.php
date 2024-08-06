<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReceiveSeeder extends Seeder
{
    protected \Faker\Generator $fake;
    protected Collection $products;

    public function run(): void
    {
        $this->fake = fake('ID_id');
        $this->truncate();

        $this->products = \App\Models\Product::all();
        $this->fakerReceive(5);
    }

    protected function truncate ()
    {
        Schema::disableForeignKeyConstraints();
        \App\Models\ReceiveOrderItem::truncate();
        \App\Models\ReceiveOrder::truncate();

        \App\Models\Lockerable::truncate();
        \App\Models\Locker::resetStore();
        Schema::enableForeignKeyConstraints();
    }

    protected function fakerReceive($count = 1)
    {
        for ($i=0; $i < $count; $i++) {
            $request = new Request([
                "date" => $this->fake->date,
                "number" => $this->fake->word(),
                "reference" => "Receive[". ($i+1) ."]",
            ]);

            $response = app(\App\Http\ApiControllers\ReceiveOrderController::class)->save($request);

            $this->fakerReceiveItem($response->resource, rand(2,10));
        }

    }

    protected function fakerReceiveItem($receive, $count = 10)
    {
        $products = $this->products->shuffle()->take(5);
        $trollies = collect();
        $pallets = $this->pallets()->shuffle();

        for ($i=0; $i < $count; $i++) {
            $product = $products->first();
            $request = new Request([
                "pallet" => $pallets[$i],
                "receive_order_id" => $receive->id,
                "product_id" => $product->id,
                "amount" => rand(5,10) * rand(4,5)
            ]);

            $response = app(\App\Http\ApiControllers\ReceiveOrderItemController::class)->store($request);

            $trollies->push($response->getData());
        }

        sleep(2);

        $trollies->each(fn($e) => $this->fakerMounted(
            \App\Models\ReceiveOrderItem::has('mounts')->whereNull('mounted_uid')->get()->shuffle()->first()
        ));
    }

    protected function fakerMounted(\App\Models\ReceiveOrderItem $receiveItem = null)
    {
        if (!$receiveItem) return;
        $request = new Request(['id' => $receiveItem->id]);
        // app(\App\Http\ApiControllers\ReceiveOrderItemController::class)->setMounted($request);
    }

    protected function pallets()
    {
        $pallets = collect();
        collect(['K','L','M','N','P','Q','R'])->each(fn($i) => collect(['T','U','V','W','X','Y','Z'])->each(fn($j) => $pallets->push("PALLET[$i$j]")));
        return $pallets;
    }
}
