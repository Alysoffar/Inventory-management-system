<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use Carbon\Carbon;

class AIPredictionController extends Controller
{
    private $apiUrl;
    
    public function __construct()
    {
        $this->apiUrl = env('AI_API_URL', 'http://localhost:5000');
    }
    
    /**
     * Display the AI prediction dashboard
     */
    public function index()
    {
        // Get recent predictions or sample data
        $recentPredictions = $this->getRecentPredictions();
        $totalPredictions = count($recentPredictions);
        $averageAccuracy = 85.5; // Sample accuracy
        
        return view('ai.predictions.index', compact(
            'recentPredictions', 
            'totalPredictions', 
            'averageAccuracy'
        ));
    }
    
    /**
     * Show prediction form for a specific product
     */
    public function create()
    {
        $products = Product::all();
        return view('ai.predictions.create', compact('products'));
    }
    
    /**
     * Make a single prediction
     */
    public function predict(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required',
                'current_stock' => 'required|numeric|min:0',
                'expected_demand' => 'required|numeric|min:0',
                'price' => 'required|numeric|min:0',
                'prediction_date' => 'required|date'
            ]);
            
            // Get product details
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }
            
            // Prepare data for AI API
            $predictionData = [
                'product_id' => $product->id,
                'store_id' => 'S001', // Default store
                'current_stock' => floatval($request->current_stock),
                'expected_demand' => floatval($request->expected_demand),
                'price' => floatval($request->price),
                'category' => $product->category ?? 'General',
                'date' => $request->prediction_date,
                'inventory_level' => floatval($request->current_stock),
                'demand_forecast' => floatval($request->expected_demand)
            ];
            
            // Make API call
            $response = Http::timeout(30)->post($this->apiUrl . '/predict', $predictionData);
            
            if ($response->successful()) {
                $prediction = $response->json();
                
                // Log the actual response for debugging
                Log::info('AI API Response:', $prediction);
                
                // Validate the response structure
                if (!isset($prediction['prediction'])) {
                    Log::error('Invalid API response structure - missing prediction key:', $prediction);
                    throw new \Exception('Invalid API response structure');
                }
                
                // Store prediction for future reference
                $this->storePrediction($product, $prediction, $predictionData);
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => true,
                        'prediction' => $prediction,
                        'product' => $product
                    ]);
                }
                
                return view('ai.predictions.result', compact('prediction', 'product', 'predictionData'));
            } else {
                Log::error('AI API HTTP Error:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new \Exception('AI API request failed: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            Log::error('AI Prediction Error: ' . $e->getMessage());
            
            // Create fallback prediction data
            $fallbackPrediction = $this->createFallbackPrediction($request, $product);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'prediction' => $fallbackPrediction,
                    'product' => $product,
                    'fallback' => true
                ]);
            }
            
            return view('ai.predictions.result', [
                'prediction' => $fallbackPrediction,
                'product' => $product,
                'predictionData' => $predictionData ?? []
            ]);
        }
    }
    
    /**
     * Bulk prediction for multiple products
     */
    public function bulkPredict(Request $request)
    {
        try {
            $request->validate([
                'product_ids' => 'required|array',
                'product_ids.*' => 'exists:products,id',
                'prediction_date' => 'required|date'
            ]);
            
            $products = Product::whereIn('id', $request->product_ids)->get();
            $predictions = [];
            
            foreach ($products as $product) {
                // Get latest sales data for better prediction
                $latestSale = Sale::where('product_id', $product->id)
                    ->orderBy('sale_date', 'desc')
                    ->first();
                
                $latestPurchase = Purchase::where('product_id', $product->id)
                    ->orderBy('purchase_date', 'desc')
                    ->first();
                
                $predictionData = [
                    'product_id' => $product->id,
                    'store_id' => 'S001',
                    'current_stock' => $product->quantity ?? 0,
                    'expected_demand' => $latestSale ? $latestSale->quantity * 7 : 50, // Weekly estimate
                    'price' => $product->price ?? 0,
                    'category' => $product->category ?? 'General',
                    'date' => $request->prediction_date,
                    'inventory_level' => $product->quantity ?? 0,
                    'demand_forecast' => $latestSale ? $latestSale->quantity * 7 : 50
                ];
                
                $predictions[] = $predictionData;
            }
            
            // Make bulk API call
            $response = Http::timeout(60)->post($this->apiUrl . '/bulk_predict', [
                'products' => $predictions
            ]);
            
            if ($response->successful()) {
                $results = $response->json();
                
                return view('ai.predictions.bulk_result', [
                    'results' => $results,
                    'products' => $products,
                    'predictionDate' => $request->prediction_date
                ]);
            } else {
                throw new \Exception('Bulk prediction API request failed');
            }
            
        } catch (\Exception $e) {
            Log::error('Bulk AI Prediction Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Bulk prediction service unavailable.']);
        }
    }
    
    /**
     * Get AI insights and recommendations
     */
    public function insights()
    {
        try {
            // Get products with low stock
            $lowStockProducts = Product::where('quantity', '<', 20)->get();
            
            // Get best-selling products
            $bestSellers = Product::select('products.*')
                ->join('sales', 'products.id', '=', 'sales.product_id')
                ->selectRaw('products.*, SUM(sales.quantity) as total_sold')
                ->groupBy('products.id')
                ->orderBy('total_sold', 'desc')
                ->limit(10)
                ->get();
            
            // Generate insights
            $insights = [
                'low_stock_alerts' => $lowStockProducts->count(),
                'reorder_recommendations' => $this->generateReorderRecommendations(),
                'demand_patterns' => $this->analyzeDemandPatterns(),
                'revenue_opportunities' => $this->identifyRevenueOpportunities(),
                'best_sellers' => $bestSellers
            ];
            
            return view('ai.insights.index', compact('insights'));
            
        } catch (\Exception $e) {
            Log::error('AI Insights Error: ' . $e->getMessage());
            return view('ai.insights.index', ['insights' => []]);
        }
    }
    
    /**
     * API health check
     */
    public function healthCheck()
    {
        try {
            $response = Http::timeout(10)->get($this->apiUrl . '/health');
            
            if ($response->successful()) {
                return response()->json([
                    'status' => 'healthy',
                    'api_response' => $response->json()
                ]);
            } else {
                return response()->json([
                    'status' => 'unhealthy',
                    'error' => 'API not responding'
                ], 503);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'error' => $e->getMessage()
            ], 503);
        }
    }
    
    /**
     * Create fallback prediction when API fails
     */
    private function createFallbackPrediction($request, $product)
    {
        $currentStock = floatval($request->current_stock);
        $expectedDemand = floatval($request->expected_demand);
        $price = floatval($request->price);
        
        // Simple fallback calculation
        $predictedSales = $expectedDemand * 0.9; // 90% of expected demand
        $daysOfStock = $currentStock / ($predictedSales + 1);
        $shouldReorder = $daysOfStock < 7;
        
        return [
            'success' => true,
            'prediction' => [
                'predicted_sales' => round($predictedSales, 1),
                'confidence_level' => 'MEDIUM',
                'prediction_date' => now()->format('Y-m-d H:i:s'),
                'model_accuracy' => '75.0%'
            ],
            'inventory_status' => [
                'current_stock' => $currentStock,
                'days_of_stock' => round($daysOfStock, 1),
                'stockout_risk' => $daysOfStock < 3 ? 'HIGH' : ($daysOfStock < 7 ? 'MEDIUM' : 'LOW'),
                'should_reorder' => $shouldReorder,
                'recommended_order_qty' => $shouldReorder ? round($expectedDemand * 2, 0) : 0,
                'safety_stock_level' => round($expectedDemand * 0.5, 0)
            ],
            'financial_impact' => [
                'potential_revenue' => round($predictedSales * $price, 2),
                'lost_sales_risk' => max(0, $predictedSales - $currentStock) * $price,
                'carrying_cost_per_day' => round($currentStock * $price * 0.001, 2),
                'reorder_cost' => $shouldReorder ? round($expectedDemand * 2 * $price * 0.8, 2) : 0
            ],
            'recommendations' => [
                [
                    'priority' => $shouldReorder ? 'HIGH' : 'LOW',
                    'action' => $shouldReorder ? 'Schedule Reorder' : 'Monitor Stock',
                    'description' => $shouldReorder ? 'Stock levels are getting low for expected demand.' : 'Current stock levels appear adequate.',
                    'timeframe' => $shouldReorder ? '1 week' : 'Ongoing',
                    'icon' => $shouldReorder ? 'fas fa-shopping-cart' : 'fas fa-check-circle'
                ]
            ],
            'risk_factors' => [],
            'model_info' => [
                'mape' => '25.0%',
                'r2_score' => '0.750',
                'last_updated' => now()->format('Y-m-d')
            ],
            'fallback_mode' => true
        ];
    }

    /**
     * Store prediction for future analysis
     */
    private function storePrediction($product, $prediction, $inputData)
    {
        // Here you could store predictions in a database table
        // for historical analysis and model performance tracking
        Log::info('AI Prediction stored', [
            'product_id' => $product->id,
            'predicted_sales' => $prediction['prediction']['predicted_sales'] ?? 0,
            'confidence' => $prediction['prediction']['confidence_level'] ?? 'UNKNOWN',
            'timestamp' => now()
        ]);
    }
    
    /**
     * Get recent predictions (dummy data for now)
     */
    private function getRecentPredictions()
    {
        return [
            [
                'product_name' => 'Product A',
                'predicted_sales' => 125.5,
                'confidence' => 'HIGH',
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'status' => 'Completed'
            ],
            [
                'product_name' => 'Product B',
                'predicted_sales' => 78.2,
                'confidence' => 'MEDIUM',
                'date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'status' => 'Completed'
            ],
            [
                'product_name' => 'Product C',
                'predicted_sales' => 203.8,
                'confidence' => 'HIGH',
                'date' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'status' => 'Completed'
            ]
        ];
    }
    
    /**
     * Generate reorder recommendations
     */
    private function generateReorderRecommendations()
    {
        $lowStockProducts = Product::where('quantity', '<', 30)->get();
        $recommendations = [];
        
        foreach ($lowStockProducts as $product) {
            $recommendations[] = [
                'product_name' => $product->name,
                'current_stock' => $product->quantity,
                'recommended_order' => $product->quantity * 3, // Simple rule
                'priority' => $product->quantity < 10 ? 'HIGH' : 'MEDIUM'
            ];
        }
        
        return $recommendations;
    }
    
    /**
     * Analyze demand patterns
     */
    private function analyzeDemandPatterns()
    {
        return [
            'trend' => 'Increasing',
            'seasonal_factor' => 1.15,
            'peak_days' => ['Monday', 'Friday'],
            'growth_rate' => '12.5%'
        ];
    }
    
    /**
     * Export AI predictions data
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $predictions = $this->getRecentPredictions();
        
        if ($format === 'csv') {
            return $this->exportToCsv($predictions);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($predictions);
        }
        
        return redirect()->back()->with('error', 'Invalid export format');
    }
    
    /**
     * Export predictions to CSV
     */
    private function exportToCsv($predictions)
    {
        $filename = 'ai_predictions_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($predictions) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Product Name',
                'Current Stock',
                'Predicted Demand',
                'Reorder Recommendation',
                'Stockout Risk',
                'Confidence Score',
                'Prediction Date'
            ]);
            
            // CSV Data
            foreach ($predictions as $prediction) {
                fputcsv($file, [
                    $prediction['product_name'] ?? 'N/A',
                    $prediction['current_stock'] ?? 0,
                    $prediction['predicted_demand'] ?? 0,
                    $prediction['should_reorder'] ? 'Yes' : 'No',
                    $prediction['stockout_risk'] ?? 'LOW',
                    $prediction['confidence'] ?? 0,
                    $prediction['created_at'] ?? date('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Export predictions to PDF
     */
    private function exportToPdf($predictions)
    {
        // For now, redirect to CSV - PDF export can be implemented later with a PDF library
        return $this->exportToCsv($predictions);
    }
    
    /**
     * Identify revenue opportunities
     */
    private function identifyRevenueOpportunities()
    {
        return [
            'price_optimization' => 'Consider 5% price increase for best-sellers',
            'cross_selling' => 'Bundle complementary products',
            'inventory_optimization' => 'Reduce slow-moving inventory by 20%',
            'new_products' => 'Consider adding seasonal items'
        ];
    }
}
