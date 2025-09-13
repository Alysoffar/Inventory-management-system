<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard.
     */
    public function index()
    {
        // Get actual data from database
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();
        
        // Today's metrics - based on actual sales data
        $today = Carbon::today();
        $todayRevenue = Sale::whereDate('sale_date', $today)->sum('total_amount');
        $todaySalesCount = Sale::whereDate('sale_date', $today)->count();
        
        // Low stock products - items below minimum stock level
        $lowStockProducts = Product::where('stock_quantity', '<=', DB::raw('minimum_stock_level'))
            ->where('minimum_stock_level', '>', 0)
            ->get(['name', 'stock_quantity', 'category', 'minimum_stock_level']);
        
        $lowStockCount = $lowStockProducts->count();
        
        // Pending users count for admin
        $pendingUsersCount = User::where('status', 'pending')->count();
        
        // Recent sales - actual data from database
        $recentSales = Sale::with(['customer', 'saleItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($sale) {
                return (object)[
                    'id' => $sale->id,
                    'product' => (object)['name' => $sale->saleItems->first()?->product?->name ?? 'Multiple Items'],
                    'customer' => (object)['name' => $sale->customer?->name ?? 'Walk-in Customer'],
                    'total_amount' => $sale->total_amount,
                    'created_at' => $sale->created_at
                ];
            });
        
        // Recent purchases - actual data from database
        $recentPurchases = Purchase::with(['supplier', 'purchaseItems.product'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($purchase) {
                return (object)[
                    'id' => $purchase->id,
                    'product' => (object)['name' => $purchase->purchaseItems->first()?->product?->name ?? 'Multiple Items'],
                    'supplier' => (object)['name' => $purchase->supplier?->name ?? 'Unknown Supplier'],
                    'total_cost' => $purchase->total_amount,
                    'created_at' => $purchase->created_at
                ];
            });
        
        // Monthly revenue data for chart - last 6 months
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $revenue = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
                ->sum('total_amount');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M'),
                'revenue' => (float) $revenue
            ];
        }

        // Additional comprehensive stats
        $totalSales = Sale::sum('total_amount');
        $totalPurchases = Purchase::sum('total_amount');
        $totalProfit = $totalSales - $totalPurchases;
        
        // Top selling products
        $topProducts = Product::withSum('saleItems', 'quantity')
            ->withSum('saleItems', 'total_price')
            ->orderBy('sale_items_sum_total_price', 'desc')
            ->limit(5)
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->name,
                    'category' => $product->category,
                    'quantity_sold' => $product->sale_items_sum_quantity ?? 0,
                    'revenue' => $product->sale_items_sum_total_price ?? 0,
                    'stock' => $product->stock_quantity
                ];
            });

        // Recent customers
        $recentCustomers = Customer::orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($customer) {
                $totalSpent = Sale::where('customer_id', $customer->id)->sum('total_amount');
                $lastPurchase = Sale::where('customer_id', $customer->id)->latest()->first();
                
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'total_spent' => $totalSpent,
                    'last_purchase' => $lastPurchase?->created_at,
                    'orders_count' => Sale::where('customer_id', $customer->id)->count()
                ];
            });

        // Inventory overview
        $inventoryStats = [
            'total_value' => Product::selectRaw('SUM(price * stock_quantity) as total')->value('total') ?? 0,
            'low_stock_count' => $lowStockCount,
            'out_of_stock' => Product::where('stock_quantity', 0)->count(),
            'categories_count' => Product::distinct('category')->count('category')
        ];

        // Sales trends (last 7 days)
        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailySales = Sale::whereDate('sale_date', $date)->sum('total_amount');
            $salesTrend[] = [
                'date' => $date->format('M j'),
                'sales' => (float) $dailySales
            ];
        }
        
        return view('dashboard', compact(
            'totalProducts',
            'totalCustomers', 
            'totalSuppliers',
            'todayRevenue',
            'todaySalesCount',
            'lowStockCount',
            'lowStockProducts',
            'recentSales',
            'recentPurchases',
            'monthlyRevenue',
            'pendingUsersCount',
            'totalSales',
            'totalPurchases',
            'totalProfit',
            'topProducts',
            'recentCustomers',
            'inventoryStats',
            'salesTrend'
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
