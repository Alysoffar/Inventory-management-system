<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalProducts = Product::count();
        $totalCustomers = Customer::count();
        $totalSuppliers = Supplier::count();

        // Today's sales
        $todaySales = Sale::whereDate('created_at', Carbon::today())->get();
        $todayRevenue = $todaySales->sum('total_amount');
        $todaySalesCount = $todaySales->count();

        // Low stock products (quantity less than 10)
        $lowStockProducts = Product::where('quantity', '<', 10)->get();

        // Recent sales (last 5)
        $recentSales = Sale::with(['product', 'customer'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent purchases (last 5)
        $recentPurchases = Purchase::with(['product', 'supplier'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Monthly revenue data for chart
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Sale::whereYear('created_at', $month->year)
                          ->whereMonth('created_at', $month->month)
                          ->sum('total_amount');
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

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
}
