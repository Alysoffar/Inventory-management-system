@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">📊 Inventory Dashboard</h1>
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-primary btn-sm" onclick="runInventoryCheck()">
                <i class="fas fa-sync"></i> Run Inventory Check
            </button>
            <button type="button" class="btn btn-info btn-sm" onclick="showAnalytics()">
                <i class="fas fa-chart-line"></i> Analytics
            </button>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Products -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalProducts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Low Stock Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lowStockProducts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Out of Stock Products -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Out of Stock</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $outOfStockProducts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Inventory Value -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Inventory Value</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($totalInventoryValue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Inventory Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Inventory Activity</h6>
                </div>
                <div class="card-body">
                    @if($recentLogs && $recentLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Action</th>
                                        <th>Quantity</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLogs as $log)
                                        <tr>
                                            <td>{{ $log->product ? $log->product->name : 'Unknown Product' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $log->action === 'restock' ? 'success' : ($log->action === 'sale' ? 'primary' : 'secondary') }}">
                                                    {{ ucfirst($log->action) }}
                                                </span>
                                            </td>
                                            <td>{{ $log->quantity_changed }}</td>
                                            <td>{{ $log->created_at ? $log->created_at->diffForHumans() : 'Unknown' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted mb-2">No recent activity</h5>
                            <p class="text-muted mb-0">Inventory changes will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">Low Stock Alert</h6>
                </div>
                <div class="card-body">
                    @if($lowStockItems && $lowStockItems->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>Min Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockItems as $product)
                                        <tr>
                                            <td>{{ $product->name }}</td>
                                            <td>
                                                <span class="badge badge-{{ $product->stock_quantity <= 0 ? 'danger' : 'warning' }}">
                                                    {{ $product->stock_quantity }}
                                                </span>
                                            </td>
                                            <td>{{ $product->minimum_stock_level }}</td>
                                            <td>
                                                @if($product->auto_reorder)
                                                    <span class="badge badge-success">Auto Reorder</span>
                                                @else
                                                    <button class="btn btn-sm btn-outline-primary" onclick="manualRestock({{ $product->id }})" title="Manual restock">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success mb-2">All products are well-stocked!</h5>
                            <p class="text-muted mb-0">Your inventory levels are healthy.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Notifications</h6>
                </div>
                <div class="card-body">
                    @if($notifications && $notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <span class="text-{{ $notification->type_color ?? 'info' }}">{{ $notification->type_icon ?? '🔔' }}</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">{{ $notification->title ?? 'Notification' }}</div>
                                            <p class="text-muted mb-1 small">{{ $notification->message ?? 'No message' }}</p>
                                            <small class="text-muted">{{ $notification->created_at ? $notification->created_at->diffForHumans() : 'Unknown time' }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="markAsRead({{ $notification->id ?? 0 }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No new notifications.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-list"></i> View All Products
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('inventory.map') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-map"></i> Inventory Map
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('products.create') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-plus"></i> Add Product
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-outline-warning btn-block" onclick="runInventoryCheck()">
                                <i class="fas fa-sync"></i> Inventory Check
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function runInventoryCheck() {
    if (confirm('This will check all inventory levels and trigger auto-restock where needed. Continue?')) {
        fetch('{{ route("inventory.check") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Failed to run inventory check');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while running inventory check');
        });
    }
}

function manualRestock(productId) {
    if (confirm('Trigger manual restock for this product?')) {
        const url = '/products/' + productId + '/manual-restock';
        fetch(url, {
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

function markAsRead(notificationId) {
    const url = '{{ url("inventory/notifications") }}/' + notificationId + '/read';
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function showAnalytics() {
    alert('Analytics feature coming soon!');
}

// Auto-refresh dashboard every 5 minutes
setTimeout(function() {
    location.reload();
}, 300000);
</script>
@endsection

@section('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.badge-red { background-color: #dc3545; }
.badge-green { background-color: #28a745; }
.badge-blue { background-color: #007bff; }
.badge-yellow { background-color: #ffc107; color: #212529; }
.badge-orange { background-color: #fd7e14; }
.badge-gray { background-color: #6c757d; }
</style>
@endsection
