<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductMargin;

class ProductMarginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example margins for each product
        $margins = [
            'A.C.' => [
                ['min' => 1, 'max' => 5, 'company' => 22, 'distributor' => 10, 'shop' => 15],
                ['min' => 6, 'max' => 20, 'company' => 20, 'distributor' => 12, 'shop' => 13],
                ['min' => 21, 'max' => 100, 'company' => 18, 'distributor' => 10, 'shop' => 10],
            ],
            'Cooler' => [
                ['min' => 1, 'max' => 10, 'company' => 20, 'distributor' => 8, 'shop' => 12],
                ['min' => 6, 'max' => 20, 'company' => 20, 'distributor' => 12, 'shop' => 13],
                ['min' => 21, 'max' => 100, 'company' => 18, 'distributor' => 10, 'shop' => 10],
            ],
            'Refrigerator' => [
                ['min' => 1, 'max' => 5, 'company' => 21, 'distributor' => 11, 'shop' => 14],
                ['min' => 6, 'max' => 20, 'company' => 20, 'distributor' => 12, 'shop' => 13],
                ['min' => 21, 'max' => 100, 'company' => 18, 'distributor' => 10, 'shop' => 10],
            ],
        ];

        foreach ($margins as $productName => $ranges) {
            $product = Product::where('name', $productName)->first();
            if (!$product) continue;

            foreach ($ranges as $range) {
                ProductMargin::create([
                    'product_id' => $product->id,
                    'min_quantity' => $range['min'],
                    'max_quantity' => $range['max'],
                    'company_margin' => $range['company'],
                    'distributor_margin' => $range['distributor'],
                    'shop_margin' => $range['shop'],
                ]);
            }
        }
    }
}
