<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        // Build the query with supplier relationship only
        $query = Purchase::with(['supplier', 'purchaseItems' => function($query) {
            $query->with('product');
        }]);
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }
        
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search functionality - removed the problematic orWhereHas that causes duplicates
        if ($request->has('search') && !empty($request->get('search'))) {
            $search = $request->get('search');
            $query->whereHas('supplier', function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%");
            });
        }
        
        // Get paginated purchases - removed distinct() as it wasn't working properly
        $purchases = $query->orderBy('order_date', 'desc')->paginate(10);
        
        // Calculate statistics
        $totalPurchases = Purchase::count();
        $totalCost = Purchase::sum('total_amount') ?? 0;
        $todayPurchases = Purchase::whereDate('order_date', today())->count();
        $averagePurchase = $totalPurchases > 0 ? $totalCost / $totalPurchases : 0;
        
        // Get suppliers for filter dropdown
        $suppliers = Supplier::orderBy('name')->get();
        
        return view('purchases.index', compact(
            'purchases', 
            'totalPurchases', 
            'totalCost', 
            'todayPurchases', 
            'averagePurchase',
            'suppliers'
        ));
    }

    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        return view('purchases.create', compact('products', 'suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'status' => 'required|in:pending,ordered,received,cancelled',
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            // Calculate total amount
            $totalAmount = 0;
            foreach ($validated['products'] as $product) {
                $totalAmount += $product['quantity'] * $product['unit_cost'];
            }

            // Create the purchase order
            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'total_amount' => $totalAmount,
                'status' => $validated['status'],
                'order_date' => $validated['order_date'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create purchase items for each product
            foreach ($validated['products'] as $productData) {
                $purchase->purchaseItems()->create([
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_cost'],
                    'total_price' => $productData['quantity'] * $productData['unit_cost'],
                ]);

                // Update product stock if status is received
                if ($validated['status'] === 'received') {
                    Product::findOrFail($productData['product_id'])
                        ->increment('quantity', $productData['quantity']);
                }
            }
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase order created successfully!');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['product', 'supplier']);
        return view('purchases.show', compact('purchase'));
    }

    public function edit(Purchase $purchase)
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $purchase->load(['product', 'supplier']);
        return view('purchases.edit', compact('purchase', 'products', 'suppliers'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date'
        ]);

        DB::transaction(function () use ($validated, $purchase) {
            $oldProduct = $purchase->product;
            $oldQuantity = $purchase->quantity;
            $oldProduct->decrement('quantity', $oldQuantity);

            $totalCost = $validated['unit_cost'] * $validated['quantity'];
            $purchase->update([
                'product_id' => $validated['product_id'],
                'supplier_id' => $validated['supplier_id'],
                'quantity' => $validated['quantity'],
                'unit_cost' => $validated['unit_cost'],
                'total_cost' => $totalCost,
                'purchase_date' => $validated['purchase_date']
            ]);

            Product::findOrFail($validated['product_id'])->increment('quantity', $validated['quantity']);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase updated successfully!');
    }

    public function destroy(Purchase $purchase)
    {
        DB::transaction(function () use ($purchase) {
            $purchase->product->decrement('quantity', $purchase->quantity);
            $purchase->delete();
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase deleted and stock adjusted!');
    }
}
