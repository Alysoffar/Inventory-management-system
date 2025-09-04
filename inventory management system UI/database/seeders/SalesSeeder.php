<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First create some customers if they don't exist
        $customers = Customer::all();
        if ($customers->count() == 0) {
            Customer::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'phone' => '555-0123',
                'address' => '123 Main St',
            ]);
            
            Customer::create([
                'name' => 'Jane Smith', 
                'email' => 'jane@example.com',
                'phone' => '555-0456',
                'address' => '456 Oak Ave',
            ]);
            
            Customer::create([
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com', 
                'phone' => '555-0789',
                'address' => '789 Pine St',
            ]);
            
            $customers = Customer::all();
        }

        $products = Product::all();
        
        // Create sample sales
        $sales = [
            [
                'customer_id' => $customers->random()->id,
                'sale_date' => now()->subHours(2)->toDateString(),
                'total_amount' => 125.50,
                'status' => 'completed',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2),
            ],
            [
                'customer_id' => $customers->random()->id,
                'sale_date' => now()->subHours(5)->toDateString(),
                'total_amount' => 89.25,
                'status' => 'completed',
                'created_at' => now()->subHours(5),
                'updated_at' => now()->subHours(5),
            ],
            [
                'customer_id' => $customers->random()->id,
                'sale_date' => now()->subDays(1)->toDateString(),
                'total_amount' => 156.75,
                'status' => 'pending',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'customer_id' => $customers->random()->id,
                'sale_date' => now()->subDays(2)->toDateString(),
                'total_amount' => 67.99,
                'status' => 'completed',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'customer_id' => $customers->random()->id,
                'sale_date' => now()->subDays(3)->toDateString(),
                'total_amount' => 234.80,
                'status' => 'completed',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
        ];

        foreach ($sales as $saleData) {
            Sale::create($saleData);
        }
    }
}
