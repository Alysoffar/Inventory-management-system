<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryLog;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with(['supplier']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        // Filter by stock level
        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low':
                    $query->whereRaw('stock_quantity <= minimum_stock_level');
                    break;
                case 'out':
                    $query->where('stock_quantity', '<=', 0);
                    break;
                case 'normal':
                    $query->whereRaw('stock_quantity > minimum_stock_level');
                    break;
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(20);

        // Get summary statistics
        $stats = [
            'total_products' => Product::count(),
            'low_stock_count' => Product::whereRaw('stock_quantity <= minimum_stock_level')->count(),
            'out_of_stock_count' => Product::where('stock_quantity', '<=', 0)->count(),
            'total_value' => Product::selectRaw('SUM(stock_quantity * price) as total')->value('total') ?? 0,
        ];

        return view('products.index', compact('products', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::active()->get();
        return view('products.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku|max:100',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'maximum_stock_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'auto_reorder' => 'boolean',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $product = Product::create($request->all());

            // Log the initial stock entry
            InventoryLog::create([
                'product_id' => $product->id,
                'type' => 'adjustment',
                'quantity_before' => 0,
                'quantity_after' => $product->stock_quantity,
                'quantity_changed' => $product->stock_quantity,
                'notes' => 'Initial stock entry',
                'location_name' => $product->location,
                'latitude' => $product->latitude,
                'longitude' => $product->longitude,
            ]);

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create product: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with(['supplier', 'inventoryLogs' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(50);
        }])->findOrFail($id);

        $recentLogs = $product->inventoryLogs;
        
        // Get stock movements summary for the last 30 days
        $stockMovements = InventoryLog::where('product_id', $id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('type, SUM(ABS(quantity_changed)) as total_quantity, COUNT(*) as count')
            ->groupBy('type')
            ->get();

        return view('products.show', compact('product', 'recentLogs', 'stockMovements'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $suppliers = Supplier::active()->get();
        return view('products.edit', compact('product', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100|unique:products,sku,' . $id,
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'minimum_stock_level' => 'required|integer|min:0',
            'maximum_stock_level' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'auto_reorder' => 'boolean',
            'status' => 'required|in:active,inactive,discontinued',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $product->update($request->all());

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if product has any sales or purchase records
        if ($product->sales()->exists() || $product->purchases()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete product with existing sales or purchase records.');
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Adjust stock quantity for a product
     */
    public function adjustStock(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'adjustment_type' => 'required|in:increase,decrease,set',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $oldQuantity = $product->stock_quantity;
            $notes = $request->notes ?? 'Manual stock adjustment';

            switch ($request->adjustment_type) {
                case 'increase':
                    $newQuantity = $oldQuantity + $request->quantity;
                    break;
                case 'decrease':
                    $newQuantity = max(0, $oldQuantity - $request->quantity);
                    break;
                case 'set':
                    $newQuantity = $request->quantity;
                    break;
            }

            $product->updateStock(
                $newQuantity,
                'adjustment',
                null,
                null,
                $notes,
                $request->location
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Stock adjusted successfully',
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to adjust stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manually trigger restock for a product
     */
    public function manualRestock($id)
    {
        $product = Product::findOrFail($id);

        DB::beginTransaction();
        try {
            $oldQuantity = $product->stock_quantity;
            $restockQuantity = $product->reorder_quantity;
            $newQuantity = $oldQuantity + $restockQuantity;

            $product->updateStock(
                $newQuantity,
                'manual_restock',
                null,
                null,
                'Manual restock triggered by admin'
            );

            $product->update(['last_restocked_at' => now()]);

            DB::commit();

            return redirect()->back()
                ->with('success', "Product restocked successfully. Added {$restockQuantity} units.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to restock product: ' . $e->getMessage());
        }
    }

    /**
     * Get inventory map data
     */
    public function mapData(Request $request)
    {
        $products = Product::active()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('stock_filter')) {
            switch ($request->stock_filter) {
                case 'low':
                    $products->whereRaw('stock_quantity <= minimum_stock_level');
                    break;
                case 'out':
                    $products->where('stock_quantity', '<=', 0);
                    break;
            }
        }

        $products = $products->get();

        $mapData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'stock_quantity' => $product->stock_quantity,
                'minimum_stock_level' => $product->minimum_stock_level,
                'location' => $product->location,
                'latitude' => (float) $product->latitude,
                'longitude' => (float) $product->longitude,
                'status' => $product->stock_quantity <= 0 ? 'out_of_stock' : 
                           ($product->isLowStock() ? 'low_stock' : 'normal'),
                'url' => route('products.show', $product),
            ];
        });

        return response()->json($mapData);
    }
}
