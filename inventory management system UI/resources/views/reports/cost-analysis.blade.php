@extends('layouts.app')

@section('title', 'Cost Analysis Report')

@section('content')
<style>
.section-spacing {
    margin-bottom: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
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

.profit-summary {
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
    border: 1px solid #dc2626;
    border-radius: 10px;
    padding: 2rem;
}

.margin-indicator {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: bold;
}

.margin-excellent {
    background: #dcfce7;
    color: #166534;
}

.margin-good {
    background: #dbeafe;
    color: #1e40af;
}

.margin-fair {
    background: #fef3c7;
    color: #92400e;
}

.margin-poor {
    background: #fee2e2;
    color: #991b1b;
}

.cost-category {
    border-left: 4px solid;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.cost-category:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.cost-product {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.cost-operational {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.cost-fixed {
    border-left-color: #6b7280;
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
}

.chart-container {
    position: relative;
    height: 350px;
}

.product-avatar {
    width: 45px;
    height: 45px;
    border-radius: 8px;
    background: linear-gradient(45deg, #dc2626, #b91c1c);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.1rem;
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

.profitability-gauge {
    width: 120px;
    height: 120px;
    margin: 0 auto;
    position: relative;
}

.gauge-background {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: conic-gradient(
        #ef4444 0deg 90deg,
        #f59e0b 90deg 180deg,
        #10b981 180deg 270deg,
        #059669 270deg 360deg
    );
    display: flex;
    align-items: center;
    justify-content: center;
}

.gauge-inner {
    width: 90px;
    height: 90px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.breakeven-progress {
    width: 100%;
    height: 20px;
    background: #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.breakeven-fill {
    height: 100%;
    background: linear-gradient(90deg, #ef4444, #f59e0b, #10b981);
    transition: width 0.3s ease;
}

.operational-cost-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}
</style>

<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-2">
                    <i class="fas fa-calculator me-3"></i>Cost Analysis Report
                </h1>
                <p class="page-subtitle mb-0">Comprehensive cost structure and profitability analysis</p>
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
    <!-- Financial Summary -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="profit-summary">
                <div class="row">
                    <div class="col-lg-2 col-md-4 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-primary">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h4 class="text-primary mb-1">${{ number_format($totalRevenue, 2) }}</h4>
                            <p class="text-muted mb-0">Total Revenue</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-info">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4 class="text-info mb-1">${{ number_format($totalProductCosts, 2) }}</h4>
                            <p class="text-muted mb-0">Product Costs</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-warning">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h4 class="text-warning mb-1">${{ number_format($totalOperatingCosts, 2) }}</h4>
                            <p class="text-muted mb-0">Operating Costs</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon bg-success">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h4 class="text-success mb-1">${{ number_format($grossProfit, 2) }}</h4>
                            <p class="text-muted mb-0">Gross Profit</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 mb-3">
                        <div class="kpi-card">
                            <div class="kpi-icon {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }}">
                                <i class="fas fa-{{ $netProfit >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                            </div>
                            <h4 class="text-{{ $netProfit >= 0 ? 'success' : 'danger' }} mb-1">${{ number_format($netProfit, 2) }}</h4>
                            <p class="text-muted mb-0">Net Profit</p>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-4 mb-3">
                        <div class="kpi-card">
                            <div class="profitability-gauge">
                                <div class="gauge-background">
                                    <div class="gauge-inner">
                                        <h5 class="mb-0 {{ $netMargin >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netMargin, 1) }}%</h5>
                                        <small class="text-muted">Net Margin</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Break-even Analysis -->
    <div class="row section-spacing">
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-balance-scale me-2"></i>Break-even Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Current Revenue</span>
                            <strong>${{ number_format($totalRevenue, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Break-even Revenue</span>
                            <strong>${{ number_format($breakEvenRevenue, 2) }}</strong>
                        </div>
                        @php
                            $breakEvenProgress = $breakEvenRevenue > 0 ? min(($totalRevenue / $breakEvenRevenue) * 100, 100) : 100;
                        @endphp
                        <div class="breakeven-progress mt-3">
                            <div class="breakeven-fill" style="width: {{ $breakEvenProgress }}%"></div>
                        </div>
                        <small class="text-muted">{{ number_format($breakEvenProgress, 1) }}% of break-even target</small>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="text-{{ $totalRevenue >= $breakEvenRevenue ? 'success' : 'warning' }}">
                                {{ $totalRevenue >= $breakEvenRevenue ? 'Profitable' : 'Needs Improvement' }}
                            </h6>
                            <small class="text-muted">Current Status</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-info">
                                ${{ number_format(max($breakEvenRevenue - $totalRevenue, 0), 2) }}
                            </h6>
                            <small class="text-muted">Revenue Needed</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Cost Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="costBreakdownChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operating Costs Breakdown -->
    <div class="row section-spacing">
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>Operating Costs Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($operatingCosts as $costType => $amount)
                    <div class="operational-cost-item">
                        <div>
                            <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $costType)) }}</span>
                        </div>
                        <div>
                            <span class="fw-bold text-primary">${{ number_format($amount, 2) }}</span>
                            <small class="text-muted">({{ number_format(($amount / $totalOperatingCosts) * 100, 1) }}%)</small>
                        </div>
                    </div>
                    @endforeach
                    
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total Operating Costs</span>
                            <span class="fw-bold text-warning">${{ number_format($totalOperatingCosts, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Category Profitability
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryProfitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Cost Analysis -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-boxes me-2"></i>Product Cost Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Product</th>
                                    <th>Unit Cost</th>
                                    <th>Unit Price</th>
                                    <th>Unit Margin</th>
                                    <th>Margin %</th>
                                    <th>Total Sold</th>
                                    <th>Total Revenue</th>
                                    <th>Total Profit</th>
                                    <th>Stock Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(collect($costAnalysis)->take(15) as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="product-avatar me-3">
                                                {{ substr($item['product']->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $item['product']->name }}</div>
                                                <small class="text-muted">{{ $item['product']->sku }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>${{ number_format($item['unit_cost'], 2) }}</td>
                                    <td>${{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="text-{{ $item['unit_margin'] >= 0 ? 'success' : 'danger' }}">
                                        ${{ number_format($item['unit_margin'], 2) }}
                                    </td>
                                    <td>
                                        <span class="margin-indicator 
                                            @if($item['margin_percentage'] >= 50) margin-excellent
                                            @elseif($item['margin_percentage'] >= 30) margin-good
                                            @elseif($item['margin_percentage'] >= 15) margin-fair
                                            @else margin-poor
                                            @endif">
                                            {{ number_format($item['margin_percentage'], 1) }}%
                                        </span>
                                    </td>
                                    <td>{{ number_format($item['total_sold']) }}</td>
                                    <td class="fw-bold text-primary">${{ number_format($item['total_revenue'], 2) }}</td>
                                    <td class="fw-bold text-{{ $item['total_profit'] >= 0 ? 'success' : 'danger' }}">
                                        ${{ number_format($item['total_profit'], 2) }}
                                    </td>
                                    <td>${{ number_format($item['stock_value'], 2) }}</td>
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
                        <i class="fas fa-layer-group me-2"></i>Category Cost Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categoryCosts as $category)
                        <div class="col-lg-6 mb-3">
                            <div class="card cost-category cost-product">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $category['category'] }}</h6>
                                            <small class="text-muted">{{ $category['products_count'] }} products</small>
                                        </div>
                                        <span class="margin-indicator 
                                            @if($category['avg_margin'] >= 50) margin-excellent
                                            @elseif($category['avg_margin'] >= 30) margin-good
                                            @elseif($category['avg_margin'] >= 15) margin-fair
                                            @else margin-poor
                                            @endif">
                                            {{ number_format($category['avg_margin'], 1) }}%
                                        </span>
                                    </div>
                                    
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted">Revenue</small>
                                            <div class="fw-bold text-primary">${{ number_format($category['total_revenue'], 0) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Costs</small>
                                            <div class="fw-bold text-info">${{ number_format($category['total_cost'], 0) }}</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Profit</small>
                                            <div class="fw-bold text-{{ $category['total_profit'] >= 0 ? 'success' : 'danger' }}">
                                                ${{ number_format($category['total_profit'], 0) }}
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
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Cost Breakdown Chart
const costBreakdownCtx = document.getElementById('costBreakdownChart').getContext('2d');
const costBreakdownChart = new Chart(costBreakdownCtx, {
    type: 'doughnut',
    data: {
        labels: ['Product Costs', 'Operating Costs', 'Net Profit'],
        datasets: [{
            data: [
                {{ $totalProductCosts }},
                {{ $totalOperatingCosts }},
                {{ max($netProfit, 0) }}
            ],
            backgroundColor: [
                '#3b82f6', // Blue for product costs
                '#f59e0b', // Orange for operating costs
                '#10b981'  // Green for profit
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
                        const total = {{ $totalRevenue }};
                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                        return `${label}: $${new Intl.NumberFormat().format(value)} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Category Profit Chart
const categoryProfitData = @json($categoryCosts->take(6)->values());
const categoryProfitCtx = document.getElementById('categoryProfitChart').getContext('2d');
const categoryProfitChart = new Chart(categoryProfitCtx, {
    type: 'bar',
    data: {
        labels: categoryProfitData.map(item => item.category),
        datasets: [{
            label: 'Revenue',
            data: categoryProfitData.map(item => item.total_revenue),
            backgroundColor: 'rgba(59, 130, 246, 0.8)',
            borderColor: '#3b82f6',
            borderWidth: 1
        }, {
            label: 'Costs',
            data: categoryProfitData.map(item => item.total_cost),
            backgroundColor: 'rgba(245, 158, 11, 0.8)',
            borderColor: '#f59e0b',
            borderWidth: 1
        }, {
            label: 'Profit',
            data: categoryProfitData.map(item => item.total_profit),
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: '#10b981',
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

function refreshData() {
    location.reload();
}

function exportToPDF() {
    alert('PDF export functionality will be implemented soon!');
}
</script>
@endsection