<?php
// Script to create sample data for the inventory management system

// First, run this in Laravel context using: php artisan tinker < create_sample_data.php

// Create sample customers
\App\Models\Customer::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '+1234567890',
    'address' => '123 Main St, City, State'
]);

\App\Models\Customer::create([
    'name' => 'Jane Smith', 
    'email' => 'jane@example.com',
    'phone' => '+1234567891',
    'address' => '456 Oak Ave, City, State'
]);

\App\Models\Customer::create([
    'name' => 'Bob Johnson',
    'email' => 'bob@example.com', 
    'phone' => '+1234567892',
    'address' => '789 Pine St, City, State'
]);

// Create sample suppliers
\App\Models\Supplier::create([
    'name' => 'Supplier Inc',
    'email' => 'supplier@example.com',
    'phone' => '+1234567893',
    'address' => '321 Business Blvd, City, State'
]);

\App\Models\Supplier::create([
    'name' => 'Supply Corp',
    'email' => 'supply@example.com',
    'phone' => '+1234567894', 
    'address' => '654 Commerce St, City, State'
]);

// Create sample products
\App\Models\Product::create([
    'name' => 'Widget A',
    'description' => 'High-quality widget for electronics',
    'sku' => 'WGT-A-001',
    'category' => 'Electronics',
    'price' => 125.00,
    'cost_price' => 75.00,
    'stock_quantity' => 150,
    'minimum_stock_level' => 20,
    'location' => 'Warehouse A, Shelf 1'
]);

\App\Models\Product::create([
    'name' => 'Widget B',
    'description' => 'Premium widget for tools',
    'sku' => 'WGT-B-001', 
    'category' => 'Tools',
    'price' => 89.00,
    'cost_price' => 55.00,
    'stock_quantity' => 75,
    'minimum_stock_level' => 15,
    'location' => 'Warehouse B, Shelf 2'
]);

\App\Models\Product::create([
    'name' => 'Widget C',
    'description' => 'Essential widget for supplies',
    'sku' => 'WGT-C-001',
    'category' => 'Supplies', 
    'price' => 157.00,
    'cost_price' => 95.00,
    'stock_quantity' => 10, // Low stock
    'minimum_stock_level' => 25,
    'location' => 'Warehouse C, Shelf 3'
]);

\App\Models\Product::create([
    'name' => 'Raw Material A',
    'description' => 'High-grade raw material',
    'sku' => 'RM-A-001',
    'category' => 'Raw Materials',
    'price' => 199.00,
    'cost_price' => 120.00,
    'stock_quantity' => 200,
    'minimum_stock_level' => 50,
    'location' => 'Warehouse A, Shelf 4'
]);

\App\Models\Product::create([
    'name' => 'Raw Material B',
    'description' => 'Premium raw material',
    'sku' => 'RM-B-001',
    'category' => 'Raw Materials',
    'price' => 339.75,
    'cost_price' => 200.00,
    'stock_quantity' => 8, // Low stock
    'minimum_stock_level' => 30,
    'location' => 'Warehouse B, Shelf 5'
]);

// Create sample sales for today
$today = \Carbon\Carbon::today();
$customer1 = \App\Models\Customer::first();
$customer2 = \App\Models\Customer::skip(1)->first();
$customer3 = \App\Models\Customer::skip(2)->first();

$sale1 = \App\Models\Sale::create([
    'customer_id' => $customer1->id,
    'sale_date' => $today,
    'total_amount' => 125.00,
    'status' => 'completed'
]);

$sale2 = \App\Models\Sale::create([
    'customer_id' => $customer2->id,
    'sale_date' => $today,
    'total_amount' => 89.00,
    'status' => 'completed'
]);

$sale3 = \App\Models\Sale::create([
    'customer_id' => $customer3->id,
    'sale_date' => $today,
    'total_amount' => 157.00,
    'status' => 'completed'
]);

// Create sale items
$product1 = \App\Models\Product::where('name', 'Widget A')->first();
$product2 = \App\Models\Product::where('name', 'Widget B')->first();
$product3 = \App\Models\Product::where('name', 'Widget C')->first();

\App\Models\SaleItem::create([
    'sale_id' => $sale1->id,
    'product_id' => $product1->id,
    'quantity' => 1,
    'unit_price' => 125.00,
    'total_price' => 125.00
]);

\App\Models\SaleItem::create([
    'sale_id' => $sale2->id,
    'product_id' => $product2->id,
    'quantity' => 1,
    'unit_price' => 89.00,
    'total_price' => 89.00
]);

\App\Models\SaleItem::create([
    'sale_id' => $sale3->id,
    'product_id' => $product3->id,
    'quantity' => 1,
    'unit_price' => 157.00,
    'total_price' => 157.00
]);

// Create sample purchases
$supplier1 = \App\Models\Supplier::first();
$supplier2 = \App\Models\Supplier::skip(1)->first();

$purchase1 = \App\Models\Purchase::create([
    'supplier_id' => $supplier1->id,
    'purchase_date' => $today->subDays(1),
    'total_amount' => 599.95,
    'status' => 'completed'
]);

$purchase2 = \App\Models\Purchase::create([
    'supplier_id' => $supplier2->id,
    'purchase_date' => $today->subDays(2),
    'total_amount' => 339.75,
    'status' => 'completed'
]);

// Create purchase items
$product4 = \App\Models\Product::where('name', 'Raw Material A')->first();
$product5 = \App\Models\Product::where('name', 'Raw Material B')->first();

\App\Models\PurchaseItem::create([
    'purchase_id' => $purchase1->id,
    'product_id' => $product4->id,
    'quantity' => 3,
    'unit_cost' => 199.00,
    'total_cost' => 597.00
]);

\App\Models\PurchaseItem::create([
    'purchase_id' => $purchase2->id,
    'product_id' => $product5->id,
    'quantity' => 1,
    'unit_cost' => 339.75,
    'total_cost' => 339.75
]);

echo "Sample data created successfully!\n";
echo "Products: " . \App\Models\Product::count() . "\n";
echo "Customers: " . \App\Models\Customer::count() . "\n";
echo "Sales: " . \App\Models\Sale::count() . "\n";
echo "Purchases: " . \App\Models\Purchase::count() . "\n";