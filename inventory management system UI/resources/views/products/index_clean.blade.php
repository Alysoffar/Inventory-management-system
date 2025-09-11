@extends('layouts.app')

@section('title', 'Products')

@section('content')
<style>
/* AWS Cloudscape Design System - Products Management */
:root {
    --aws-color-blue-600: #146eb4;
    --aws-color-blue-700: #0972d3;
    --aws-color-grey-900: #16191f;
    --aws-color-grey-600: #5f6b7a;
    --aws-color-grey-200: #e9ebed;
    --aws-color-green-600: #037f0c;
    --aws-color-orange-600: #b7740e;
    --aws-color-red-600: #d13212;
}

.products-management .card {
    border: 1px solid var(--aws-color-grey-200);
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    min-height: 180px;
    transition: all 0.2s ease;
}

.products-management .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.products-management .stats-card {
    min-height: 200px;
}

.products-management .card-body {
    padding: 24px;
}

.products-management .metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #212529 !important;
    margin-bottom: 8px;
}

.products-management .metric-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 8px;
}

.products-management .page-title {
    font-size: 2rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 8px;
}

.products-management .page-subtitle {
    font-size: 1.1rem;
    color: #212529 !important;
    margin: 0;
}

.products-management .card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #212529 !important;
    margin: 0;
}

.products-management .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
    padding: 1.5rem;
}

.products-management .btn {
    font-size: 1rem;
    font-weight: 500;
    padding: 10px 16px;
    border-radius: 6px;
}

.products-management .form-control, .products-management .form-select {
    font-size: 1rem;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid var(--aws-color-grey-200);
}

.products-management th {
    font-size: 1rem !important;
    font-weight: 600;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
}

.products-management td {
    font-size: 1.1rem !important;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    border-bottom: 1px solid #f8f9fc;
}

.products-management .badge {
    font-size: 1rem;
    padding: 6px 12px;
}

.products-management .btn-sm {
    font-size: 0.9rem;
    padding: 8px 12px;
}

.products-management h1, .products-management h2, .products-management h3, .products-management h4, .products-management h5, .products-management h6,
.products-management p, .products-management span, .products-management div, .products-management a, .products-management li {
    color: #212529 !important;
}

.products-management .text-muted {
    color: #5f6b7a !important;
}

.products-management .empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.products-management .empty-state i {
    font-size: 4rem;
    color: #5f6b7a;
    margin-bottom: 2rem;
}

.products-management .empty-state h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 1rem;
}

.products-management .empty-state p {
    font-size: 1.1rem;
    color: #5f6b7a !important;
    margin-bottom: 2rem;
}
</style>

<div class="container-fluid products-management">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">📦 Products Management</h2>
                <p class="page-subtitle">Manage your inventory products and stock levels</p>
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
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-boxes text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">{{ number_format($stats['total_products'] ?? 0) }}</h2>
                    <p class="metric-label">Total Products</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+5.2% from last month</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">{{ number_format($stats['low_stock'] ?? 0) }}</h2>
                    <p class="metric-label">Low Stock Items</p>
                    <small class="text-danger"><i class="fas fa-arrow-down me-1"></i>Needs attention</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">{{ number_format($stats['in_stock'] ?? 0) }}</h2>
                    <p class="metric-label">In Stock</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+3.1% from last week</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-dollar-sign text-info mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">${{ number_format($stats['total_value'] ?? 0, 2) }}</h2>
                    <p class="metric-label">Total Value</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+8.7% from last month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="card-title">Products List</h6>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> Add Product
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="row g-3 mb-4 p-3 bg-light rounded">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search Products</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Product name or SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        @if(isset($categories))
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="stock_status" class="form-label">Stock Status</label>
                    <select class="form-select" id="stock_status" name="stock_status">
                        <option value="">All Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <!-- Products Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products ?? [] as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if(isset($product->image))
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover; margin-right: 12px;">
                                    @else
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; margin-right: 12px;">
                                            <i class="fas fa-box text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $product->name ?? 'N/A' }}</strong>
                                        @if(isset($product->description))
                                            <br><small class="text-muted">{{ Str::limit($product->description, 30) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code>{{ $product->sku ?? 'N/A' }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $product->category ?? 'Uncategorized' }}</span>
                            </td>
                            <td>
                                <strong>${{ number_format($product->price ?? 0, 2) }}</strong>
                            </td>
                            <td>
                                <strong>{{ number_format($product->quantity ?? 0) }}</strong>
                                @if(isset($product->unit))
                                    <small class="text-muted">{{ $product->unit }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $quantity = $product->quantity ?? 0;
                                    $lowStockThreshold = $product->low_stock_threshold ?? 10;
                                @endphp
                                @if($quantity <= 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($quantity <= $lowStockThreshold)
                                    <span class="badge bg-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-success">In Stock</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('products.show', $product->id ?? 1) }}">
                                            <i class="fas fa-eye me-2"></i> View
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('products.edit', $product->id ?? 1) }}">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $product->id ?? 1 }})">
                                            <i class="fas fa-trash me-2"></i> Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-boxes"></i>
                                    <h4>No Products Found</h4>
                                    <p>There are no products to display.</p>
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

            <!-- Pagination -->
            @if(isset($products) && method_exists($products, 'links'))
                <div class="row">
                    <div class="col">
                        <div class="mt-3">
                            {{ $products->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(productId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/products/${productId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush
@endsection
