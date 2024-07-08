<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;

class CustomerSeeder extends Seeder
{
    protected \Faker\Generator $fake;

    public function run(): void
    {
        $this->fake = fake('ID_id');
        $this->defaultCustomer();
        $this->fakerCustomer(20);
    }

    protected function defaultCustomer()
    {
        Customer::updateOrCreate(['id' => 1], [
            "code" => "general.customer",
            "name" => "General",
            "option" => [
                "tax_no" => null,
                "tax_inclusive" => false,
                // "payment_contactable" => false,
            ]
        ]);

    }

    protected function getRequestCustomer()
    {
        $taxNumber = rand(0,9) > 7 ? null : $this->fake->unique()->numerify("###.##.#####.#");
        return new Request([
            "code" => $this->fake->word ."-". $this->fake->unique()->numberBetween(10000, 999999),
            "name" => join(' ', $this->fake->words(rand(2,4))),
            "contact" => [
                "email" => $this->fake->email(),
                "phone" => $this->fake->phoneNumber(),
                "street" => $this->fake->streetAddress(),
                "city" => $this->fake->city(),
                "zipcode" => $this->fake->postcode(),
            ],
            "option" => [
                "tax_no" => $taxNumber,
                "tax_inclusive" => $taxNumber ? (rand(0,9) > 7 ? true : false) : false,
            ]
        ]);
    }

    protected function fakerCustomer($count = 10)
    {

        for ($i=0; $i < $count; $i++) {
            $request = $this->getRequestCustomer();

            /** @var \App\Http\Resources\CustomerResource $response */
            $response = app(\App\Http\ApiControllers\CustomerController::class)->save($request);

            if (rand(0,10) > 7) app(\App\Http\ApiControllers\CustomerController::class)->disabled($response->resource->id);
        }

    }
}
