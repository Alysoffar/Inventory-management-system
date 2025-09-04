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
        // Build the query with supplier relationship
        $query = Purchase::with(['supplier']);
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        
        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('supplier', fn($q) => $q->where('name', 'LIKE', "%{$search}%"));
        }
        
        // Get paginated purchases
        $purchases = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate statistics
        $totalPurchases = Purchase::count();
        $totalCost = Purchase::sum('total_amount') ?? 0;
        $todayPurchases = Purchase::whereDate('created_at', today())->count();
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
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'purchase_date' => 'required|date'
        ]);

        DB::transaction(function () use ($validated) {
            $totalCost = $validated['unit_cost'] * $validated['quantity'];
            Purchase::create([
                'product_id' => $validated['product_id'],
                'supplier_id' => $validated['supplier_id'],
                'quantity' => $validated['quantity'],
                'unit_cost' => $validated['unit_cost'],
                'total_cost' => $totalCost,
                'purchase_date' => $validated['purchase_date']
            ]);
            Product::findOrFail($validated['product_id'])->increment('quantity', $validated['quantity']);
        });

        return redirect()->route('purchases.index')->with('success', 'Purchase recorded successfully!');
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
