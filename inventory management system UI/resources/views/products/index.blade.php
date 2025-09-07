@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2 fw-semibold">📦 Products Management</h2>
            <p class="mb-0 text-muted">Manage your inventory products and stock levels</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add Product
            </a>
            <a href="{{ route('inventory.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-warehouse me-2"></i> Inventory
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-boxes text-primary mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ number_format($stats['total_products']) }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Total Products</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ number_format($stats['low_stock']) }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Low Stock</p>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['low_stock_count']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-times-circle text-danger mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ number_format($stats['out_of_stock_count']) }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Out of Stock</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-dollar-sign text-success mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">${{ number_format($stats['total_value'], 2) }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Total Value</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-2 px-3">
            <h6 class="mb-0 fw-semibold text-primary">Filter & Search Products</h6>
        </div>
        <div class="card-body p-3">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search products..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="discontinued" {{ request('status') === 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="stock_filter" class="form-select">
                        <option value="">All Stock</option>
                        <option value="normal" {{ request('stock_filter') === 'normal' ? 'selected' : '' }}>Normal Stock</option>
                        <option value="low" {{ request('stock_filter') === 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ request('stock_filter') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="location" class="form-control" 
                           placeholder="Location" value="{{ request('location') }}">
                </div>
                <div class="col-md-3">
                    <div class="btn-group w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i> Search
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header py-2 px-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold text-primary">Products List</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('inventory.export') }}?format=csv">CSV Format</a></li>
                    <li><a class="dropdown-item" href="#">PDF Format</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Product</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">SKU</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Category</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Price</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Stock</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Location</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Status</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td class="py-2 px-3">
                                    <div class="d-flex align-items-center">
                                        <div class="ms-3">
                                            <div class="fw-bold" style="font-size: 1.1rem;">{{ $product->name }}</div>
                                            <div class="text-muted small">{{ $product->description ?? 'No description' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 px-3">
                                    <span class="badge bg-secondary" style="font-size: 0.9rem;">{{ $product->sku }}</span>
                                </td>
                                <td class="py-2 px-3" style="font-size: 1rem;">{{ $product->category }}</td>
                                <td class="py-2 px-3 fw-bold" style="font-size: 1.1rem;">${{ number_format($product->price, 2) }}</td>
                                <td class="py-3 px-4">
                                    <div class="d-flex align-items-center">
                                        <span class="badge 
                                            @if($product->stock_quantity <= 0)
                                                bg-danger
                                            @elseif($product->isLowStock())
                                                bg-warning
                                            @else
                                                bg-success
                                            @endif
                                        ">
                                            {{ $product->stock_quantity }}
                                        </span>
                                        <small class="text-muted ms-2">/ {{ $product->minimum_stock_level }} min</small>
                                        @if($product->auto_reorder)
                                            <i class="fas fa-sync text-success ms-1" title="Auto-reorder enabled"></i>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if($product->location)
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                            <span>{{ $product->location }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="badge 
                                        @switch($product->status)
                                            @case('active') bg-success @break
                                            @case('inactive') bg-warning @break
                                            @case('discontinued') bg-danger @break
                                            @default badge-secondary
                                        @endswitch
                                    ">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('products.show', $product) }}" 
                                           class="btn btn-sm btn-outline-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="showStockAdjustment({{ $product->id }})" title="Adjust Stock">
                                            <i class="fas fa-plus-minus"></i>
                                        </button>
                                        @if($product->needsRestock())
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="manualRestock({{ $product->id }})" title="Restock">
                                                <i class="fas fa-refresh"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteProduct({{ $product->id }})" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="fas fa-box-open fa-3x text-muted mb-4"></i>
                                        <h4 class="text-muted mb-3">No products found</h4>
                                        <p class="text-muted mb-4">Try adjusting your search criteria or add a new product.</p>
                                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i> Add First Product
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($products->hasPages())
            <div class="card-footer py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                    </div>
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adjust Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockAdjustmentForm">
                <div class="modal-body">
                    <input type="hidden" id="adjustProductId">
                    <div class="mb-3">
                        <label for="adjustmentType" class="form-label">Adjustment Type</label>
                        <select class="form-select" id="adjustmentType" required>
                            <option value="">Select type</option>
                            <option value="increase">Increase Stock</option>
                            <option value="decrease">Decrease Stock</option>
                            <option value="set">Set Exact Amount</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adjustmentQuantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="adjustmentQuantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="adjustmentLocation" class="form-label">Location (Optional)</label>
                        <input type="text" class="form-control" id="adjustmentLocation">
                    </div>
                    <div class="mb-3">
                        <label for="adjustmentNotes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="adjustmentNotes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Adjust Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showStockAdjustment(productId) {
    document.getElementById('adjustProductId').value = productId;
    document.getElementById('stockAdjustmentForm').reset();
    const modal = new bootstrap.Modal(document.getElementById('stockAdjustmentModal'));
    modal.show();
}

function manualRestock(productId) {
    if (confirm('Trigger manual restock for this product?')) {
        fetch(`/products/${productId}/manual-restock`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Failed to restock product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while restocking');
        });
    }
}

function deleteProduct(productId) {
    document.getElementById('deleteForm').action = `/products/${productId}`;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Handle stock adjustment form submission
document.getElementById('stockAdjustmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const productId = document.getElementById('adjustProductId').value;
    const adjustmentType = document.getElementById('adjustmentType').value;
    const quantity = document.getElementById('adjustmentQuantity').value;
    const location = document.getElementById('adjustmentLocation').value;
    const notes = document.getElementById('adjustmentNotes').value;
    
    fetch(`/products/${productId}/adjust-stock`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            adjustment_type: adjustmentType,
            quantity: parseInt(quantity),
            location: location,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Failed to adjust stock: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adjusting stock');
    });
});

// Auto-refresh every 5 minutes to show updated stock levels
setTimeout(() => {
    location.reload();
}, 300000);
</script>
@endsection
