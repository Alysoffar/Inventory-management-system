<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        // Build the query with relationships
        $query = Sale::with(['customer', 'saleItems.product']);
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('customer', fn($q) => $q->where('name', 'LIKE', "%{$search}%"));
        }
        
        // Get paginated sales
        $sales = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate statistics
        $totalSales = Sale::count();
        $totalRevenue = Sale::sum('total_amount') ?? 0;
        $todaySales = Sale::whereDate('created_at', today())->count();
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        
        // Get customers for filter dropdown
        $customers = Customer::orderBy('name')->get();
        
        return view('sales.index', compact(
            'sales', 
            'totalSales', 
            'totalRevenue', 
            'todaySales', 
            'averageSale',
            'customers'
        ));
    }

    public function create()
    {
        $products = Product::where('quantity', '>', 0)->get();
        $customers = Customer::all();
        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        // Validate the request for multiple products
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Start transaction for data integrity
        DB::transaction(function () use ($validated) {
            $totalAmount = 0;
            $saleProducts = [];

            // First, validate stock availability for all products
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                if ($product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}, Requested: {$productData['quantity']}");
                }
                
                $itemTotal = $productData['quantity'] * $productData['unit_price'];
                $totalAmount += $itemTotal;
                
                $saleProducts[] = [
                    'product' => $product,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'total' => $itemTotal
                ];
            }

            // Create the main sale record
            $sale = Sale::create([
                'customer_id' => $validated['customer_id'],
                'sale_date' => $validated['sale_date'],
                'total_amount' => $totalAmount,
                'status' => 'completed',
            ]);

            // Create sale items and update product stock
            foreach ($saleProducts as $saleProduct) {
                // Create sale item
                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $saleProduct['product']->id,
                    'quantity' => $saleProduct['quantity'],
                    'unit_price' => $saleProduct['unit_price'],
                    'total_price' => $saleProduct['total'],
                ]);

                // Update product stock
                $saleProduct['product']->decrement('stock_quantity', $saleProduct['quantity']);
            }
        });

        return redirect()->route('sales.index')->with('success', 'Sales transaction recorded successfully!');
    }

    public function show(Sale $sale)
    {
        $sale->load(['saleItems.product', 'customer']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $products = Product::all();
        $customers = Customer::all();
        $sale->load(['saleItems.product', 'customer']);
        return view('sales.edit', compact('sale', 'products', 'customers'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000'
        ]);

        DB::transaction(function () use ($validated, $sale) {
            // First, restore stock from original sale items
            foreach ($sale->saleItems as $oldItem) {
                $oldItem->product->increment('stock_quantity', $oldItem->quantity);
            }
            
            // Delete old sale items
            $sale->saleItems()->delete();

            $totalAmount = 0;
            $saleProducts = [];

            // Validate stock availability for all new products
            foreach ($validated['products'] as $productData) {
                $product = Product::findOrFail($productData['product_id']);
                if ($product->stock_quantity < $productData['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->stock_quantity}, Requested: {$productData['quantity']}");
                }
                
                $itemTotal = $productData['quantity'] * $productData['unit_price'];
                $totalAmount += $itemTotal;
                
                $saleProducts[] = [
                    'product' => $product,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'total' => $itemTotal
                ];
            }

            // Update the main sale record
            $sale->update([
                'customer_id' => $validated['customer_id'],
                'sale_date' => $validated['sale_date'],
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create new sale items and update product stock
            foreach ($saleProducts as $saleProduct) {
                // Create sale item
                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $saleProduct['product']->id,
                    'quantity' => $saleProduct['quantity'],
                    'unit_price' => $saleProduct['unit_price'],
                    'total_price' => $saleProduct['total'],
                ]);

                // Update product stock
                $saleProduct['product']->decrement('stock_quantity', $saleProduct['quantity']);
            }
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully!');
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            // Restore stock for all sale items
            foreach ($sale->saleItems as $item) {
                $item->product->increment('stock_quantity', $item->quantity);
            }
            
            // Delete sale items first
            $sale->saleItems()->delete();
            
            // Delete the sale
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted and stock restored!');
    }

    public function getProductPrice($productId)
    {
        $product = Product::find($productId);
        return response()->json([
            'price' => $product ? $product->price : 0,
            'available_quantity' => $product ? $product->stock_quantity : 0
        ]);
    }
}
