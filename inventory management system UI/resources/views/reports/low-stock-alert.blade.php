@extends('layouts.app')

@section('title', 'Low Stock Alert Report')

@section('content')
<style>
.section-spacing {
    margin-bottom: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #f59e0b 0%, #dc2626 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 10px;
}

.metric-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
}

.alert-card {
    border-left: 4px solid;
    margin-bottom: 1rem;
}

.alert-critical {
    border-left-color: #dc2626;
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
}

.alert-warning {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
}

.alert-info {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #dbeafe 0%, #93c5fd 100%);
}

.stock-level {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.stock-level-fill {
    height: 100%;
    transition: width 0.3s ease;
}

.stock-critical {
    background: #dc2626;
}

.stock-warning {
    background: #f59e0b;
}

.stock-good {
    background: #10b981;
}

.product-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
}

.urgency-badge {
    position: relative;
    overflow: hidden;
}

.urgency-badge.critical {
    background: #dc2626;
    animation: pulse 2s infinite;
}

.urgency-badge.warning {
    background: #f59e0b;
}

.urgency-badge.info {
    background: #3b82f6;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.reorder-recommendation {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
}

.chart-container {
    position: relative;
    height: 300px;
}
</style>

<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-2">
                    <i class="fas fa-exclamation-triangle me-3"></i>Low Stock Alert Report
                </h1>
                <p class="page-subtitle mb-0">Real-time inventory monitoring and reorder recommendations</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    <button class="btn btn-light" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                    <button class="btn btn-outline-light" onclick="refreshData()">
                        <i class="fas fa-sync me-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Threshold Filter -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-body">
                    <form method="GET" class="row align-items-center">
                        <div class="col-md-3">
                            <label for="threshold" class="form-label fw-bold">Low Stock Threshold:</label>
                            <input type="number" name="threshold" id="threshold" class="form-control" 
                                   value="{{ $threshold }}" min="0" max="100" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-9">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h4 class="text-danger mb-0">{{ $outOfStockProducts->count() }}</h4>
                                    <small class="text-muted">Out of Stock</small>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-warning mb-0">{{ $lowStockProducts->count() }}</h4>
                                    <small class="text-muted">Low Stock</small>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-info mb-0">{{ count($soonOutOfStock) }}</h4>
                                    <small class="text-muted">Soon Empty</small>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-success mb-0">{{ $lowStockProducts->count() + count($soonOutOfStock) }}</h4>
                                    <small class="text-muted">Need Reorder</small>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Critical Alerts -->
    @if($outOfStockProducts->count() > 0)
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>Out of Stock - Immediate Action Required
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($outOfStockProducts as $product)
                        <div class="col-lg-6 mb-3">
                            <div class="alert-card alert-critical">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="product-image me-3">
                                            {{ substr($product->name, 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <small class="text-muted">SKU: {{ $product->sku }}</small>
                                            <div class="mt-2">
                                                <span class="badge urgency-badge critical">
                                                    <i class="fas fa-exclamation"></i> CRITICAL
                                                </span>
                                                @if($product->supplier)
                                                <span class="badge bg-secondary ms-2">{{ $product->supplier->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="text-danger mb-0">{{ $product->quantity }}</h5>
                                            <small class="text-muted">In Stock</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Low Stock Products -->
    @if($lowStockProducts->count() > 0)
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Low Stock Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Stock Level</th>
                                    <th>Category</th>
                                    <th>Supplier</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="product-image me-3">
                                                {{ substr($product->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $product->quantity <= 5 ? 'bg-danger' : 'bg-warning' }}">
                                            {{ $product->quantity }}
                                        </span>
                                    </td>
                                    <td style="width: 150px;">
                                        @php
                                            $percentage = min(($product->quantity / $threshold) * 100, 100);
                                            $levelClass = $percentage <= 25 ? 'stock-critical' : ($percentage <= 50 ? 'stock-warning' : 'stock-good');
                                        @endphp
                                        <div class="stock-level">
                                            <div class="stock-level-fill {{ $levelClass }}" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($percentage, 1) }}%</small>
                                    </td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>{{ $product->supplier->name ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="quickReorder({{ $product->id }})">
                                            <i class="fas fa-shopping-cart"></i> Reorder
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Products Soon to be Empty -->
    @if(count($soonOutOfStock) > 0)
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Products Running Out Soon
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($soonOutOfStock as $item)
                        <div class="col-lg-6 mb-3">
                            <div class="alert-card alert-info">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="product-image me-3">
                                            {{ substr($item['product']->name, 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $item['product']->name }}</h6>
                                            <small class="text-muted">Current Stock: {{ $item['product']->quantity }}</small>
                                            <div class="mt-2">
                                                <span class="badge urgency-badge info">
                                                    {{ $item['days_until_empty'] }} days left
                                                </span>
                                                <small class="text-muted ms-2">
                                                    Avg: {{ $item['daily_sales_avg'] }}/day
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Reorder Recommendations -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-shopping-cart me-2"></i>Smart Reorder Recommendations
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($reorderRecommendations) > 0)
                    <div class="row">
                        @foreach($reorderRecommendations as $recommendation)
                        <div class="col-lg-6 mb-3">
                            <div class="reorder-recommendation">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">{{ $recommendation['product']->name }}</h6>
                                        <small class="text-muted">{{ $recommendation['supplier']->name ?? 'No supplier' }}</small>
                                    </div>
                                    <span class="badge bg-primary">Recommended</span>
                                </div>
                                
                                <div class="row text-center">
                                    <div class="col-3">
                                        <small class="text-muted">Current</small>
                                        <div class="fw-bold text-danger">{{ $recommendation['current_stock'] }}</div>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted">Monthly Sales</small>
                                        <div class="fw-bold">{{ $recommendation['monthly_sales'] }}</div>
                                    </div>
                                    <div class="col-3">
                                        <small class="text-muted">Recommended</small>
                                        <div class="fw-bold text-success">{{ $recommendation['recommended_order'] }}</div>
                                    </div>
                                    <div class="col-3">
                                        <button class="btn btn-sm btn-outline-primary" onclick="createPurchaseOrder({{ $recommendation['product']->id }}, {{ $recommendation['recommended_order'] }})">
                                            Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">All products are well stocked!</h5>
                        <p class="text-muted">No reorder recommendations at this time.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Distribution Chart -->
    <div class="row section-spacing">
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Stock Status Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="stockDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Stock Levels by Category
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryStockChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Stock Distribution Chart
const stockDistCtx = document.getElementById('stockDistributionChart').getContext('2d');
const stockDistChart = new Chart(stockDistCtx, {
    type: 'doughnut',
    data: {
        labels: ['Out of Stock', 'Low Stock', 'Soon Empty', 'Well Stocked'],
        datasets: [{
            data: [
                {{ $outOfStockProducts->count() }},
                {{ $lowStockProducts->count() }},
                {{ count($soonOutOfStock) }},
                {{ \App\Models\Product::where('quantity', '>', $threshold)->count() }}
            ],
            backgroundColor: [
                '#dc2626', // Red
                '#f59e0b', // Orange
                '#3b82f6', // Blue
                '#10b981'  // Green
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            }
        }
    }
});

// Category Stock Chart
const categoryData = @json(
    $lowStockProducts->groupBy('category.name')->map(function($products, $category) {
        return [
            'category' => $category ?: 'Uncategorized',
            'count' => $products->count()
        ];
    })->values()
);

const categoryStockCtx = document.getElementById('categoryStockChart').getContext('2d');
const categoryStockChart = new Chart(categoryStockCtx, {
    type: 'bar',
    data: {
        labels: categoryData.map(item => item.category),
        datasets: [{
            label: 'Low Stock Products',
            data: categoryData.map(item => item.count),
            backgroundColor: 'rgba(245, 158, 11, 0.8)',
            borderColor: '#f59e0b',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

function refreshData() {
    location.reload();
}

function exportToPDF() {
    alert('PDF export functionality will be implemented soon!');
}

function quickReorder(productId) {
    // This would integrate with your purchase order system
    alert(`Quick reorder functionality for product ID ${productId} will be implemented soon!`);
}

function createPurchaseOrder(productId, quantity) {
    // This would create a purchase order
    alert(`Creating purchase order for product ID ${productId} with quantity ${quantity}`);
}
</script>
@endsection