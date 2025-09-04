@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Inventory Report</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                        <li class="breadcrumb-item active">Inventory Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title mb-0">Report Filters</h5>
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-primary" onclick="exportReport()">
                                <i class="ri-download-line me-1"></i> Export Report
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <option value="Electronics">Electronics</option>
                                <option value="Tools">Tools</option>
                                <option value="Supplies">Supplies</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Stock Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Items</option>
                                <option value="in_stock">In Stock</option>
                                <option value="low_stock">Low Stock</option>
                                <option value="out_of_stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="value_range" class="form-label">Value Range</label>
                            <select class="form-select" id="value_range" name="value_range">
                                <option value="">All Values</option>
                                <option value="0-100">$0 - $100</option>
                                <option value="100-500">$100 - $500</option>
                                <option value="500+">$500+</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="ri-search-line me-1"></i> Apply Filters
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Items</p>
                            <h4 class="mb-2">{{ $totalItems ?? 125 }}</h4>
                            <p class="text-muted mb-0">Across all categories</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-archive-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Value</p>
                            <h4 class="mb-2">${{ number_format($totalValue ?? 45780.50, 2) }}</h4>
                            <p class="text-muted mb-0">Current inventory value</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-money-dollar-circle-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Low Stock Items</p>
                            <h4 class="mb-2 text-warning">{{ $lowStockItems ?? 8 }}</h4>
                            <p class="text-muted mb-0">Need restocking</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="ri-error-warning-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Out of Stock</p>
                            <h4 class="mb-2 text-danger">{{ $outOfStockItems ?? 3 }}</h4>
                            <p class="text-muted mb-0">Immediate attention</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-danger rounded-3">
                                <i class="ri-alert-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventory Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Inventory Details</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Unit Price</th>
                                    <th>Total Value</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Sample inventory data - replace with actual data from controller
                                    $inventoryItems = [
                                        (object)[
                                            'id' => 1,
                                            'name' => 'Widget A',
                                            'sku' => 'WID-001',
                                            'category' => 'Electronics',
                                            'stock_quantity' => 5,
                                            'price' => 25.99,
                                            'status' => 'low_stock'
                                        ],
                                        (object)[
                                            'id' => 2,
                                            'name' => 'Widget B',
                                            'sku' => 'WID-002',
                                            'category' => 'Tools',
                                            'stock_quantity' => 2,
                                            'price' => 45.50,
                                            'status' => 'low_stock'
                                        ],
                                        (object)[
                                            'id' => 3,
                                            'name' => 'Widget C',
                                            'sku' => 'WID-003',
                                            'category' => 'Supplies',
                                            'stock_quantity' => 8,
                                            'price' => 12.75,
                                            'status' => 'low_stock'
                                        ],
                                        (object)[
                                            'id' => 4,
                                            'name' => 'Widget D',
                                            'sku' => 'WID-004',
                                            'category' => 'Electronics',
                                            'stock_quantity' => 25,
                                            'price' => 89.99,
                                            'status' => 'in_stock'
                                        ],
                                        (object)[
                                            'id' => 5,
                                            'name' => 'Widget E',
                                            'sku' => 'WID-005',
                                            'category' => 'Supplies',
                                            'stock_quantity' => 50,
                                            'price' => 8.99,
                                            'status' => 'in_stock'
                                        ]
                                    ];
                                @endphp

                                @forelse($inventoryItems as $item)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $item->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <code>{{ $item->sku }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $item->category }}</span>
                                    </td>
                                    <td>
                                        <strong class="{{ $item->stock_quantity <= 10 ? 'text-warning' : 'text-success' }}">
                                            {{ $item->stock_quantity }}
                                        </strong>
                                    </td>
                                    <td>
                                        ${{ number_format($item->price, 2) }}
                                    </td>
                                    <td>
                                        <strong>${{ number_format($item->price * $item->stock_quantity, 2) }}</strong>
                                    </td>
                                    <td>
                                        @if($item->stock_quantity == 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($item->stock_quantity <= 10)
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('products.show', $item->id) }}">
                                                    <i class="ri-eye-line me-2"></i> View Details
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('products.edit', $item->id) }}">
                                                    <i class="ri-edit-line me-2"></i> Edit Product
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item" href="#" onclick="adjustStock({{ $item->id }})">
                                                    <i class="ri-add-circle-line me-2"></i> Adjust Stock
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="py-4">
                                            <i class="ri-archive-line display-4 text-muted"></i>
                                            <h5 class="mt-3">No Inventory Data</h5>
                                            <p class="text-muted">No inventory items found for the current filters.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportReport() {
    // Placeholder for export functionality
    alert('Export functionality will be implemented soon. This will generate a CSV/PDF report of the inventory data.');
}

function adjustStock(productId) {
    // Placeholder for stock adjustment
    alert('Stock adjustment functionality will be implemented soon. Product ID: ' + productId);
}
</script>
@endpush
@endsection
