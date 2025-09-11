@extends('layouts.app')

@section('title', 'Profit & Loss Report with AI Predictions')

@section('content')
<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-3"></i>Profit & Loss Report
            </h1>
            <p class="page-subtitle">Comprehensive financial analysis with AI-powered predictions</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <button class="btn btn-outline-primary" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </button>
                <button class="btn btn-success" onclick="refreshData()">
                    <i class="fas fa-sync me-2"></i>Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Date Range Filter -->
<div class="row section-spacing">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('reports.profit-loss') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Summary Cards -->
<div class="row section-spacing">
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x mb-3 text-success"></i>
                <h3 class="text-success">${{ number_format($currentPeriod['revenue'], 2) }}</h3>
                <p class="mb-1">Total Revenue</p>
                <small class="text-dark">Current Period</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-chart-bar fa-2x mb-3 text-primary"></i>
                <h3 class="text-primary">${{ number_format($currentPeriod['gross_profit'], 2) }}</h3>
                <p class="mb-1">Gross Profit</p>
                <small class="text-dark">{{ number_format($currentPeriod['margin_percentage'], 1) }}% Margin</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-coins fa-2x mb-3 text-warning"></i>
                <h3 class="text-warning">${{ number_format($currentPeriod['net_profit'], 2) }}</h3>
                <p class="mb-1">Net Profit</p>
                <small class="text-dark">After expenses</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-robot fa-2x mb-3 text-info"></i>
                <h3 class="text-info">${{ number_format($aiPredictions['predicted_net_profit'], 2) }}</h3>
                <p class="mb-1">AI Predicted Profit</p>
                <small class="text-dark">{{ number_format($aiPredictions['confidence_level'], 1) }}% Confidence</small>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row section-spacing">
    <!-- P&L Statement -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Profit & Loss Statement
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-end">Current Period</th>
                                <th class="text-end">Previous Period</th>
                                <th class="text-end">Change</th>
                                <th class="text-end">AI Prediction</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Revenue</strong></td>
                                <td class="text-end">${{ number_format($currentPeriod['revenue'], 2) }}</td>
                                <td class="text-end">${{ number_format($previousPeriod['revenue'], 2) }}</td>
                                <td class="text-end text-success">
                                    +${{ number_format($currentPeriod['revenue'] - $previousPeriod['revenue'], 2) }}
                                </td>
                                <td class="text-end text-info">${{ number_format($aiPredictions['predicted_revenue'], 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cost of Goods Sold</td>
                                <td class="text-end text-danger">(${{ number_format($currentPeriod['cost_of_goods_sold'], 2) }})</td>
                                <td class="text-end text-danger">(${{ number_format($previousPeriod['cost_of_goods_sold'], 2) }})</td>
                                <td class="text-end text-warning">
                                    -${{ number_format($currentPeriod['cost_of_goods_sold'] - $previousPeriod['cost_of_goods_sold'], 2) }}
                                </td>
                                <td class="text-end text-info">(${{ number_format($aiPredictions['predicted_cogs'], 2) }})</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>Gross Profit</strong></td>
                                <td class="text-end"><strong>${{ number_format($currentPeriod['gross_profit'], 2) }}</strong></td>
                                <td class="text-end"><strong>${{ number_format($previousPeriod['gross_profit'], 2) }}</strong></td>
                                <td class="text-end text-success">
                                    <strong>+${{ number_format($currentPeriod['gross_profit'] - $previousPeriod['gross_profit'], 2) }}</strong>
                                </td>
                                <td class="text-end text-info"><strong>${{ number_format($aiPredictions['predicted_gross_profit'], 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td>Operating Expenses</td>
                                <td class="text-end text-danger">(${{ number_format($currentPeriod['operating_expenses'], 2) }})</td>
                                <td class="text-end text-danger">(${{ number_format($previousPeriod['operating_expenses'], 2) }})</td>
                                <td class="text-end text-warning">
                                    -${{ number_format($currentPeriod['operating_expenses'] - $previousPeriod['operating_expenses'], 2) }}
                                </td>
                                <td class="text-end text-info">(${{ number_format($aiPredictions['predicted_operating_expenses'], 2) }})</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Net Profit</strong></td>
                                <td class="text-end"><strong>${{ number_format($currentPeriod['net_profit'], 2) }}</strong></td>
                                <td class="text-end"><strong>${{ number_format($previousPeriod['net_profit'], 2) }}</strong></td>
                                <td class="text-end text-success">
                                    <strong>+${{ number_format($currentPeriod['net_profit'] - $previousPeriod['net_profit'], 2) }}</strong>
                                </td>
                                <td class="text-end text-info"><strong>${{ number_format($aiPredictions['predicted_net_profit'], 2) }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- AI Insights -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-brain me-2"></i>AI Financial Insights
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-primary">
                        <i class="fas fa-chart-line me-2"></i>Prediction Confidence
                    </h6>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $aiPredictions['confidence_level'] }}%">
                        </div>
                    </div>
                    <small class="text-dark">{{ number_format($aiPredictions['confidence_level'], 1) }}% Confidence Level</small>
                </div>
                
                <div class="mb-4">
                    <h6 class="text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Risk Factors
                    </h6>
                    <ul class="list-unstyled">
                        @foreach($aiPredictions['risk_factors'] as $risk)
                        <li class="mb-2">
                            <i class="fas fa-chevron-right me-2 text-warning"></i>
                            <span class="text-dark">{{ $risk }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <div>
                    <h6 class="text-success">
                        <i class="fas fa-lightbulb me-2"></i>AI Recommendations
                    </h6>
                    <ul class="list-unstyled">
                        @foreach($aiPredictions['recommendations'] as $recommendation)
                        <li class="mb-2">
                            <i class="fas fa-check-circle me-2 text-success"></i>
                            <span class="text-dark">{{ $recommendation }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Profit Trend Chart -->
<div class="row section-spacing">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-area me-2"></i>Profit Trend Analysis
                </h5>
            </div>
            <div class="card-body">
                <canvas id="profitTrendChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Profit Trend Chart
const ctx = document.getElementById('profitTrendChart').getContext('2d');
const profitTrendChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json(array_column($monthlyData, 'month')),
        datasets: [{
            label: 'Actual Profit',
            data: @json(array_column($monthlyData, 'profit')),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'AI Predicted Profit',
            data: @json(array_column($monthlyData, 'predicted')),
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            borderDash: [5, 5],
            tension: 0.4,
            fill: false
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Monthly Profit Trend: Actual vs AI Predictions'
            },
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

function exportToPDF() {
    alert('PDF export functionality would be implemented here');
}

function refreshData() {
    location.reload();
}
</script>
@endsection
