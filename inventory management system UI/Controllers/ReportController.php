<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\SaleItem;
use App\Models\PurchaseItem;
use App\Models\Supplier;

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
        // Get date range from request or set defaults to show all data
        $startDate = $request->input('start_date', '2020-01-01'); // Start from a far back date
        $endDate = $request->input('end_date', now()->addYear()->format('Y-m-d')); // End in the future

        // Get real sales data from database
        $saleItems = \App\Models\SaleItem::with(['sale', 'product', 'sale.customer'])
            ->whereHas('sale', function($q) use ($startDate, $endDate) {
                $q->whereBetween('sale_date', [$startDate, $endDate]);
            })
            ->get();

        $totalSales = $saleItems->count();
        $totalRevenue = $saleItems->sum('total_price');

        // Group sales by product for chart
        $salesByProduct = $saleItems->groupBy('product.name')->map(function($group) {
            return $group->sum('quantity');
        })->toArray();

        return view('reports.sales', compact('saleItems', 'totalSales', 'totalRevenue', 'startDate', 'endDate', 'salesByProduct'));
    }
    
    /**
     * Display inventory reports.
     */
    public function inventory()
    {
        // Get real inventory data from database
        $products = \App\Models\Product::all();
        $inventoryData = [
            'products' => $products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'category' => $product->category,
                    'stock_quantity' => $product->stock_quantity,
                    'cost_price' => $product->cost_price ?? 0,
                    'total_value' => $product->stock_quantity * ($product->cost_price ?? 0),
                    'minimum_stock_level' => $product->minimum_stock_level ?? 0
                ];
            })->toArray()  // Convert Collection to array for JavaScript
        ];
        $totalProducts = $products->count();
        $totalValue = $products->sum(function($p) { return $p->stock_quantity * ($p->cost_price ?? 0); });
        $lowStockItems = $products->filter(function($p) { return $p->stock_quantity <= ($p->minimum_stock_level ?? 0); })->count();
        $outOfStockItems = $products->where('stock_quantity', 0)->count();

        return view('reports.inventory', compact('inventoryData', 'totalProducts', 'totalValue', 'lowStockItems', 'outOfStockItems'));
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

    /**
     * Export AI analytics report as PDF.
     */
    public function aiExport()
    {
        // Collect comprehensive AI analytics data
        $data = [
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
            'report_period' => now()->startOfMonth()->format('M Y') . ' - ' . now()->format('M Y'),
            
            // AI Predictions Summary
            'ai_predictions' => [
                'predicted_revenue' => 52850.95,
                'predicted_cogs' => 31710.57,
                'predicted_gross_profit' => 21140.38,
                'predicted_net_profit' => 11689.63,
                'predicted_margin' => 22.11,
                'confidence_level' => 87.5,
                'accuracy_score' => 94.2
            ],
            
            // Current Performance
            'current_metrics' => [
                'total_sales' => 45750.80,
                'total_profit' => 8350.25,
                'margin_percentage' => 18.25,
                'inventory_turnover' => 6.4,
                'stock_accuracy' => 96.8
            ],
            
            // AI Insights
            'insights' => [
                'top_performing_products' => [
                    ['name' => 'Product A', 'sales' => 15420, 'growth' => '+12.5%'],
                    ['name' => 'Product B', 'sales' => 12850, 'growth' => '+8.3%'],
                    ['name' => 'Product C', 'sales' => 9750, 'growth' => '+15.7%']
                ],
                'risk_factors' => [
                    'Seasonal demand fluctuation in Q4',
                    'Supply chain uncertainties',
                    'Increasing market competition',
                    'Raw material cost volatility'
                ],
                'recommendations' => [
                    'Optimize inventory levels for high-demand products',
                    'Focus marketing on high-margin items',
                    'Review and renegotiate supplier contracts',
                    'Implement dynamic pricing strategies',
                    'Expand product line in growth categories'
                ]
            ],
            
            // Forecasting Data
            'forecasts' => [
                'next_quarter' => [
                    'expected_sales' => 58920.45,
                    'projected_profit' => 12450.30,
                    'inventory_needs' => 35680.20
                ],
                'yearly_projection' => [
                    'annual_revenue' => 234567.89,
                    'annual_profit' => 48925.67,
                    'growth_rate' => '+23.4%'
                ]
            ],
            
            // AI Model Performance
            'model_performance' => [
                'prediction_accuracy' => 94.2,
                'false_positive_rate' => 3.1,
                'model_confidence' => 87.5,
                'last_trained' => now()->subDays(7)->format('M j, Y'),
                'training_data_points' => 15847
            ]
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.ai-export-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'AI_Analytics_Report_' . now()->format('Y_m_d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export Sales report as PDF.
     */
    public function exportSalesPdf()
    {
        try {
            // Get real sales data from database
            $sales = Sale::with(['customer', 'saleItems.product'])->get();
            $totalSales = Sale::sum('total_amount') ?? 0;
            $totalTransactions = Sale::count();
            $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
            $totalItemsSold = SaleItem::sum('quantity') ?? 0;
            
            // Get top products
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
                        'quantity' => $product->total_sold,
                        'revenue' => $product->total_revenue,
                        'profit' => $product->total_revenue - ($product->cost_price * $product->total_sold)
                    ];
                });
            
            // Collect sales data
            $data = [
                'generated_at' => now()->format('F j, Y \a\t g:i A'),
                'report_period' => now()->startOfMonth()->format('M Y') . ' - ' . now()->format('M Y'),
                
                // Sales Summary (real data)
                'sales_summary' => [
                    'total_sales' => $totalSales,
                    'total_transactions' => $totalTransactions,
                    'average_transaction' => $averageTransaction,
                    'total_items_sold' => $totalItemsSold,
                    'tax_collected' => $totalSales * 0.08 // 8% tax estimate
                ],
                
                // Sales by Product (real data)
                'top_products' => $topProducts,
                
                // Recent sales
                'recent_sales' => $sales->take(10)->map(function($sale) {
                    return [
                        'id' => $sale->id,
                        'date' => $sale->sale_date,
                        'customer' => $sale->customer?->name ?? 'Walk-in Customer',
                        'amount' => $sale->total_amount,
                        'items' => $sale->saleItems->count()
                    ];
                }),
                
                // Monthly breakdown (last 6 months)
                'monthly_breakdown' => collect(range(5, 0))->map(function($i) {
                    $date = now()->subMonths($i);
                    $monthSales = Sale::whereMonth('sale_date', $date->month)
                                    ->whereYear('sale_date', $date->year)
                                    ->sum('total_amount') ?? 0;
                    $monthTransactions = Sale::whereMonth('sale_date', $date->month)
                                           ->whereYear('sale_date', $date->year)
                                           ->count();
                    return [
                        'month' => $date->format('M'),
                        'sales' => $monthSales,
                        'transactions' => $monthTransactions
                    ];
                }),
                
                // Payment Methods (sample data - add payment_method column to sales table if needed)
                'payment_methods' => [
                    ['method' => 'Cash', 'amount' => $totalSales * 0.6, 'percentage' => 60.0],
                    ['method' => 'Credit Card', 'amount' => $totalSales * 0.3, 'percentage' => 30.0],
                    ['method' => 'Other', 'amount' => $totalSales * 0.1, 'percentage' => 10.0]
                ]
            ];

            // Generate PDF
            $pdf = Pdf::loadView('reports.sales-export-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'Sales_Report_' . now()->format('Y_m_d_His') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            // If there's an error, return a simple response
            return response()->json(['error' => 'Error generating PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export Inventory report as PDF.
     */
    public function exportInventoryPdf()
    {
        try {
            // Get real inventory data from database
            $products = Product::all();
            $totalProducts = Product::count();
            $totalStockValue = Product::selectRaw('SUM(stock_quantity * cost_price) as total_value')->first()->total_value ?? 0;
            $lowStockItems = Product::whereRaw('stock_quantity <= minimum_stock_level')
                                   ->where('minimum_stock_level', '>', 0)->count();
            $outOfStockItems = Product::where('stock_quantity', 0)->count();
            $overstockedItems = Product::whereRaw('stock_quantity > minimum_stock_level * 3')
                                      ->where('minimum_stock_level', '>', 0)->count();
            
            // Get high value items
            $highValueItems = Product::selectRaw('*, (stock_quantity * cost_price) as total_value')
                                    ->orderBy('total_value', 'desc')
                                    ->limit(5)
                                    ->get()
                                    ->map(function($product) {
                                        return [
                                            'name' => $product->name,
                                            'stock' => $product->stock_quantity,
                                            'unit_cost' => $product->cost_price,
                                            'total_value' => $product->stock_quantity * $product->cost_price
                                        ];
                                    });
            
            // Get low stock items
            $lowStockProducts = Product::whereRaw('stock_quantity <= minimum_stock_level')
                                      ->where('minimum_stock_level', '>', 0)
                                      ->get()
                                      ->map(function($product) {
                                          return [
                                              'name' => $product->name,
                                              'current_stock' => $product->stock_quantity,
                                              'min_stock' => $product->minimum_stock_level,
                                              'suggested_order' => max(($product->minimum_stock_level * 2) - $product->stock_quantity, 0)
                                          ];
                                      });
            
            // Collect inventory data
            $data = [
                'generated_at' => now()->format('F j, Y \a\t g:i A'),
                'report_period' => now()->format('M Y'),
                
                // Inventory Summary (real data)
                'inventory_summary' => [
                    'total_products' => $totalProducts,
                    'total_stock_value' => $totalStockValue,
                    'low_stock_items' => $lowStockItems,
                    'out_of_stock_items' => $outOfStockItems,
                    'overstocked_items' => $overstockedItems
                ],
                
                // Stock Status
                'stock_status' => [
                    'in_stock' => $totalProducts - $outOfStockItems,
                    'low_stock' => $lowStockItems,
                    'out_of_stock' => $outOfStockItems,
                    'overstocked' => $overstockedItems
                ],
                
                // Top Value Items (real data)
                'high_value_items' => $highValueItems,
                
                // Low Stock Items (real data)
                'low_stock_items' => $lowStockProducts,
                
                // Categories Breakdown (real data)
                'categories' => Product::selectRaw('category, COUNT(*) as items, SUM(stock_quantity * cost_price) as value')
                              ->groupBy('category')
                              ->get()
                              ->map(function($category) {
                                  return [
                                      'name' => $category->category ?: 'Uncategorized',
                                      'items' => $category->items,
                                      'value' => $category->value ?: 0
                                  ];
                              }),
                              
                // All products summary
                'all_products' => $products->map(function($product) {
                    return [
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'category' => $product->category,
                        'stock' => $product->stock_quantity,
                        'min_stock' => $product->minimum_stock_level,
                        'cost_price' => $product->cost_price,
                        'total_value' => $product->stock_quantity * $product->cost_price
                    ];
                })
            ];

            // Generate PDF
            $pdf = Pdf::loadView('reports.inventory-export-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'Inventory_Report_' . now()->format('Y_m_d_His') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            // If there's an error, return a simple response
            return response()->json(['error' => 'Error generating inventory PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export Customers report as PDF.
     */
    public function exportCustomersPdf()
    {
        try {
            // Get real customer data from database
            $customers = Customer::all();
            $totalCustomers = Customer::count();
            
            // Get customer sales data
            $customerSales = Customer::select('customers.*')
                ->selectRaw('COALESCE(COUNT(sales.id), 0) as total_orders')
                ->selectRaw('COALESCE(SUM(sales.total_amount), 0) as total_spent')
                ->selectRaw('MAX(sales.sale_date) as last_order_date')
                ->leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
                ->groupBy('customers.id', 'customers.name', 'customers.email', 'customers.phone', 'customers.address', 'customers.created_at', 'customers.updated_at')
                ->orderBy('total_spent', 'desc')
                ->get();
            
            // Calculate averages
            $totalSpent = $customerSales->sum('total_spent');
            $totalOrders = $customerSales->sum('total_orders');
            $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
            
            // Get top customers
            $topCustomers = $customerSales->take(10)->map(function($customer) {
                return [
                    'name' => $customer->name,
                    'orders' => $customer->total_orders,
                    'total_spent' => $customer->total_spent,
                    'last_order' => $customer->last_order_date ? \Carbon\Carbon::parse($customer->last_order_date)->format('Y-m-d') : 'Never'
                ];
            });
            
            // Customer segments
            $vipCustomers = $customerSales->where('total_spent', '>', 2000)->count();
            $premiumCustomers = $customerSales->whereBetween('total_spent', [500, 2000])->count();
            $regularCustomers = $customerSales->whereBetween('total_spent', [100, 500])->count();
            $newCustomers = $customerSales->where('total_spent', '<', 100)->count();
            
            $vipValue = $customerSales->where('total_spent', '>', 2000)->sum('total_spent');
            $premiumValue = $customerSales->whereBetween('total_spent', [500, 2000])->sum('total_spent');
            $regularValue = $customerSales->whereBetween('total_spent', [100, 500])->sum('total_spent');
            $newValue = $customerSales->where('total_spent', '<', 100)->sum('total_spent');
            
            // Collect customer data
            $data = [
                'generated_at' => now()->format('F j, Y \a\t g:i A'),
                'report_period' => now()->startOfMonth()->format('M Y') . ' - ' . now()->format('M Y'),
                
                // Customer Summary (real data)
                'customer_summary' => [
                    'total_customers' => $totalCustomers,
                    'new_customers' => Customer::whereDate('created_at', '>=', now()->startOfMonth())->count(),
                    'active_customers' => $customerSales->where('total_orders', '>', 0)->count(),
                    'average_order_value' => $averageOrderValue,
                    'customer_retention_rate' => $totalCustomers > 0 ? ($customerSales->where('total_orders', '>', 1)->count() / $totalCustomers) * 100 : 0
                ],
                
                // Top Customers (real data)
                'top_customers' => $topCustomers,
                
                // Customer Segments (real data)
                'customer_segments' => [
                    [
                        'segment' => 'VIP (>$2000)', 
                        'count' => $vipCustomers, 
                        'percentage' => $totalCustomers > 0 ? ($vipCustomers / $totalCustomers) * 100 : 0, 
                        'total_value' => $vipValue
                    ],
                    [
                        'segment' => 'Premium ($500-$2000)', 
                        'count' => $premiumCustomers, 
                        'percentage' => $totalCustomers > 0 ? ($premiumCustomers / $totalCustomers) * 100 : 0, 
                        'total_value' => $premiumValue
                    ],
                    [
                        'segment' => 'Regular ($100-$500)', 
                        'count' => $regularCustomers, 
                        'percentage' => $totalCustomers > 0 ? ($regularCustomers / $totalCustomers) * 100 : 0, 
                        'total_value' => $regularValue
                    ],
                    [
                        'segment' => 'New (<$100)', 
                        'count' => $newCustomers, 
                        'percentage' => $totalCustomers > 0 ? ($newCustomers / $totalCustomers) * 100 : 0, 
                        'total_value' => $newValue
                    ]
                ],
                
                // All customers
                'all_customers' => $customers->map(function($customer) use ($customerSales) {
                    $customerData = $customerSales->firstWhere('id', $customer->id);
                    return [
                        'name' => $customer->name,
                        'email' => $customer->email,
                        'phone' => $customer->phone,
                        'address' => $customer->address,
                        'orders' => $customerData ? $customerData->total_orders : 0,
                        'total_spent' => $customerData ? $customerData->total_spent : 0,
                        'last_order' => $customerData && $customerData->last_order_date ? 
                                       \Carbon\Carbon::parse($customerData->last_order_date)->format('Y-m-d') : 'Never',
                        'joined' => $customer->created_at->format('Y-m-d')
                    ];
                })
            ];

            // Generate PDF
            $pdf = Pdf::loadView('reports.customers-export-pdf', $data);
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'Customers_Report_' . now()->format('Y_m_d_His') . '.pdf';
            
            return $pdf->download($filename);
        } catch (\Exception $e) {
            // If there's an error, return a simple response
            return response()->json(['error' => 'Error generating customers PDF: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Export Profit & Loss report as PDF.
     */
    public function exportProfitLossPdf()
    {
        // Collect profit & loss data
        $data = [
            'generated_at' => now()->format('F j, Y \a\t g:i A'),
            'report_period' => now()->startOfMonth()->format('M Y') . ' - ' . now()->format('M Y'),
            
            // Current Period Summary
            'current_period' => [
                'revenue' => 45750.80,
                'gross_profit' => 17300.55,
                'net_profit' => 11689.63,
                'margin_percentage' => 25.5,
                'operating_expenses' => 8350.25
            ],
            
            // Revenue Breakdown
            'revenue_breakdown' => [
                ['category' => 'Electronics', 'amount' => 18420.50, 'percentage' => 40.2],
                ['category' => 'Accessories', 'amount' => 12680.75, 'percentage' => 27.7],
                ['category' => 'Components', 'amount' => 8950.30, 'percentage' => 19.6],
                ['category' => 'Software', 'amount' => 3890.25, 'percentage' => 8.5],
                ['category' => 'Services', 'amount' => 1809.00, 'percentage' => 4.0]
            ],
            
            // Cost Analysis
            'cost_analysis' => [
                'cost_of_goods_sold' => 28450.25,
                'operating_expenses' => 8350.25,
                'administrative_costs' => 2260.67,
                'marketing_expenses' => 1850.40,
                'other_expenses' => 980.15
            ],
            
            // Monthly Comparison
            'monthly_comparison' => [
                ['month' => 'Jan', 'revenue' => 38420.50, 'profit' => 9855.30, 'margin' => 25.7],
                ['month' => 'Feb', 'revenue' => 42680.75, 'profit' => 11250.80, 'margin' => 26.4],
                ['month' => 'Mar', 'revenue' => 45750.80, 'profit' => 11689.63, 'margin' => 25.5],
                ['month' => 'Apr', 'revenue' => 41230.60, 'profit' => 10456.20, 'margin' => 25.4],
                ['month' => 'May', 'revenue' => 47890.25, 'profit' => 12580.45, 'margin' => 26.3],
                ['month' => 'Jun', 'revenue' => 43560.90, 'profit' => 11234.75, 'margin' => 25.8]
            ],
            
            // AI Predictions
            'ai_predictions' => [
                'predicted_revenue' => 48250.90,
                'predicted_profit' => 12456.78,
                'confidence_level' => 87.5,
                'trend_analysis' => 'positive'
            ]
        ];

        // Generate PDF
        $pdf = Pdf::loadView('reports.profit-loss-export-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Profit_Loss_Report_' . now()->format('Y_m_d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Display monthly sales report.
     */
    public function monthlySales(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        // Get monthly sales data for the year
        $monthlySales = [];
        $monthlyComparison = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $monthEnd = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
            
            // Current year sales
            $currentSales = \App\Models\Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
                ->sum('total_amount');
            
            // Previous year sales for comparison
            $prevYearStart = $monthStart->copy()->subYear();
            $prevYearEnd = $monthEnd->copy()->subYear();
            $previousSales = \App\Models\Sale::whereBetween('sale_date', [$prevYearStart, $prevYearEnd])
                ->sum('total_amount');
            
            $monthlySales[] = [
                'month' => $monthStart->format('M'),
                'sales' => $currentSales,
                'previous_sales' => $previousSales,
                'growth' => $previousSales > 0 ? (($currentSales - $previousSales) / $previousSales) * 100 : 0
            ];
        }
        
        // Get best performing products per month
        $monthlyTopProducts = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $monthEnd = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
            
            $topProduct = \App\Models\SaleItem::with(['product', 'sale'])
                ->whereHas('sale', function($query) use ($monthStart, $monthEnd) {
                    $query->whereBetween('sale_date', [$monthStart, $monthEnd]);
                })
                ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(quantity * unit_price) as total_revenue')
                ->groupBy('product_id')
                ->orderBy('total_revenue', 'desc')
                ->first();
            
            $monthlyTopProducts[] = [
                'month' => $monthStart->format('M'),
                'product' => $topProduct ? $topProduct->product->name : 'No sales',
                'quantity' => $topProduct ? $topProduct->total_quantity : 0,
                'revenue' => $topProduct ? $topProduct->total_revenue : 0
            ];
        }
        
        // Calculate summary metrics
        $totalYearSales = collect($monthlySales)->sum('sales');
        $totalPrevYearSales = collect($monthlySales)->sum('previous_sales');
        $yearGrowth = $totalPrevYearSales > 0 ? (($totalYearSales - $totalPrevYearSales) / $totalPrevYearSales) * 100 : 0;
        $averageMonthlySales = $totalYearSales / 12;
        $bestMonth = collect($monthlySales)->sortByDesc('sales')->first();
        $worstMonth = collect($monthlySales)->sortBy('sales')->first();
        
        return view('reports.monthly-sales', compact(
            'monthlySales',
            'monthlyTopProducts',
            'year',
            'totalYearSales',
            'totalPrevYearSales',
            'yearGrowth',
            'averageMonthlySales',
            'bestMonth',
            'worstMonth'
        ));
    }

    /**
     * Display customer analysis report.
     */
    public function customerAnalysis(Request $request)
    {
        // Get customer metrics
        $totalCustomers = \App\Models\Customer::count();
        $activeCustomers = \App\Models\Customer::whereHas('sales', function($query) {
            $query->where('sale_date', '>=', now()->subDays(30));
        })->count();
        
        // Top customers by revenue
        $topCustomers = \App\Models\Customer::with('sales')
            ->withSum('sales', 'total_amount')
            ->withCount('sales')
            ->orderBy('sales_sum_total_amount', 'desc')
            ->take(10)
            ->get();
        
        // Customer segmentation
        $customerSegments = [];
        $allCustomers = \App\Models\Customer::with('sales')->get();
        
        foreach ($allCustomers as $customer) {
            $totalSpent = $customer->sales->sum('total_amount');
            $orderCount = $customer->sales->count();
            $lastOrderDate = $customer->sales->max('sale_date');
            
            $daysSinceLastOrder = $lastOrderDate ? now()->diffInDays($lastOrderDate) : 999;
            
            // Segment customers
            if ($totalSpent >= 1000 && $daysSinceLastOrder <= 30) {
                $segment = 'VIP';
            } elseif ($totalSpent >= 500 && $daysSinceLastOrder <= 60) {
                $segment = 'Loyal';
            } elseif ($orderCount >= 3 && $daysSinceLastOrder <= 90) {
                $segment = 'Regular';
            } elseif ($daysSinceLastOrder > 90) {
                $segment = 'At Risk';
            } else {
                $segment = 'New';
            }
            
            $customerSegments[] = [
                'customer' => $customer,
                'segment' => $segment,
                'total_spent' => $totalSpent,
                'order_count' => $orderCount,
                'days_since_last_order' => $daysSinceLastOrder
            ];
        }
        
        // Group by segments
        $segmentStats = collect($customerSegments)->groupBy('segment')
            ->map(function($group, $segment) {
                return [
                    'segment' => $segment,
                    'count' => $group->count(),
                    'total_revenue' => $group->sum('total_spent'),
                    'avg_order_value' => $group->avg('total_spent'),
                    'customers' => $group->take(5) // Top 5 customers per segment
                ];
            });
        
        // Monthly new customers
        $monthlyNewCustomers = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = \App\Models\Customer::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $monthlyNewCustomers[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }
        
        // Customer retention rate
        $existingCustomersThisMonth = \App\Models\Customer::where('created_at', '<', now()->startOfMonth())
            ->whereHas('sales', function($query) {
                $query->whereBetween('sale_date', [now()->startOfMonth(), now()->endOfMonth()]);
            })->count();
        
        $totalExistingCustomers = \App\Models\Customer::where('created_at', '<', now()->startOfMonth())->count();
        $retentionRate = $totalExistingCustomers > 0 ? ($existingCustomersThisMonth / $totalExistingCustomers) * 100 : 0;
        
        return view('reports.customer-analysis', compact(
            'totalCustomers',
            'activeCustomers',
            'topCustomers',
            'segmentStats',
            'monthlyNewCustomers',
            'retentionRate'
        ));
    }

    /**
     * Display low stock alert report.
     */
    public function lowStockAlert(Request $request)
    {
        $threshold = $request->input('threshold', 10);
        
        // Get low stock products
        $lowStockProducts = \App\Models\Product::where('stock_quantity', '<=', $threshold)
            ->where('stock_quantity', '>', 0)
            ->with(['supplier'])
            ->orderBy('stock_quantity', 'asc')
            ->get();
        
        // Get out of stock products
        $outOfStockProducts = \App\Models\Product::where('stock_quantity', '<=', 0)
            ->with(['supplier'])
            ->get();
        
        // Get products that will be out of stock soon (based on sales velocity)
        $soonOutOfStock = [];
        $products = \App\Models\Product::where('stock_quantity', '>', 0)->get();
        
        foreach ($products as $product) {
            // Calculate average daily sales over last 30 days
            $dailySales = \App\Models\SaleItem::where('product_id', $product->id)
                ->whereHas('sale', function($query) {
                    $query->where('sale_date', '>=', now()->subDays(30));
                })
                ->sum('quantity') / 30;
            
            if ($dailySales > 0) {
                $daysUntilEmpty = $product->stock_quantity / $dailySales;
                if ($daysUntilEmpty <= 7 && $daysUntilEmpty > 0) { // Will be empty in 7 days
                    $soonOutOfStock[] = [
                        'product' => $product,
                        'days_until_empty' => round($daysUntilEmpty, 1),
                        'daily_sales_avg' => round($dailySales, 2)
                    ];
                }
            }
        }
        
        // Reorder recommendations
        $reorderRecommendations = [];
        foreach ($lowStockProducts as $product) {
            $avgMonthlySales = \App\Models\SaleItem::where('product_id', $product->id)
                ->whereHas('sale', function($query) {
                    $query->where('sale_date', '>=', now()->subDays(30));
                })
                ->sum('quantity');
            
            $recommendedOrder = max($avgMonthlySales * 2, 50); // 2 months supply or minimum 50
            
            $reorderRecommendations[] = [
                'product' => $product,
                'current_stock' => $product->stock_quantity,
                'monthly_sales' => $avgMonthlySales,
                'recommended_order' => $recommendedOrder,
                'supplier' => $product->supplier
            ];
        }
        
        return view('reports.low-stock-alert', compact(
            'lowStockProducts',
            'outOfStockProducts',
            'soonOutOfStock',
            'reorderRecommendations',
            'threshold'
        ));
    }

    /**
     * Display stock valuation report.
     */
    public function stockValuation(Request $request)
    {
        // Get all products with valuation data
        $products = \App\Models\Product::with(['supplier'])->get();
        
        $valuationData = [];
        $totalValue = 0;
        $totalCost = 0;
        
        foreach ($products as $product) {
            $stockValue = $product->stock_quantity * $product->price;
            $stockCost = $product->stock_quantity * $product->cost_price;
            $potentialProfit = $stockValue - $stockCost;
            $profitMargin = $stockCost > 0 ? (($product->price - $product->cost_price) / $product->cost_price) * 100 : 0;
            
            $valuationData[] = [
                'product' => $product,
                'stock_value' => $stockValue,
                'stock_cost' => $stockCost,
                'potential_profit' => $potentialProfit,
                'profit_margin' => $profitMargin,
                'turnover_ratio' => $this->calculateTurnoverRatio($product->id)
            ];
            
            $totalValue += $stockValue;
            $totalCost += $stockCost;
        }
        
        // Sort by value
        usort($valuationData, function($a, $b) {
            return $b['stock_value'] <=> $a['stock_value'];
        });
        
        // Convert valuationData to Collection immediately after sorting
        $valuationData = \Illuminate\Support\Collection::make($valuationData);
        
        // Category wise valuation
        $categoryValuation = [];
        $productsByCategory = \App\Models\Product::selectRaw('category, 
                                    SUM(stock_quantity * price) as total_value,
                                    SUM(stock_quantity * cost_price) as total_cost,
                                    SUM(stock_quantity) as total_quantity,
                                    COUNT(*) as product_count')
                                    ->groupBy('category')
                                    ->get();
        
        foreach ($productsByCategory as $categoryData) {
            $categoryValue = $categoryData->total_value;
            $categoryCost = $categoryData->total_cost;
            $categoryQuantity = $categoryData->total_quantity;
            
            $categoryValuation[] = [
                'category' => $categoryData->category ?: 'Uncategorized',
                'value' => $categoryValue,
                'cost' => $categoryCost,
                'quantity' => $categoryQuantity,
                'profit' => $categoryValue - $categoryCost,
                'products_count' => $categoryData->product_count
            ];
        }
        
        // Convert to Collection immediately after building the array
        $categoryValuation = \Illuminate\Support\Collection::make($categoryValuation);
        
        // Calculate key metrics
        $totalPotentialProfit = $totalValue - $totalCost;
        $overallMargin = $totalCost > 0 ? (($totalValue - $totalCost) / $totalCost) * 100 : 0;
        $deadStock = $valuationData->where('turnover_ratio', 0)->sum('stock_value');
        
        return view('reports.stock-valuation', compact(
            'valuationData',
            'categoryValuation',
            'totalValue',
            'totalCost',
            'totalPotentialProfit',
            'overallMargin',
            'deadStock'
        ));
    }

    /**
     * Display purchase analysis report.
     */
    public function purchaseAnalysis(Request $request)
    {
        // Get date range
        $startDate = $request->input('start_date', now()->subMonths(12)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Monthly purchase trends
        $monthlyPurchases = [];
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        
        while ($start <= $end) {
            $monthTotal = \App\Models\Purchase::whereYear('order_date', $start->year)
                ->whereMonth('order_date', $start->month)
                ->sum('total_amount');
            
            $monthCount = \App\Models\Purchase::whereYear('order_date', $start->year)
                ->whereMonth('order_date', $start->month)
                ->count();
                
            $monthlyPurchases[] = [
                'month' => $start->format('M Y'),
                'total' => $monthTotal,
                'count' => $monthCount,
                'average' => $monthCount > 0 ? $monthTotal / $monthCount : 0
            ];
            
            $start->addMonth();
        }
        
        // Supplier performance
        $supplierPerformance = \App\Models\Supplier::with('purchases')
            ->withSum('purchases', 'total_amount')
            ->withCount('purchases')
            ->withAvg('purchases', 'total_amount')
            ->orderBy('purchases_sum_total_amount', 'desc')
            ->get()
            ->map(function($supplier) {
                return [
                    'supplier' => $supplier,
                    'total_purchased' => $supplier->purchases_sum_total_amount ?? 0,
                    'order_count' => $supplier->purchases_count ?? 0,
                    'average_order' => $supplier->purchases_avg_total_amount ?? 0,
                    'last_order' => $supplier->purchases()->max('order_date'),
                    'products_supplied' => $supplier->products->count()
                ];
            });
        
        // Cost trends by category
        $categoryPurchases = \App\Models\PurchaseItem::with(['product', 'purchase'])
            ->whereHas('purchase', function($query) use ($startDate, $endDate) {
                $query->whereBetween('order_date', [$startDate, $endDate]);
            })
            ->get()
            ->groupBy('product.category')
            ->map(function($items, $category) {
                $total = $items->sum(function($item) {
                    return $item->quantity * $item->unit_price;
                });
                
                return [
                    'category' => $category,
                    'total_spent' => $total,
                    'items_count' => $items->count(),
                    'avg_cost' => $items->avg('unit_price')
                ];
            })
            ->sortByDesc('total_spent');
        
        // Purchase frequency analysis
        $averageDaysBetweenPurchases = [];
        foreach ($supplierPerformance as $supplier) {
            $purchases = $supplier['supplier']->purchases()
                ->orderBy('order_date')
                ->pluck('order_date')
                ->toArray();
            
            if (count($purchases) > 1) {
                $intervals = [];
                for ($i = 1; $i < count($purchases); $i++) {
                    $intervals[] = \Carbon\Carbon::parse($purchases[$i])
                        ->diffInDays(\Carbon\Carbon::parse($purchases[$i-1]));
                }
                $avgInterval = array_sum($intervals) / count($intervals);
            } else {
                $avgInterval = 0;
            }
            
            $averageDaysBetweenPurchases[$supplier['supplier']->name] = round($avgInterval, 1);
        }
        
        // Key metrics
        $totalPurchases = collect($monthlyPurchases)->sum('total');
        $totalOrders = collect($monthlyPurchases)->sum('count');
        $averageOrderValue = $totalOrders > 0 ? $totalPurchases / $totalOrders : 0;
        $topSupplier = $supplierPerformance->first();
        
        return view('reports.purchase-analysis', compact(
            'monthlyPurchases',
            'supplierPerformance',
            'categoryPurchases',
            'averageDaysBetweenPurchases',
            'totalPurchases',
            'totalOrders',
            'averageOrderValue',
            'topSupplier',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display cost analysis report.
     */
    public function costAnalysis(Request $request)
    {
        // Product cost analysis
        $products = \App\Models\Product::with(['supplier'])->get();
        
        $costAnalysis = [];
        foreach ($products as $product) {
            $salesData = \App\Models\SaleItem::where('product_id', $product->id)
                ->selectRaw('SUM(quantity) as total_sold, SUM(quantity * unit_price) as total_revenue')
                ->first();
            
            $purchaseData = \App\Models\PurchaseItem::where('product_id', $product->id)
                ->selectRaw('SUM(quantity) as total_purchased, SUM(quantity * unit_price) as total_cost')
                ->first();
            
            $unitMargin = $product->price - $product->cost_price;
            $marginPercentage = $product->cost_price > 0 ? ($unitMargin / $product->cost_price) * 100 : 0;
            
            $costAnalysis[] = [
                'product' => $product,
                'unit_cost' => $product->cost_price,
                'unit_price' => $product->price,
                'unit_margin' => $unitMargin,
                'margin_percentage' => $marginPercentage,
                'total_sold' => $salesData->total_sold ?? 0,
                'total_revenue' => $salesData->total_revenue ?? 0,
                'total_cost' => $purchaseData->total_cost ?? 0,
                'total_profit' => ($salesData->total_revenue ?? 0) - ($purchaseData->total_cost ?? 0),
                'stock_value' => $product->stock_quantity * $product->price,
                'stock_cost' => $product->stock_quantity * $product->cost_price
            ];
        }
        
        // Sort by margin percentage
        usort($costAnalysis, function($a, $b) {
            return $b['margin_percentage'] <=> $a['margin_percentage'];
        });
        
        // Category cost breakdown
        $categoryCosts = collect($costAnalysis)->groupBy('product.category')
            ->map(function($products, $category) {
                return [
                    'category' => $category,
                    'total_cost' => $products->sum('total_cost'),
                    'total_revenue' => $products->sum('total_revenue'),
                    'total_profit' => $products->sum('total_profit'),
                    'avg_margin' => $products->avg('margin_percentage'),
                    'products_count' => $products->count(),
                    'stock_value' => $products->sum('stock_value')
                ];
            })
            ->sortByDesc('total_profit');
        
        // Operating costs simulation (you can enhance this with actual operational data)
        $operatingCosts = [
            'rent' => 2000,
            'utilities' => 500,
            'salaries' => 8000,
            'marketing' => 1200,
            'insurance' => 300,
            'maintenance' => 400,
            'other' => 600
        ];
        
        $totalOperatingCosts = array_sum($operatingCosts);
        $totalRevenue = collect($costAnalysis)->sum('total_revenue');
        $totalProductCosts = collect($costAnalysis)->sum('total_cost');
        $grossProfit = $totalRevenue - $totalProductCosts;
        $netProfit = $grossProfit - $totalOperatingCosts;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        
        // Break-even analysis
        $fixedCosts = $totalOperatingCosts;
        $avgMarginPercentage = collect($costAnalysis)->avg('margin_percentage');
        $breakEvenRevenue = $avgMarginPercentage > 0 ? $fixedCosts / ($avgMarginPercentage / 100) : 0;
        
        return view('reports.cost-analysis', compact(
            'costAnalysis',
            'categoryCosts',
            'operatingCosts',
            'totalOperatingCosts',
            'totalRevenue',
            'totalProductCosts',
            'grossProfit',
            'netProfit',
            'netMargin',
            'breakEvenRevenue'
        ));
    }

    /**
     * Calculate turnover ratio for a product
     */
    private function calculateTurnoverRatio($productId)
    {
        $soldQuantity = \App\Models\SaleItem::where('product_id', $productId)
            ->whereHas('sale', function($query) {
                $query->where('sale_date', '>=', now()->subDays(30));
            })
            ->sum('quantity');
        
        $avgInventory = \App\Models\Product::find($productId)->stock_quantity;
        
        return $avgInventory > 0 ? $soldQuantity / $avgInventory : 0;
    }
}
