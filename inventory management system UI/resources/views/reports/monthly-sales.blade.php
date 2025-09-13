@extends('layouts.app')

@section('title', 'Monthly Sales Report')

@section('content')
<style>
.section-spacing {
    margin-bottom: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

.chart-container {
    position: relative;
    height: 400px;
}

.insight-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border-radius: 10px;
    padding: 1.5rem;
}

.growth-positive {
    color: #10b981 !important;
}

.growth-negative {
    color: #ef4444 !important;
}

.growth-neutral {
    color: #6b7280 !important;
}
</style>

<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-2">
                    <i class="fas fa-calendar-alt me-3"></i>Monthly Sales Report - {{ $year }}
                </h1>
                <p class="page-subtitle mb-0">Comprehensive monthly sales analysis and trends</p>
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
    <!-- Year Selector -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-body">
                    <form method="GET" class="row align-items-center">
                        <div class="col-md-4">
                            <label for="year" class="form-label fw-bold">Select Year:</label>
                            <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                                @for($y = now()->year; $y >= 2020; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <small class="text-muted">Total Sales</small>
                                    <h4 class="text-primary mb-0">${{ number_format($totalYearSales, 2) }}</h4>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Growth Rate</small>
                                    <h4 class="mb-0 {{ $yearGrowth > 0 ? 'growth-positive' : ($yearGrowth < 0 ? 'growth-negative' : 'growth-neutral') }}">
                                        {{ number_format($yearGrowth, 1) }}%
                                    </h4>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Monthly Average</small>
                                    <h4 class="text-info mb-0">${{ number_format($averageMonthlySales, 2) }}</h4>
                                </div>
                                <div class="col-md-3">
                                    <small class="text-muted">Best Month</small>
                                    <h4 class="text-success mb-0">{{ $bestMonth['month'] ?? 'N/A' }}</h4>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Sales Chart -->
    <div class="row section-spacing">
        <div class="col-lg-8">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Monthly Sales Trend
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="insight-card h-100">
                <h5 class="mb-3">
                    <i class="fas fa-lightbulb me-2"></i>Key Insights
                </h5>
                <div class="insight-item mb-3">
                    <small class="opacity-75">Best Performing Month</small>
                    <h6 class="mb-1">{{ $bestMonth['month'] ?? 'N/A' }}</h6>
                    <small>${{ number_format($bestMonth['sales'] ?? 0, 2) }}</small>
                </div>
                <div class="insight-item mb-3">
                    <small class="opacity-75">Lowest Performing Month</small>
                    <h6 class="mb-1">{{ $worstMonth['month'] ?? 'N/A' }}</h6>
                    <small>${{ number_format($worstMonth['sales'] ?? 0, 2) }}</small>
                </div>
                <div class="insight-item mb-3">
                    <small class="opacity-75">Year-over-Year Growth</small>
                    <h6 class="mb-1 {{ $yearGrowth > 0 ? 'text-light' : 'text-warning' }}">
                        {{ number_format($yearGrowth, 1) }}%
                    </h6>
                </div>
                <div class="insight-item">
                    <small class="opacity-75">Monthly Consistency</small>
                    <h6 class="mb-1">
                        @php
                            $salesValues = collect($monthlySales)->pluck('sales')->filter(function($v) { return $v > 0; });
                            $consistency = $salesValues->count() > 1 ? 
                                (1 - ($salesValues->std() / $salesValues->avg())) * 100 : 100;
                        @endphp
                        {{ number_format($consistency, 1) }}%
                    </h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Table -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>Monthly Sales Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Month</th>
                                    <th>Sales ({{ $year }})</th>
                                    <th>Sales ({{ $year - 1 }})</th>
                                    <th>Growth</th>
                                    <th>Top Product</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlySales as $index => $month)
                                <tr>
                                    <td class="fw-bold">{{ $month['month'] }}</td>
                                    <td>${{ number_format($month['sales'], 2) }}</td>
                                    <td class="text-muted">${{ number_format($month['previous_sales'], 2) }}</td>
                                    <td>
                                        <span class="badge {{ $month['growth'] > 0 ? 'bg-success' : ($month['growth'] < 0 ? 'bg-danger' : 'bg-secondary') }}">
                                            {{ number_format($month['growth'], 1) }}%
                                        </span>
                                    </td>
                                    <td>{{ $monthlyTopProducts[$index]['product'] ?? 'N/A' }}</td>
                                    <td>${{ number_format($monthlyTopProducts[$index]['revenue'] ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Comparison Chart -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Year-over-Year Comparison
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="comparisonChart"></canvas>
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
// Monthly Sales Chart
const monthlySalesCtx = document.getElementById('monthlySalesChart').getContext('2d');
const monthlySalesChart = new Chart(monthlySalesCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($monthlySales, 'month')),
        datasets: [{
            label: 'Sales {{ $year }}',
            data: @json(array_column($monthlySales, 'sales')),
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#667eea',
            pointBorderColor: '#667eea',
            pointRadius: 5,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                mode: 'index',
                intersect: false,
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
        },
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        }
    }
});

// Year-over-Year Comparison Chart
const comparisonCtx = document.getElementById('comparisonChart').getContext('2d');
const comparisonChart = new Chart(comparisonCtx, {
    type: 'bar',
    data: {
        labels: @json(array_column($monthlySales, 'month')),
        datasets: [{
            label: '{{ $year }}',
            data: @json(array_column($monthlySales, 'sales')),
            backgroundColor: 'rgba(102, 126, 234, 0.8)',
            borderColor: '#667eea',
            borderWidth: 1
        }, {
            label: '{{ $year - 1 }}',
            data: @json(array_column($monthlySales, 'previous_sales')),
            backgroundColor: 'rgba(156, 163, 175, 0.6)',
            borderColor: '#9ca3af',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
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