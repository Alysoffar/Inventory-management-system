<?php

// Simple test script to add sample products for testing
// Place this file in your Laravel project root and run: php test_products.php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\Supplier;

// Check if we have any products
$productCount = Product::count();
echo "Current products in database: $productCount\n";

if ($productCount == 0) {
    echo "No products found. Creating sample products...\n";
    
    // Create a sample supplier if none exists
    $supplier = Supplier::first();
    if (!$supplier) {
        $supplier = Supplier::create([
            'name' => 'Sample Supplier Inc.',
            'contact_person' => 'John Doe',
            'email' => 'supplier@example.com',
            'phone' => '123-456-7890',
            'address' => '123 Business St, City, State 12345',
            'status' => 'active'
        ]);
        echo "Created sample supplier: {$supplier->name}\n";
    }
    
    // Sample products
    $sampleProducts = [
        [
            'name' => 'Laptop Computer',
            'sku' => 'LAP-001',
            'description' => 'High-performance laptop for business use',
            'category' => 'Electronics',
            'price' => 999.99,
            'cost_price' => 750.00,
            'stock_quantity' => 25,
            'minimum_stock_level' => 5,
            'maximum_stock_level' => 50,
            'location' => 'Warehouse A-1',
            'supplier_id' => $supplier->id,
            'status' => 'active'
        ],
        [
            'name' => 'Office Chair',
            'sku' => 'CHR-001',
            'description' => 'Ergonomic office chair with lumbar support',
            'category' => 'Furniture',
            'price' => 199.99,
            'cost_price' => 120.00,
            'stock_quantity' => 15,
            'minimum_stock_level' => 3,
            'maximum_stock_level' => 30,
            'location' => 'Warehouse B-2',
            'supplier_id' => $supplier->id,
            'status' => 'active'
        ],
        [
            'name' => 'Wireless Mouse',
            'sku' => 'MOU-001',
            'description' => 'Wireless optical mouse with USB receiver',
            'category' => 'Electronics',
            'price' => 29.99,
            'cost_price' => 15.00,
            'stock_quantity' => 50,
            'minimum_stock_level' => 10,
            'maximum_stock_level' => 100,
            'location' => 'Warehouse A-3',
            'supplier_id' => $supplier->id,
            'status' => 'active'
        ],
        [
            'name' => 'Office Desk',
            'sku' => 'DSK-001',
            'description' => 'Modern office desk with drawers',
            'category' => 'Furniture',
            'price' => 299.99,
            'cost_price' => 180.00,
            'stock_quantity' => 8,
            'minimum_stock_level' => 2,
            'maximum_stock_level' => 20,
            'location' => 'Warehouse B-1',
            'supplier_id' => $supplier->id,
            'status' => 'active'
        ],
        [
            'name' => 'Smartphone',
            'sku' => 'PHN-001',
            'description' => 'Latest model smartphone with advanced features',
            'category' => 'Electronics',
            'price' => 699.99,
            'cost_price' => 450.00,
            'stock_quantity' => 0, // Out of stock item
            'minimum_stock_level' => 5,
            'maximum_stock_level' => 25,
            'location' => 'Warehouse A-2',
            'supplier_id' => $supplier->id,
            'status' => 'active'
        ]
    ];
    
    foreach ($sampleProducts as $productData) {
        $product = Product::create($productData);
        echo "Created product: {$product->name} (SKU: {$product->sku}) - Stock: {$product->stock_quantity}\n";
    }
    
    echo "\nSample products created successfully!\n";
    echo "Total products now: " . Product::count() . "\n";
} else {
    echo "Products already exist in database.\n";
    
    // Show current products
    $products = Product::take(5)->get();
    echo "\nFirst 5 products:\n";
    foreach ($products as $product) {
        echo "- {$product->name} (SKU: {$product->sku}) - Stock: {$product->stock_quantity}\n";
    }
}

echo "\nDone!\n";
?>