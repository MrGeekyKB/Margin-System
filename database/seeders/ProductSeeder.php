<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Cooler', 'making_cost' => 8000.00, 'selling_price' => 12000.00],
            ['name' => 'A.C.', 'making_cost' => 20000.00, 'selling_price' => 30000.00],
            ['name' => 'Refrigerator', 'making_cost' => 15000.00, 'selling_price' => 25000.00],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
