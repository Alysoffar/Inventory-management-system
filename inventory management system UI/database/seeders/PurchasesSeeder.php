<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\Supplier;

class PurchasesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First create some suppliers if they don't exist
        $suppliers = Supplier::all();
        if ($suppliers->count() == 0) {
            Supplier::create([
                'name' => 'Supplier Inc.',
                'email' => 'contact@supplier.com',
                'phone' => '555-1001',
                'address' => '100 Supply St',
            ]);
            
            Supplier::create([
                'name' => 'Supply Corp.', 
                'email' => 'info@supplycorp.com',
                'phone' => '555-1002',
                'address' => '200 Vendor Ave',
            ]);
            
            Supplier::create([
                'name' => 'Materials Ltd.',
                'email' => 'sales@materials.com', 
                'phone' => '555-1003',
                'address' => '300 Materials Blvd',
            ]);
            
            $suppliers = Supplier::all();
        }

        // Create sample purchases
        $purchases = [
            [
                'supplier_id' => $suppliers->random()->id,
                'purchase_date' => now()->subDays(1)->toDateString(),
                'total_amount' => 450.00,
                'status' => 'completed',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'supplier_id' => $suppliers->random()->id,
                'purchase_date' => now()->subDays(2)->toDateString(),
                'total_amount' => 320.75,
                'status' => 'completed',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
            [
                'supplier_id' => $suppliers->random()->id,
                'purchase_date' => now()->subDays(3)->toDateString(),
                'total_amount' => 675.50,
                'status' => 'pending',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'supplier_id' => $suppliers->random()->id,
                'purchase_date' => now()->subDays(5)->toDateString(),
                'total_amount' => 289.25,
                'status' => 'completed',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'supplier_id' => $suppliers->random()->id,
                'purchase_date' => now()->subDays(7)->toDateString(),
                'total_amount' => 1240.80,
                'status' => 'completed',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(7),
            ],
        ];

        foreach ($purchases as $purchaseData) {
            Purchase::create($purchaseData);
        }
    }
}
