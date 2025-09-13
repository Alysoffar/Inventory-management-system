<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real counts from database
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalSales = Sale::count();
        
        // Calculate real revenue and metrics
        $totalRevenue = Sale::sum('total_amount') ?? 0;
        $totalPurchases = Purchase::sum('total_amount') ?? 0;
        $currentMonthRevenue = Sale::whereMonth('sale_date', now()->month)
                                  ->whereYear('sale_date', now()->year)
                                  ->sum('total_amount') ?? 0;
        
        // Add today's revenue for the dashboard view
        $todayRevenue = Sale::whereDate('sale_date', now()->toDateString())
                           ->sum('total_amount') ?? 0;
        
        // Add today's sales count for dashboard view
        $todaySalesCount = Sale::whereDate('sale_date', now()->toDateString())
                              ->count();
        
        // Get low stock products as collection for dashboard view
        $lowStockProducts = Product::whereRaw('stock_quantity <= minimum_stock_level')
                                  ->where('minimum_stock_level', '>', 0)
                                  ->get();
        
        // Add lowStockCount for dashboard view compatibility
        $lowStockCount = $lowStockProducts->count();
        
        // Get recent sales with relationships - providing product info for view compatibility
        $recentSales = Sale::with(['customer', 'saleItems.product'])
                          ->orderBy('sale_date', 'desc')
                          ->limit(5)
                          ->get()
                          ->map(function($sale) {
                              // Create an object-like structure that matches what the view expects
                              $saleObject = new \stdClass();
                              $saleObject->id = $sale->id;
                              $saleObject->total_amount = $sale->total_amount;
                              $saleObject->created_at = $sale->created_at;
                              $saleObject->sale_date = $sale->sale_date;
                              
                              // Create customer object
                              $saleObject->customer = new \stdClass();
                              $saleObject->customer->name = $sale->customer?->name ?? 'Walk-in Customer';
                              
                              // Create product object (use first product from sale items)
                              $saleObject->product = new \stdClass();
                              $firstProduct = $sale->saleItems->first()?->product;
                              $saleObject->product->name = $firstProduct?->name ?? 'Multiple Items';
                              
                              return $saleObject;
                          });
        
        // Get recent purchases with relationships - providing product info for view compatibility
        $recentPurchases = Purchase::with(['supplier', 'purchaseItems.product'])
                                  ->orderBy('purchase_date', 'desc')
                                  ->limit(5)
                                  ->get()
                                  ->map(function($purchase) {
                                      // Create an object-like structure that matches what the view expects
                                      $purchaseObject = new \stdClass();
                                      $purchaseObject->id = $purchase->id;
                                      $purchaseObject->total_amount = $purchase->total_amount;
                                      $purchaseObject->total_cost = $purchase->total_amount; // View expects total_cost
                                      $purchaseObject->created_at = $purchase->created_at;
                                      $purchaseObject->purchase_date = $purchase->purchase_date;
                                      
                                      // Create supplier object
                                      $purchaseObject->supplier = new \stdClass();
                                      $purchaseObject->supplier->name = $purchase->supplier?->name ?? 'Direct Purchase';
                                      
                                      // Create product object (use first product from purchase items)
                                      $purchaseObject->product = new \stdClass();
                                      $firstProduct = $purchase->purchaseItems->first()?->product;
                                      $purchaseObject->product->name = $firstProduct?->name ?? 'Multiple Items';
                                      
                                      return $purchaseObject;
                                  });
        
        // Get top selling products based on actual sales - with correct array keys for view
        $topProducts = Product::select('products.*')
                              ->selectRaw('COALESCE(SUM(sale_items.quantity), 0) as total_sold')
                              ->selectRaw('COALESCE(SUM(sale_items.total_price), 0) as total_revenue')
                              ->leftJoin('sale_items', 'products.id', '=', 'sale_items.product_id')
                              ->groupBy('products.id', 'products.name', 'products.sku', 'products.price', 'products.stock_quantity', 'products.category', 'products.minimum_stock_level', 'products.cost_price', 'products.created_at', 'products.updated_at')
                              ->orderBy('total_sold', 'desc')
                              ->limit(5)
                              ->get()
                              ->map(function($product) {
                                  return [
                                      'name' => $product->name,
                                      'category' => $product->category,
                                      'quantity_sold' => $product->total_sold,
                                      'revenue' => $product->total_revenue,
                                      'stock_quantity' => $product->stock_quantity
                                  ];
                              });
        
        // Get customer insights - with correct array keys for view
        $topCustomers = Customer::select('customers.*')
                               ->selectRaw('COALESCE(SUM(sales.total_amount), 0) as total_spent')
                               ->selectRaw('COUNT(sales.id) as total_orders')
                               ->leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
                               ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone', 'customers.address', 'customers.created_at', 'customers.updated_at')
                               ->orderBy('total_spent', 'desc')
                               ->limit(5)
                               ->get()
                               ->map(function($customer) {
                                   return [
                                       'name' => $customer->name,
                                       'email' => $customer->email,
                                       'orders_count' => $customer->total_orders,
                                       'total_spent' => $customer->total_spent,
                                       'last_purchase' => null // We'll need to add this later if needed
                                   ];
                               });
        
        // Calculate monthly data for charts (last 6 months)
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthlySales = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])->sum('total_amount') ?? 0;
            $monthlyPurchases = Purchase::whereBetween('purchase_date', [$monthStart, $monthEnd])->sum('total_amount') ?? 0;
            
            $monthlyData[] = [
                'month' => $monthStart->format('M'),
                'sales' => $monthlySales,
                'purchases' => $monthlyPurchases,
                'profit' => $monthlySales - $monthlyPurchases
            ];
        }
        
        // Business performance metrics
        $averageOrderValue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        $profitMargin = $totalRevenue > 0 ? (($totalRevenue - $totalPurchases) / $totalRevenue) * 100 : 0;
        
        // Add missing variables for dashboard view compatibility
        $pendingUsersCount = 0; // No pending users functionality yet
        $recentCustomers = $topCustomers; // Use same data as topCustomers
        $totalProfit = $totalRevenue - $totalPurchases;
        
        // Create monthlyRevenue from monthlyData for chart compatibility
        $monthlyRevenue = array_map(function($month) {
            return [
                'month' => $month['month'],
                'revenue' => $month['sales']
            ];
        }, $monthlyData);
        
        // Calculate inventory stats
        $inventoryValue = Product::selectRaw('SUM(stock_quantity * cost_price) as total_value')->first();
        $inventoryStats = [
            'total_value' => $inventoryValue->total_value ?? 0
        ];
        
        // Stock alerts
        $stockAlerts = [
            'low_stock' => Product::whereRaw('stock_quantity <= minimum_stock_level')
                                 ->where('minimum_stock_level', '>', 0)
                                 ->get(['name', 'stock_quantity', 'minimum_stock_level']),
            'out_of_stock' => Product::where('stock_quantity', 0)->get(['name']),
            'overstocked' => Product::whereRaw('stock_quantity > minimum_stock_level * 3')
                                   ->where('minimum_stock_level', '>', 0)
                                   ->get(['name', 'stock_quantity'])
        ];
        
        return view('dashboard', compact(
            'totalProducts',
            'totalCustomers', 
            'totalSales',
            'totalRevenue',
            'totalPurchases',
            'totalProfit',
            'currentMonthRevenue',
            'todayRevenue',
            'todaySalesCount',
            'pendingUsersCount',
            'lowStockProducts',
            'lowStockCount',
            'recentSales',
            'recentPurchases',
            'topProducts',
            'topCustomers',
            'recentCustomers',
            'monthlyData',
            'monthlyRevenue',
            'inventoryStats',
            'averageOrderValue',
            'profitMargin',
            'stockAlerts'
        ));
    }
}