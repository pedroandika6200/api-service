<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \App\Models\ProductCategory::create([
            "id" => 1,
            "name" => "General",
        ]);

        $this->fakerProductCategory();
    }

    protected function fakerProductCategory()
    {
        $data = [
            ["id" => 2, "name" => "A Category"],
            ["id" => 3, "name" => "B Category"],
            ["id" => 4, "name" => "C Category"],
            ["id" => 5, "name" => "A-1 Category"],
        ];

        foreach ($data as $key => $row) {
            \App\Models\ProductCategory::firstOrCreate(["id" => $row['id']], $row);
        }
    }
}
