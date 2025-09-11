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
    
    /**
     * Display profit and loss report with AI predictions
     */
    public function profitLoss(Request $request)
    {
        // Get date range from request or set defaults
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Sample current period data
        $currentPeriod = [
            'revenue' => 45750.80,
            'cost_of_goods_sold' => 28450.25,
            'gross_profit' => 17300.55,
            'operating_expenses' => 8950.30,
            'net_profit' => 8350.25,
            'margin_percentage' => 18.25
        ];
        
        // Sample previous period for comparison
        $previousPeriod = [
            'revenue' => 38920.60,
            'cost_of_goods_sold' => 24680.40,
            'gross_profit' => 14240.20,
            'operating_expenses' => 8200.15,
            'net_profit' => 6040.05,
            'margin_percentage' => 15.52
        ];
        
        // AI Predictions for next period
        $aiPredictions = [
            'predicted_revenue' => 52850.95,
            'predicted_cogs' => 31710.57,
            'predicted_gross_profit' => 21140.38,
            'predicted_operating_expenses' => 9450.75,
            'predicted_net_profit' => 11689.63,
            'predicted_margin' => 22.11,
            'confidence_level' => 87.5,
            'risk_factors' => [
                'Seasonal demand fluctuation',
                'Supply chain uncertainties',
                'Market competition'
            ],
            'recommendations' => [
                'Optimize inventory levels for Q4',
                'Focus on high-margin products',
                'Review supplier contracts'
            ]
        ];
        
        // Monthly breakdown for charts
        $monthlyData = [
            ['month' => 'Jan', 'profit' => 6850, 'predicted' => 7200],
            ['month' => 'Feb', 'profit' => 7320, 'predicted' => 7680],
            ['month' => 'Mar', 'profit' => 8150, 'predicted' => 8550],
            ['month' => 'Apr', 'profit' => 7890, 'predicted' => 8290],
            ['month' => 'May', 'profit' => 8450, 'predicted' => 8870],
            ['month' => 'Jun', 'profit' => 8350, 'predicted' => 8750],
        ];
        
        return view('reports.profit-loss', compact(
            'currentPeriod', 
            'previousPeriod', 
            'aiPredictions', 
            'monthlyData', 
            'startDate', 
            'endDate'
        ));
    }
}
