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
            [
                'name' => 'Widget A',
                'description' => 'High-quality electronic widget',
                'sku' => 'WID-001',
                'stock_quantity' => 5,
                'price' => 25.99,
                'category' => 'Electronics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Widget B',
                'description' => 'Durable tool widget',
                'sku' => 'WID-002',
                'stock_quantity' => 2,
                'price' => 45.50,
                'category' => 'Tools',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Widget C',
                'description' => 'Office supply widget',
                'sku' => 'WID-003',
                'stock_quantity' => 8,
                'price' => 12.75,
                'category' => 'Supplies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Widget D',
                'description' => 'Premium widget with advanced features',
                'sku' => 'WID-004',
                'stock_quantity' => 25,
                'price' => 89.99,
                'category' => 'Electronics',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Widget E',
                'description' => 'Basic utility widget',
                'sku' => 'WID-005',
                'stock_quantity' => 50,
                'price' => 8.99,
                'category' => 'Supplies',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
