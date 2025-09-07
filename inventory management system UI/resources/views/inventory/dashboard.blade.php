@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2 fw-semibold">📦 Inventory Dashboard</h2>
            <p class="mb-0 text-muted">Monitor and manage your inventory in real-time</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Add Product
            </a>
            <button type="button" class="btn btn-outline-secondary" onclick="runInventoryCheck()">
                <i class="fas fa-sync me-2"></i> Check Inventory
            </button>
            <a href="{{ route('inventory.export') }}?format=csv" class="btn btn-outline-success">
                <i class="fas fa-download me-2"></i> Export
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
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
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Low Stock Items</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-times-circle text-danger mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ number_format($stats['out_of_stock_products']) }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Out of Stock</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-dollar-sign text-success mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">${{ number_format($stats['total_inventory_value'], 2) }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Inventory Value</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Activities -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header py-3 px-4 d-flex flex-row align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold text-primary">Recent Inventory Activities</h5>
                    <a href="{{ route('inventory.logs') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentActivities as $activity)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <span class="badge badge-{{ $activity->type_color }} p-2">
                                                {{ $activity->type_icon }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">{{ $activity->product->name }}</div>
                                            <small class="text-muted">
                                                {{ ucfirst(str_replace('_', ' ', $activity->type)) }} - 
                                                {{ $activity->quantity_changed > 0 ? '+' : '' }}{{ $activity->quantity_changed }} units
                                                ({{ $activity->quantity_before }} → {{ $activity->quantity_after }})
                </div>
                <div class="card-body p-4">
                    @if($activities ?? false)
                        <div class="list-group list-group-flush">
                            @foreach($activities as $activity)
                                <div class="list-group-item border-0 px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $activity->description ?? 'Activity logged' }}</strong>
                                            <small class="text-muted d-block">
                                                {{ $activity->details ?? 'No additional details' }}
                                            </small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">No recent activities found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header py-2 px-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="mb-0 fw-semibold text-warning">Low Stock Alert</h6>
                    <span class="badge bg-warning">{{ $lowStockProducts->count() ?? 0 }} items</span>
                </div>
                <div class="card-body p-3">
                    @if(($lowStockProducts->count() ?? 0) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="py-2" style="font-size: 0.9rem;">Product</th>
                                        <th class="text-center py-2" style="font-size: 0.9rem;">Stock</th>
                                        <th class="text-center py-2" style="font-size: 0.9rem;">Min Level</th>
                                        <th class="text-center py-2" style="font-size: 0.9rem;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                        <tr>
                                            <td class="py-3">
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </td>
                                            <td class="text-center py-2">
                                                <span class="badge bg-danger" style="font-size: 0.9rem;">{{ $product->stock_quantity }}</span>
                                            </td>
                                            <td class="text-center py-2" style="font-size: 1rem;">{{ $product->minimum_stock_level }}</td>
                                            <td class="text-center py-2">
                                                @if($product->auto_reorder)
                                                    <span class="badge bg-success" title="Auto-reorder enabled">
                                                        <i class="fas fa-sync"></i>
                                                    </span>
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
    </div>
</div>
@endsection
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0 me-3">
                                            <span class="text-{{ $notification->type_color }}">{{ $notification->type_icon }}</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">{{ $notification->title }}</div>
                                            <p class="text-muted mb-1 small">{{ $notification->message }}</p>
                                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button class="btn btn-sm btn-outline-secondary" onclick="markAsRead({{ $notification->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">No new notifications.</p>
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
                            <a href="{{ route('inventory.logs') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-history"></i> Activity Logs
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <button class="btn btn-outline-warning btn-block" onclick="showAnalytics()">
                                <i class="fas fa-chart-bar"></i> Analytics
                            </button>
                        </div>
                    </div>
                    
                    @if($locations->count() > 0)
                        <hr>
                        <h6 class="font-weight-bold">Inventory Locations</h6>
                        <div class="mb-3">
                            @foreach($locations as $location)
                                <span class="badge badge-secondary mr-1 mb-1">
                                    <i class="fas fa-map-marker-alt"></i> {{ $location->location }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Modal -->
<div class="modal fade" id="analyticsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Inventory Analytics</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="analyticsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
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

function markAsRead(notificationId) {
    fetch(`{{ url('inventory/notifications') }}/${notificationId}/read`, {
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
    $('#analyticsModal').modal('show');
    
    fetch('{{ route("inventory.analytics") }}?days=30')
        .then(response => response.json())
        .then(data => {
            let html = '<div class="row">';
            
            // Stock movements chart placeholder
            html += '<div class="col-md-12 mb-4">';
            html += '<h6>Stock Movements (Last 30 Days)</h6>';
            html += '<div class="alert alert-info">Analytics charts will be implemented with Chart.js</div>';
            html += '</div>';
            
            // Top products by movement
            if (data.top_products.length > 0) {
                html += '<div class="col-md-12">';
                html += '<h6>Top Products by Movement</h6>';
                html += '<div class="table-responsive">';
                html += '<table class="table table-sm">';
                html += '<thead><tr><th>Product</th><th>Total Movement</th></tr></thead>';
                html += '<tbody>';
                
                data.top_products.forEach(item => {
                    html += `<tr><td>${item.product.name}</td><td>${item.total_movement}</td></tr>`;
                });
                
                html += '</tbody></table></div></div>';
            }
            
            html += '</div>';
            document.getElementById('analyticsContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('analyticsContent').innerHTML = '<div class="alert alert-danger">Failed to load analytics data</div>';
        });
}

// Auto-refresh dashboard every 5 minutes
setTimeout(() => {
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
