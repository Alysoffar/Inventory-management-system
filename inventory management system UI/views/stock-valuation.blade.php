@extends('layouts.app')

@section('title', 'Stock Valuation Report')

@section('content')
<style>
.section-spacing {
    margin-bottom: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
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

.valuation-summary {
    background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
    border: 1px solid #10b981;
    border-radius: 10px;
    padding: 2rem;
}

.profit-indicator {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: bold;
}

.profit-high {
    background: #dcfce7;
    color: #166534;
}

.profit-medium {
    background: #fef3c7;
    color: #92400e;
}

.profit-low {
    background: #fee2e2;
    color: #991b1b;
}

.turnover-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 8px;
}

.turnover-high {
    background: #10b981;
}

.turnover-medium {
    background: #f59e0b;
}

.turnover-low {
    background: #ef4444;
}

.category-card {
    border-left: 4px solid;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.category-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.chart-container {
    position: relative;
    height: 350px;
}

.value-display {
    font-size: 1.5rem;
    font-weight: bold;
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
</style>

<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-2">
                    <i class="fas fa-coins me-3"></i>Stock Valuation Report
                </h1>
                <p class="page-subtitle mb-0">Comprehensive inventory valuation and profitability analysis</p>
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
    <!-- Valuation Summary -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="valuation-summary">
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-primary">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h3 class="text-primary value-display">${{ number_format($totalValue, 2) }}</h3>
                            <p class="text-muted mb-0">Total Stock Value</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-info">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="text-info value-display">${{ number_format($totalCost, 2) }}</h3>
                            <p class="text-muted mb-0">Total Stock Cost</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-success">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <h3 class="text-success value-display">${{ number_format($totalPotentialProfit, 2) }}</h3>
                            <p class="text-muted mb-0">Potential Profit</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-warning">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <h3 class="text-warning value-display">{{ number_format($overallMargin, 1) }}%</h3>
                            <p class="text-muted mb-0">Overall Margin</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Valuation -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>Category Valuation Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryValuation as $category)
                        @php
                            $categoryMargin = $category['cost'] > 0 ? (($category['value'] - $category['cost']) / $category['cost']) * 100 : 0;
                            $borderColor = $categoryMargin > 50 ? '#10b981' : ($categoryMargin > 20 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <div class="col-lg-6 mb-3">
                            <div class="card category-card" style="border-left-color: {{ $borderColor }};">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $category['category'] }}</h6>
                                            <small class="text-muted">{{ $category['products_count'] }} products</small>
                                        </div>
                                        <span class="profit-indicator {{ $categoryMargin > 50 ? 'profit-high' : ($categoryMargin > 20 ? 'profit-medium' : 'profit-low') }}">
                                            {{ number_format($categoryMargin, 1) }}%
                                        </span>
                                    </div>
                                    
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Value</small>
                                            <div class="fw-bold text-primary">${{ number_format($category['value'], 0) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Cost</small>
                                            <div class="fw-bold text-info">${{ number_format($category['cost'], 0) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Profit</small>
                                            <div class="fw-bold text-success">${{ number_format($category['profit'], 0) }}</div>
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

    <!-- Charts Row -->
    <div class="row section-spacing">
        <div class="col-lg-8">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Top Products by Value
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-pie-chart me-2"></i>Value Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="valueDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Product Valuation -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>Detailed Product Valuation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>Stock Qty</th>
                                    <th>Unit Cost</th>
                                    <th>Unit Price</th>
                                    <th>Stock Value</th>
                                    <th>Stock Cost</th>
                                    <th>Potential Profit</th>
                                    <th>Margin %</th>
                                    <th>Turnover</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(collect($valuationData)->take(20) as $item)
                                <tr>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $item['product']->name }}</div>
                                            <small class="text-muted">{{ $item['product']->sku }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $item['product']->stock_quantity }}</td>
                                    <td>${{ number_format($item['product']->cost_price, 2) }}</td>
                                    <td>${{ number_format($item['product']->price, 2) }}</td>
                                    <td class="fw-bold text-primary">${{ number_format($item['stock_value'], 2) }}</td>
                                    <td class="text-info">${{ number_format($item['stock_cost'], 2) }}</td>
                                    <td class="text-success">${{ number_format($item['potential_profit'], 2) }}</td>
                                    <td>
                                        <span class="profit-indicator {{ $item['profit_margin'] > 50 ? 'profit-high' : ($item['profit_margin'] > 20 ? 'profit-medium' : 'profit-low') }}">
                                            {{ number_format($item['profit_margin'], 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="turnover-indicator {{ $item['turnover_ratio'] > 1 ? 'turnover-high' : ($item['turnover_ratio'] > 0.5 ? 'turnover-medium' : 'turnover-low') }}"></span>
                                        {{ number_format($item['turnover_ratio'], 2) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if(count($valuationData) > 20)
                    <div class="text-center mt-3">
                        <p class="text-muted">Showing top 20 products. Total: {{ count($valuationData) }} products</p>
                        <button class="btn btn-outline-primary" onclick="showAllProducts()">
                            View All Products
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Key Insights -->
    <div class="row section-spacing">
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Dead Stock Alert
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-warning">${{ number_format($deadStock, 2) }}</h3>
                        <p class="text-muted">Value tied up in slow-moving inventory</p>
                        
                        @php
                            $deadStockProducts = collect($valuationData)->where('turnover_ratio', 0)->count();
                        @endphp
                        <p><strong>{{ $deadStockProducts }}</strong> products with zero turnover</p>
                        
                        @if($deadStock > 0)
                        <button class="btn btn-warning" onclick="viewDeadStock()">
                            <i class="fas fa-eye me-2"></i>View Dead Stock
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Top Performers
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $topByMargin = collect($valuationData)->sortByDesc('profit_margin')->take(3);
                    @endphp
                    
                    @foreach($topByMargin as $index => $item)
                    <div class="d-flex justify-content-between align-items-center {{ $index < 2 ? 'mb-3' : '' }}">
                        <div>
                            <div class="fw-bold">{{ $item['product']->name }}</div>
                            <small class="text-muted">Margin: {{ number_format($item['profit_margin'], 1) }}%</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">${{ number_format($item['potential_profit'], 0) }}</div>
                            <small class="text-muted">Potential</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Top Products Chart
const topProductsData = @json(collect($valuationData)->take(10)->values());
const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
const topProductsChart = new Chart(topProductsCtx, {
    type: 'bar',
    data: {
        labels: topProductsData.map(item => item.product.name.substring(0, 15) + (item.product.name.length > 15 ? '...' : '')),
        datasets: [{
            label: 'Stock Value',
            data: topProductsData.map(item => item.stock_value),
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: '#10b981',
            borderWidth: 1
        }, {
            label: 'Stock Cost',
            data: topProductsData.map(item => item.stock_cost),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: '#3b82f6',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': $' + new Intl.NumberFormat().format(context.parsed.y);
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + new Intl.NumberFormat().format(value);
                    }
                }
            }
        }
    }
});

// Value Distribution Chart
const categoryData = @json(collect($categoryValuation)->take(8)->values());
const valueDistCtx = document.getElementById('valueDistributionChart').getContext('2d');
const valueDistChart = new Chart(valueDistCtx, {
    type: 'doughnut',
    data: {
        labels: categoryData.map(item => item.category),
        datasets: [{
            data: categoryData.map(item => item.value),
            backgroundColor: [
                '#10b981', '#3b82f6', '#f59e0b', '#ef4444',
                '#8b5cf6', '#06b6d4', '#84cc16', '#f97316'
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

function showAllProducts() {
    alert('View all products functionality will be implemented soon!');
}

function viewDeadStock() {
    alert('Dead stock analysis functionality will be implemented soon!');
}
</script>
@endsection