<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\Request;

class RackLockerSeeder extends Seeder
{
    protected \Faker\Generator $fake;

    public function run(): void
    {
        $this->fake = fake('ID_id');
        $this->fakerRacks(20);
    }

    protected function fakerRacks($count = 10)
    {
        for ($i=0; $i < $count; $i++) {
            $request = new Request([
                "code" => "RACK[". ($i+1) ."]",
                "position" => null,
            ]);

            $response = app(\App\Http\ApiControllers\RackController::class)->save($request);

            $this->fakerLockers($response->resource);
        }

    }

    protected function fakerLockers(\App\Models\Rack $rack, $pa = ["A", "B", "C"], $ta = ["A", "B", "C"])
    {
        $dimension = [rand(10,18)*10, rand(8,10)*10, 50]; // 100 Kg
        $wmax = 400 ; // 400 KG
        foreach ($pa as $x) {
            foreach ($ta as $y) {
                $request = new Request([
                    "rack_id" => $rack->id,
                    "code" => $rack->code . "[$x$y]",
                ]);
                $request->merge([
                    "position" => null,
                    "dimension" => $dimension,
                    "wmax" => $wmax,
                ]);

                app(\App\Http\ApiControllers\LockerController::class)->save($request);
            }
        }

    }
}
