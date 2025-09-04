<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display the reports index page.
     */
    public function index()
    {
        // Sample report overview data
        $reportsOverview = [
            'total_sales' => 15420.75,
            'total_purchases' => 8950.25,
            'current_stock_value' => 45780.50,
            'low_stock_items' => 8,
            'recent_reports' => [
                'sales' => 'Sales Report - Last 30 Days',
                'inventory' => 'Inventory Status Report',
                'profit_loss' => 'Profit & Loss Summary'
            ]
        ];
        
        return view('reports.index', compact('reportsOverview'));
    }
    
    /**
     * Display sales reports.
     */
    public function sales(Request $request)
    {
        // Get date range from request or set defaults
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Sample sales data - replace with actual database queries
        $sales = collect([
            (object)[
                'id' => 1,
                'sale_date' => now()->subDays(5),
                'product' => (object)['name' => 'Product A'],
                'customer' => (object)['name' => 'Customer 1'],
                'quantity' => 10,
                'unit_price' => 25.00,
                'total_amount' => 250.00
            ],
            (object)[
                'id' => 2,
                'sale_date' => now()->subDays(3),
                'product' => (object)['name' => 'Product B'],
                'customer' => (object)['name' => 'Customer 2'],
                'quantity' => 5,
                'unit_price' => 50.00,
                'total_amount' => 250.00
            ],
            (object)[
                'id' => 3,
                'sale_date' => now()->subDays(1),
                'product' => (object)['name' => 'Product C'],
                'customer' => (object)['name' => 'Customer 3'],
                'quantity' => 8,
                'unit_price' => 30.00,
                'total_amount' => 240.00
            ]
        ]);
        
        // Calculate totals
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        
        return view('reports.sales', compact('sales', 'totalSales', 'totalRevenue', 'startDate', 'endDate'));
    }
    
    /**
     * Display inventory reports.
     */
    public function inventory()
    {
        // Sample data - replace with actual database queries
        $inventoryData = [];
        
        return view('reports.inventory', compact('inventoryData'));
    }
}
