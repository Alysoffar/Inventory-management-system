@extends('layouts.app')

@section('title', 'Purchase Analysis Report')

@section('content')
<style>
.section-spacing {
    margin-bottom: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
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

.supplier-card {
    border-left: 4px solid;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.supplier-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.performance-excellent {
    border-left-color: #10b981;
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
}

.performance-good {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.performance-average {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.performance-poor {
    border-left-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.chart-container {
    position: relative;
    height: 350px;
}

.supplier-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(45deg, #7c3aed, #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
}

.trend-indicator {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: bold;
}

.trend-up {
    background: #dcfce7;
    color: #166534;
}

.trend-down {
    background: #fee2e2;
    color: #991b1b;
}

.trend-stable {
    background: #f3f4f6;
    color: #374151;
}

.frequency-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.frequency-high {
    background: #10b981;
}

.frequency-medium {
    background: #f59e0b;
}

.frequency-low {
    background: #ef4444;
}

.kpi-card {
    text-align: center;
    padding: 1.5rem;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    height: 100%;
}

.kpi-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
}

.date-range-form {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
}
</style>

<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-2">
                    <i class="fas fa-shopping-bag me-3"></i>Purchase Analysis Report
                </h1>
                <p class="page-subtitle mb-0">Comprehensive supplier performance and procurement insights</p>
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
    <!-- Date Range Filter -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-body">
                    <form method="GET" class="date-range-form">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label fw-bold">Start Date:</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" 
                                       value="{{ $startDate }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label fw-bold">End Date:</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" 
                                       value="{{ $endDate }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-6">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h4 class="text-primary mb-0">${{ number_format($totalPurchases, 2) }}</h4>
                                        <small class="text-muted">Total Purchases</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-info mb-0">{{ number_format($totalOrders) }}</h4>
                                        <small class="text-muted">Total Orders</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-success mb-0">${{ number_format($averageOrderValue, 2) }}</h4>
                                        <small class="text-muted">Avg Order Value</small>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-warning mb-0">{{ $supplierPerformance->count() }}</h4>
                                        <small class="text-muted">Active Suppliers</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Trends Chart -->
    <div class="row section-spacing">
        <div class="col-lg-8">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Monthly Purchase Trends
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="purchaseTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-pie-chart me-2"></i>Category Spending
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categorySpendingChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Performance -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Supplier Performance Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($supplierPerformance->take(6) as $supplier)
                        @php
                            $performance = 'average';
                            if ($supplier['total_purchased'] > 10000) {
                                $performance = 'excellent';
                            } elseif ($supplier['total_purchased'] > 5000) {
                                $performance = 'good';
                            } elseif ($supplier['total_purchased'] < 1000) {
                                $performance = 'poor';
                            }
                            
                            $daysBetween = $averageDaysBetweenPurchases[$supplier['supplier']->name] ?? 0;
                            $frequency = $daysBetween < 30 ? 'high' : ($daysBetween < 60 ? 'medium' : 'low');
                        @endphp
                        <div class="col-lg-6 mb-3">
                            <div class="card supplier-card performance-{{ $performance }}">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="supplier-avatar me-3">
                                            {{ substr($supplier['supplier']->name, 0, 1) }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="card-title mb-1">{{ $supplier['supplier']->name }}</h6>
                                            <small class="text-muted">{{ $supplier['products_supplied'] }} products supplied</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="frequency-indicator frequency-{{ $frequency }}"></span>
                                            <small class="text-muted">{{ number_format($daysBetween, 1) }} days avg</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Total Spent</small>
                                            <div class="fw-bold text-primary">${{ number_format($supplier['total_purchased'], 0) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Orders</small>
                                            <div class="fw-bold text-info">{{ $supplier['order_count'] }}</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Avg Order</small>
                                            <div class="fw-bold text-success">${{ number_format($supplier['average_order'], 0) }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">Last Order: </small>
                                        <small class="fw-bold">
                                            {{ $supplier['last_order'] ? \Carbon\Carbon::parse($supplier['last_order'])->format('M d, Y') : 'No orders' }}
                                        </small>
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

    <!-- Detailed Purchase Table -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>Supplier Performance Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Supplier</th>
                                    <th>Total Purchased</th>
                                    <th>Orders</th>
                                    <th>Avg Order Value</th>
                                    <th>Last Order</th>
                                    <th>Purchase Frequency</th>
                                    <th>Products</th>
                                    <th>Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($supplierPerformance as $supplier)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="supplier-avatar me-3" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                                {{ substr($supplier['supplier']->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $supplier['supplier']->name }}</div>
                                                <small class="text-muted">{{ $supplier['supplier']->email ?? 'No email' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary">${{ number_format($supplier['total_purchased'], 2) }}</td>
                                    <td>{{ $supplier['order_count'] }}</td>
                                    <td>${{ number_format($supplier['average_order'], 2) }}</td>
                                    <td>
                                        {{ $supplier['last_order'] ? \Carbon\Carbon::parse($supplier['last_order'])->format('M d, Y') : 'No orders' }}
                                    </td>
                                    <td>
                                        @php
                                            $daysBetween = $averageDaysBetweenPurchases[$supplier['supplier']->name] ?? 0;
                                            $frequency = $daysBetween < 30 ? 'high' : ($daysBetween < 60 ? 'medium' : 'low');
                                        @endphp
                                        <span class="frequency-indicator frequency-{{ $frequency }}"></span>
                                        {{ number_format($daysBetween, 1) }} days
                                    </td>
                                    <td>{{ $supplier['products_supplied'] }}</td>
                                    <td>
                                        @php
                                            $rating = 3; // Default
                                            if ($supplier['total_purchased'] > 10000) $rating = 5;
                                            elseif ($supplier['total_purchased'] > 5000) $rating = 4;
                                            elseif ($supplier['total_purchased'] < 1000) $rating = 2;
                                        @endphp
                                        
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
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

    <!-- Category Analysis -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>Category Purchase Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryPurchases as $category)
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body text-center">
                                    <h6 class="card-title">{{ $category['category'] }}</h6>
                                    <h4 class="text-primary mb-2">${{ number_format($category['total_spent'], 2) }}</h4>
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted">Items</small>
                                            <div class="fw-bold">{{ $category['items_count'] }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Avg Cost</small>
                                            <div class="fw-bold">${{ number_format($category['avg_cost'], 2) }}</div>
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

    <!-- Key Insights -->
    @if($topSupplier)
    <div class="row section-spacing">
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>Top Supplier
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="supplier-avatar mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                            {{ substr($topSupplier['supplier']->name, 0, 1) }}
                        </div>
                        <h5>{{ $topSupplier['supplier']->name }}</h5>
                        <p class="text-muted mb-3">{{ $topSupplier['products_supplied'] }} products supplied</p>
                        <div class="row">
                            <div class="col-6">
                                <h6 class="text-success">${{ number_format($topSupplier['total_purchased'], 2) }}</h6>
                                <small class="text-muted">Total Purchased</small>
                            </div>
                            <div class="col-6">
                                <h6 class="text-info">{{ $topSupplier['order_count'] }}</h6>
                                <small class="text-muted">Orders Placed</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Purchase Insights
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Most Purchased Category</span>
                            <strong>{{ $categoryPurchases->first()['category'] ?? 'N/A' }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Average Days Between Orders</span>
                            <strong>{{ number_format(collect($averageDaysBetweenPurchases)->avg(), 1) }} days</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Monthly Purchase Average</span>
                            <strong>${{ number_format(collect($monthlyPurchases)->avg('total'), 2) }}</strong>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between">
                            <span>Active Suppliers</span>
                            <strong>{{ $supplierPerformance->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Purchase Trend Chart
const trendCtx = document.getElementById('purchaseTrendChart').getContext('2d');
const trendChart = new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($monthlyPurchases, 'month')),
        datasets: [{
            label: 'Total Purchases',
            data: @json(array_column($monthlyPurchases, 'total')),
            borderColor: '#7c3aed',
            backgroundColor: 'rgba(124, 58, 237, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#7c3aed',
            pointBorderColor: '#7c3aed',
            pointRadius: 5,
            pointHoverRadius: 8
        }, {
            label: 'Order Count',
            data: @json(array_column($monthlyPurchases, 'count')),
            borderColor: '#f59e0b',
            backgroundColor: 'rgba(245, 158, 11, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + new Intl.NumberFormat().format(value);
                    }
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Category Spending Chart
const categorySpendingData = @json($categoryPurchases->take(8)->values());
const categoryCtx = document.getElementById('categorySpendingChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: categorySpendingData.map(item => item.category),
        datasets: [{
            data: categorySpendingData.map(item => item.total_spent),
            backgroundColor: [
                '#7c3aed', '#3b82f6', '#10b981', '#f59e0b',
                '#ef4444', '#06b6d4', '#84cc16', '#f97316'
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
                    padding: 15,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: $${new Intl.NumberFormat().format(value)} (${percentage}%)`;
                    }
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
</script>
@endsection