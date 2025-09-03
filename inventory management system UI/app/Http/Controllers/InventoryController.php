<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display inventory dashboard
     */
    public function dashboard()
    {
        // Get inventory statistics
        $stats = [
            'total_products' => Product::active()->count(),
            'low_stock_products' => Product::active()->whereRaw('stock_quantity <= minimum_stock_level')->count(),
            'out_of_stock_products' => Product::active()->where('stock_quantity', '<=', 0)->count(),
            'total_inventory_value' => Product::active()->selectRaw('SUM(stock_quantity * price) as total')->value('total') ?? 0,
            'auto_reorder_enabled' => Product::active()->where('auto_reorder', true)->count(),
        ];

        // Get recent inventory activities
        $recentActivities = InventoryLog::with('product')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get low stock products
        $lowStockProducts = Product::active()
            ->whereRaw('stock_quantity <= minimum_stock_level')
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Get unread notifications
        $notifications = Notification::unread()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get locations for map
        $locations = Product::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('location', 'latitude', 'longitude')
            ->distinct()
            ->get();

        return view('inventory.dashboard', compact(
            'stats', 
            'recentActivities', 
            'lowStockProducts', 
            'notifications',
            'locations'
        ));
    }

    /**
     * Display inventory map
     */
    public function map()
    {
        return view('inventory.map');
    }

    /**
     * Display inventory logs
     */
    public function logs(Request $request)
    {
        $query = InventoryLog::with('product');

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get filter options
        $products = Product::active()->select('id', 'name')->orderBy('name')->get();
        $types = InventoryLog::select('type')->distinct()->pluck('type');

        return view('inventory.logs', compact('logs', 'products', 'types'));
    }

    /**
     * Display notifications
     */
    public function notifications()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->paginate(20);

        return view('inventory.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        Notification::unread()->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Run inventory check manually
     */
    public function runInventoryCheck()
    {
        try {
            \App\Jobs\MonitorInventoryLevels::dispatch();
            
            return redirect()->back()
                ->with('success', 'Inventory check has been scheduled and will run shortly.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to schedule inventory check: ' . $e->getMessage());
        }
    }

    /**
     * Get inventory analytics data
     */
    public function analytics(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);

        // Stock movements over time
        $stockMovements = InventoryLog::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, type, SUM(ABS(quantity_changed)) as total_quantity')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        // Top products by movement
        $topProducts = InventoryLog::with('product')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('product_id, SUM(ABS(quantity_changed)) as total_movement')
            ->groupBy('product_id')
            ->orderBy('total_movement', 'desc')
            ->limit(10)
            ->get();

        // Low stock trend
        $lowStockTrend = DB::table('inventory_logs')
            ->join('products', 'inventory_logs.product_id', '=', 'products.id')
            ->where('inventory_logs.created_at', '>=', $startDate)
            ->selectRaw('DATE(inventory_logs.created_at) as date, 
                        COUNT(CASE WHEN inventory_logs.quantity_after <= products.minimum_stock_level THEN 1 END) as low_stock_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'stock_movements' => $stockMovements,
            'top_products' => $topProducts,
            'low_stock_trend' => $lowStockTrend,
        ]);
    }

    /**
     * Export inventory report
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $products = Product::active()->with('supplier')->get();

        if ($format === 'csv') {
            return $this->exportToCsv($products);
        }

        // Add support for other formats (PDF, Excel) here if needed
        return redirect()->back()->with('error', 'Unsupported export format.');
    }

    private function exportToCsv($products)
    {
        $filename = 'inventory_report_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'SKU', 'Name', 'Category', 'Current Stock', 'Minimum Level', 
                'Maximum Level', 'Location', 'Price', 'Cost Price', 'Total Value',
                'Status', 'Auto Reorder', 'Supplier', 'Last Restocked'
            ]);

            // CSV data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->sku,
                    $product->name,
                    $product->category,
                    $product->stock_quantity,
                    $product->minimum_stock_level,
                    $product->maximum_stock_level,
                    $product->location ?? '',
                    $product->price,
                    $product->cost_price ?? '',
                    $product->stock_quantity * $product->price,
                    $product->status,
                    $product->auto_reorder ? 'Yes' : 'No',
                    $product->supplier->name ?? '',
                    $product->last_restocked_at ? $product->last_restocked_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
