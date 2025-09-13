<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user
        $user = User::firstOrCreate([
            'email' => 'admin@example.com'
        ], [
            'name' => 'Admin User',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Create suppliers
        $supplier1 = Supplier::firstOrCreate([
            'name' => 'TechCorp Supplies'
        ], [
            'email' => 'orders@techcorp.com',
            'phone' => '+1-555-0101',
            'address' => '123 Tech Street, Silicon Valley, CA'
        ]);

        $supplier2 = Supplier::firstOrCreate([
            'name' => 'Electronics World'
        ], [
            'email' => 'sales@electronicsworld.com',
            'phone' => '+1-555-0202',
            'address' => '456 Electronic Ave, New York, NY'
        ]);

        // Create customers
        $customer1 = Customer::firstOrCreate([
            'email' => 'john@example.com'
        ], [
            'name' => 'John Doe',
            'phone' => '+1-555-1001',
            'address' => '789 Customer Lane, Los Angeles, CA'
        ]);

        $customer2 = Customer::firstOrCreate([
            'email' => 'jane@example.com'
        ], [
            'name' => 'Jane Smith',
            'phone' => '+1-555-1002',
            'address' => '321 Buyer Street, Chicago, IL'
        ]);

        // Create products
        $products = [
            [
                'name' => 'Wireless Mouse',
                'description' => 'High-precision wireless mouse with ergonomic design',
                'category' => 'Electronics',
                'price' => 29.99,
                'cost_price' => 15.00,
                'stock_quantity' => 50,
                'sku' => 'WM001',
                'minimum_stock_level' => 10,
                'maximum_stock_level' => 100,
                'supplier_id' => $supplier1->id,
            ],
            [
                'name' => 'USB Cable',
                'description' => 'Premium USB-C to USB-A cable, 6ft length',
                'category' => 'Accessories',
                'price' => 12.99,
                'cost_price' => 5.50,
                'stock_quantity' => 75,
                'sku' => 'UC001',
                'minimum_stock_level' => 20,
                'maximum_stock_level' => 150,
                'supplier_id' => $supplier1->id,
            ],
            [
                'name' => 'Bluetooth Headphones',
                'description' => 'Noise-cancelling wireless headphones',
                'category' => 'Electronics',
                'price' => 199.99,
                'cost_price' => 120.00,
                'stock_quantity' => 25,
                'sku' => 'BH001',
                'minimum_stock_level' => 5,
                'maximum_stock_level' => 50,
                'supplier_id' => $supplier2->id,
            ],
            [
                'name' => 'Smartphone Case',
                'description' => 'Protective case for smartphones',
                'category' => 'Accessories',
                'price' => 24.99,
                'cost_price' => 8.00,
                'stock_quantity' => 8, // Low stock for testing
                'sku' => 'SC001',
                'minimum_stock_level' => 15,
                'maximum_stock_level' => 80,
                'supplier_id' => $supplier2->id,
            ],
            [
                'name' => 'Laptop Stand',
                'description' => 'Adjustable aluminum laptop stand',
                'category' => 'Accessories',
                'price' => 49.99,
                'cost_price' => 22.00,
                'stock_quantity' => 0, // Out of stock for testing
                'sku' => 'LS001',
                'minimum_stock_level' => 10,
                'maximum_stock_level' => 40,
                'supplier_id' => $supplier1->id,
            ]
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate([
                'sku' => $productData['sku']
            ], $productData);
        }

        // Create purchases
        $purchase1 = Purchase::firstOrCreate([
            'supplier_id' => $supplier1->id,
            'order_date' => now()->subDays(30),
        ], [
            'total_amount' => 850.00,
            'status' => 'completed'
        ]);

        $purchase2 = Purchase::firstOrCreate([
            'supplier_id' => $supplier2->id,
            'order_date' => now()->subDays(15),
        ], [
            'total_amount' => 1250.00,
            'status' => 'completed'
        ]);

        // Create purchase items
        $mouseProd = Product::where('sku', 'WM001')->first();
        $cableProd = Product::where('sku', 'UC001')->first();
        $headphonesProd = Product::where('sku', 'BH001')->first();
        $caseProd = Product::where('sku', 'SC001')->first();

        if ($mouseProd && $cableProd && $headphonesProd && $caseProd) {
            PurchaseItem::firstOrCreate([
                'purchase_id' => $purchase1->id,
                'product_id' => $mouseProd->id,
            ], [
                'quantity' => 30,
                'unit_price' => 15.00,
                'total_price' => 450.00,
            ]);

            PurchaseItem::firstOrCreate([
                'purchase_id' => $purchase1->id,
                'product_id' => $cableProd->id,
            ], [
                'quantity' => 50,
                'unit_price' => 5.50,
                'total_price' => 275.00,
            ]);

            PurchaseItem::firstOrCreate([
                'purchase_id' => $purchase2->id,
                'product_id' => $headphonesProd->id,
            ], [
                'quantity' => 10,
                'unit_price' => 120.00,
                'total_price' => 1200.00,
            ]);

            PurchaseItem::firstOrCreate([
                'purchase_id' => $purchase2->id,
                'product_id' => $caseProd->id,
            ], [
                'quantity' => 20,
                'unit_price' => 8.00,
                'total_price' => 160.00,
            ]);
        }

        // Create sales
        $sale1 = Sale::firstOrCreate([
            'customer_id' => $customer1->id,
            'sale_date' => now()->subDays(5),
        ], [
            'total_amount' => 242.97,
            'status' => 'completed'
        ]);

        $sale2 = Sale::firstOrCreate([
            'customer_id' => $customer2->id,
            'sale_date' => now()->subDays(2),
        ], [
            'total_amount' => 199.99,
            'status' => 'completed'
        ]);

        // Create sale items
        if ($mouseProd && $cableProd && $headphonesProd) {
            SaleItem::firstOrCreate([
                'sale_id' => $sale1->id,
                'product_id' => $mouseProd->id,
            ], [
                'quantity' => 2,
                'unit_price' => 29.99,
                'total_price' => 59.98,
            ]);

            SaleItem::firstOrCreate([
                'sale_id' => $sale1->id,
                'product_id' => $cableProd->id,
            ], [
                'quantity' => 5,
                'unit_price' => 12.99,
                'total_price' => 64.95,
            ]);

            SaleItem::firstOrCreate([
                'sale_id' => $sale2->id,
                'product_id' => $headphonesProd->id,
            ], [
                'quantity' => 1,
                'unit_price' => 199.99,
                'total_price' => 199.99,
            ]);
        }

        $this->command->info('Test data seeded successfully!');
    }
}
