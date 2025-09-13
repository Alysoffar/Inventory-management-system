@extends('layouts.app')

@section('title', 'Sales Analytics Report')

@section('content')
<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-3"></i>Sales Analytics Report
            </h1>
            <p class="page-subtitle">Comprehensive sales analysis with real-time performance metrics</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ url('/reports/export/sales-pdf') }}" target="_blank" class="btn btn-outline-primary">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </a>
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
                <form method="GET" action="{{ route('reports.sales') }}" class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff; color: white;">
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
                <i class="fas fa-shopping-cart fa-2x mb-3 text-primary"></i>
                <h3 class="text-primary">{{ $totalSales ?? 0 }}</h3>
                <p class="mb-1">Total Sales</p>
                <small class="text-dark">Items sold</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x mb-3 text-success"></i>
                <h3 class="text-success">${{ number_format($totalRevenue ?? 0, 2) }}</h3>
                <p class="mb-1">Total Revenue</p>
                <small class="text-dark">Current period</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x mb-3 text-info"></i>
                <h3 class="text-info">${{ $totalSales > 0 ? number_format($totalRevenue / $totalSales, 2) : '0.00' }}</h3>
                <p class="mb-1">Avg. Sale Value</p>
                <small class="text-dark">Per transaction</small>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
        <div class="card stats-card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-trending-up fa-2x mb-3 text-warning"></i>
                <h3 class="text-warning">+15.3%</h3>
                <p class="mb-1">Growth Rate</p>
                <small class="text-dark">vs last period</small>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row section-spacing">
    <!-- Sales Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Sales by Product
                </h5>
            </div>
            <div class="card-body">
                <canvas id="salesByProductChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Sales Insights -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-lightbulb me-2"></i>Sales Insights
                </h5>
            </div>
            <div class="card-body">
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Best Performer</span>
                        <strong class="text-success">Product A</strong>
                    </div>
                </div>
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Conversion Rate</span>
                        <strong class="text-info">24.5%</strong>
                    </div>
                </div>
                <div class="insight-item mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Peak Sales Hour</span>
                        <strong class="text-warning">2:00 PM</strong>
                    </div>
                </div>
                <div class="insight-item">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Return Rate</span>
                        <strong class="text-danger">2.1%</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales Data Table -->
<div class="row section-spacing">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Sales Details
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                                <th>Date</th>
                                <th>Customer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($saleItems) && $saleItems->count() > 0)
                                @foreach($saleItems as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">SKU: {{ $item->product->sku ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $item->quantity }}</span>
                                        </td>
                                        <td>${{ number_format($item->unit_price ?? 0, 2) }}</td>
                                        <td><strong class="text-success">${{ number_format($item->total_amount ?? 0, 2) }}</strong></td>
                                        <td>{{ $item->sale_date ? $item->sale_date->format('M d, Y H:i') : 'N/A' }}</td>
                                        <td>{{ $item->customer->name ?? 'Walk-in Customer' }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">No sales found for the selected period</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                @if(isset($saleItems) && method_exists($saleItems, 'hasPages') && $saleItems->hasPages())
                    <div class="d-flex justify-content-center mt-3">
                        {{ $saleItems->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function refreshData() {
    location.reload();
}

// Sales Chart
document.addEventListener('DOMContentLoaded', function() {
    const salesCtx = document.getElementById('salesByProductChart');
    
    if (!salesCtx) {
        console.error('Sales chart canvas not found');
        return;
    }
    
    const ctx = salesCtx.getContext('2d');
    
    @if(isset($salesByProduct) && is_array($salesByProduct) && count($salesByProduct) > 0)
        const salesData = @json($salesByProduct);
        const labels = Object.keys(salesData);
        const data = Object.values(salesData);
        
        console.log('Sales Chart Data:', salesData);
        console.log('Labels:', labels);
        console.log('Data Values:', data);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Sales Quantity',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(199, 199, 199, 0.8)',
                        'rgba(83, 102, 255, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)'
                    ],
                    borderWidth: 2
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
                                return `${label}: ${value} units (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    @else
        console.log('No sales data available for chart');
        
        // Create a message in the chart container
        const chartContainer = ctx.parentElement;
        const messageDiv = document.createElement('div');
        messageDiv.className = 'text-center p-4';
        messageDiv.innerHTML = `
            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
            <p class="text-muted">No sales data available for chart</p>
            <small class="text-muted">Process sales to see the distribution</small>
        `;
        
        // Hide the canvas and show the message
        ctx.style.display = 'none';
        chartContainer.appendChild(messageDiv);
    @endif
});
</script>
@endsection
