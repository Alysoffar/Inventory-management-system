<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        // Dashboard data - Replace with actual database queries when models are created
        $totalProducts = 125; // Example data
        $totalCustomers = 89; // Example data
        $totalSuppliers = 34; // Example data
        
        // Today's metrics - Replace with actual queries
        $todayRevenue = 2540.50; // Example data
        $todaySalesCount = 18; // Example data
        
        // Low stock products - Replace with actual collection when models are ready
        $lowStockProducts = collect([
            (object)['name' => 'Widget A', 'quantity' => 5, 'category' => 'Electronics'],
            (object)['name' => 'Widget B', 'quantity' => 2, 'category' => 'Tools'],
            (object)['name' => 'Widget C', 'quantity' => 8, 'category' => 'Supplies']
        ]);
        
        // Recent sales - Replace with actual data
        $recentSales = collect([
            (object)[
                'id' => 1,
                'product' => (object)['name' => 'Widget A'],
                'customer' => (object)['name' => 'John Doe'],
                'total_amount' => 125.50,
                'created_at' => now()->subHours(2)
            ],
            (object)[
                'id' => 2,
                'product' => (object)['name' => 'Widget B'],
                'customer' => (object)['name' => 'Jane Smith'],
                'total_amount' => 89.25,
                'created_at' => now()->subHours(5)
            ],
            (object)[
                'id' => 3,
                'product' => (object)['name' => 'Widget C'],
                'customer' => (object)['name' => 'Bob Johnson'],
                'total_amount' => 156.75,
                'created_at' => now()->subDays(1)
            ]
        ]);
        
        // Recent purchases - Replace with actual data
        $recentPurchases = collect([
            (object)[
                'id' => 1,
                'product' => (object)['name' => 'Raw Material A'],
                'supplier' => (object)['name' => 'Supplier Inc.'],
                'total_cost' => 450.00,
                'created_at' => now()->subDays(1)
            ],
            (object)[
                'id' => 2,
                'product' => (object)['name' => 'Raw Material B'],
                'supplier' => (object)['name' => 'Supply Corp.'],
                'total_cost' => 320.75,
                'created_at' => now()->subDays(2)
            ]
        ]);
        
        // Monthly revenue data for chart
        $monthlyRevenue = [
            ['month' => 'Jan', 'revenue' => 12500],
            ['month' => 'Feb', 'revenue' => 15200],
            ['month' => 'Mar', 'revenue' => 18900],
            ['month' => 'Apr', 'revenue' => 16700],
            ['month' => 'May', 'revenue' => 21300],
            ['month' => 'Jun', 'revenue' => 19800]
        ];
        
        return view('dashboard', compact(
            'totalProducts',
            'totalCustomers', 
            'totalSuppliers',
            'todayRevenue',
            'todaySalesCount',
            'lowStockProducts',
            'recentSales',
            'recentPurchases',
            'monthlyRevenue'
        ));
    }
    
    /**
     * Display the inventory dashboard.
     */
    public function inventory()
    {
        // Sample inventory data
        $inventoryStats = [
            'total_items' => 125,
            'low_stock_items' => 8,
            'out_of_stock_items' => 3,
            'total_value' => 45780.50
        ];
        
        return view('inventory.dashboard', compact('inventoryStats'));
    }
}
