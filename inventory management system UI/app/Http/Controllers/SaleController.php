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
        $query = Sale::with(['product', 'customer']);
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('product', fn($q) => $q->where('name', 'LIKE', "%{$search}%"))
                  ->orWhereHas('customer', fn($q) => $q->where('name', 'LIKE', "%{$search}%"));
        }
        $sales = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('quantity', '>', 0)->get();
        $customers = Customer::all();
        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'sale_date' => 'required|date'
        ]);

        $product = Product::findOrFail($validated['product_id']);
        if ($product->quantity < $validated['quantity']) {
            return redirect()->back()->withInput()->with('error', 'Insufficient stock. Available: ' . $product->quantity);
        }

        DB::transaction(function () use ($validated, $product) {
            $unitPrice = $product->price;
            $totalAmount = $unitPrice * $validated['quantity'];
            Sale::create([
                'product_id' => $validated['product_id'],
                'customer_id' => $validated['customer_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'sale_date' => $validated['sale_date']
            ]);
            $product->decrement('quantity', $validated['quantity']);
        });

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully!');
    }

    public function show(Sale $sale)
    {
        $sale->load(['product', 'customer']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $products = Product::all();
        $customers = Customer::all();
        $sale->load(['product', 'customer']);
        return view('sales.edit', compact('sale', 'products', 'customers'));
    }

    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_id' => 'required|exists:customers,id',
            'quantity' => 'required|integer|min:1',
            'sale_date' => 'required|date'
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $oldProduct = $sale->product;
        $oldQuantity = $sale->quantity;

        DB::transaction(function () use ($validated, $product, $oldProduct, $oldQuantity, $sale) {
            $oldProduct->increment('quantity', $oldQuantity);
            if ($product->quantity < $validated['quantity']) {
                throw new \Exception('Insufficient stock. Available: ' . $product->quantity);
            }
            $unitPrice = $product->price;
            $totalAmount = $unitPrice * $validated['quantity'];
            $sale->update([
                'product_id' => $validated['product_id'],
                'customer_id' => $validated['customer_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'sale_date' => $validated['sale_date']
            ]);
            $product->decrement('quantity', $validated['quantity']);
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully!');
    }

    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            $sale->product->increment('quantity', $sale->quantity);
            $sale->delete();
        });

        return redirect()->route('sales.index')->with('success', 'Sale deleted and stock restored!');
    }

    public function getProductPrice($productId)
    {
        $product = Product::find($productId);
        return response()->json([
            'price' => $product ? $product->price : 0,
            'available_quantity' => $product ? $product->quantity : 0
        ]);
    }
}
