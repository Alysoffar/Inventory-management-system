@extends('layouts.app')

@section('title', 'Customer Analysis Report')

@section('content')
<style>
.section-spacing {
    margin-bottom: 2rem;
}

.page-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
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

.segment-card {
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.segment-card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.segment-vip {
    border-left-color: #fbbf24;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
}

.segment-loyal {
    border-left-color: #10b981;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
}

.segment-regular {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #dbeafe 0%, #93c5fd 100%);
}

.segment-at-risk {
    border-left-color: #ef4444;
    background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);
}

.segment-new {
    border-left-color: #8b5cf6;
    background: linear-gradient(135deg, #ede9fe 0%, #c4b5fd 100%);
}

.customer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.retention-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: conic-gradient(#10b981 0deg {{ $retentionRate * 3.6 }}deg, #e5e7eb {{ $retentionRate * 3.6 }}deg 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.retention-circle::before {
    content: '';
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: white;
    position: absolute;
}

.retention-text {
    position: relative;
    z-index: 1;
    font-weight: bold;
    color: #1f2937;
}
</style>

<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-2">
                    <i class="fas fa-users me-3"></i>Customer Analysis Report
                </h1>
                <p class="page-subtitle mb-0">Comprehensive customer segmentation and behavior analysis</p>
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
    <!-- Key Metrics -->
    <div class="row section-spacing">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-primary text-white mx-auto mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-primary mb-1">{{ number_format($totalCustomers) }}</h3>
                    <p class="text-muted mb-0">Total Customers</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-success text-white mx-auto mb-3">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h3 class="text-success mb-1">{{ number_format($activeCustomers) }}</h3>
                    <p class="text-muted mb-0">Active (30 days)</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <div class="retention-circle mx-auto mb-3">
                        <div class="retention-text">{{ number_format($retentionRate, 1) }}%</div>
                    </div>
                    <p class="text-muted mb-0">Retention Rate</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card metric-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon bg-info text-white mx-auto mb-3">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-info mb-1">{{ number_format(collect($monthlyNewCustomers)->sum('count')) }}</h3>
                    <p class="text-muted mb-0">New This Year</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Segmentation -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>Customer Segmentation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($segmentStats as $segment)
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card segment-card segment-{{ strtolower(str_replace(' ', '-', $segment['segment'])) }} h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $segment['segment'] }} Customers</h6>
                                            <h4 class="text-dark mb-0">{{ $segment['count'] }}</h4>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">Total Revenue</small>
                                            <h6 class="text-dark mb-0">${{ number_format($segment['total_revenue'], 2) }}</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <small class="text-muted">Average Order Value</small>
                                        <div class="fw-bold">${{ number_format($segment['avg_order_value'], 2) }}</div>
                                    </div>
                                    
                                    @if($segment['customers']->count() > 0)
                                    <div>
                                        <small class="text-muted d-block mb-2">Top Customers:</small>
                                        @foreach($segment['customers']->take(3) as $customerData)
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="customer-avatar me-2">
                                                {{ substr($customerData['customer']->name, 0, 1) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <small class="fw-bold">{{ $customerData['customer']->name }}</small>
                                                <br>
                                                <small class="text-muted">${{ number_format($customerData['total_spent'], 2) }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif
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
                        <i class="fas fa-chart-line me-2"></i>Customer Acquisition Trend
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="acquisitionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-pie-chart me-2"></i>Segment Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="segmentChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers Table -->
    <div class="row section-spacing">
        <div class="col-12">
            <div class="card metric-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-crown me-2"></i>Top Customers by Revenue
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rank</th>
                                    <th>Customer</th>
                                    <th>Total Revenue</th>
                                    <th>Orders</th>
                                    <th>Avg Order Value</th>
                                    <th>Last Order</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $index => $customer)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="customer-avatar me-3">
                                                {{ substr($customer->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $customer->name }}</div>
                                                <small class="text-muted">{{ $customer->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-success">${{ number_format($customer->sales_sum_total_amount, 2) }}</td>
                                    <td>{{ $customer->sales_count }}</td>
                                    <td>${{ number_format($customer->sales_count > 0 ? $customer->sales_sum_total_amount / $customer->sales_count : 0, 2) }}</td>
                                    <td>
                                        @if($customer->sales->count() > 0)
                                            {{ \Carbon\Carbon::parse($customer->sales->max('sale_date'))->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">No orders</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $lastOrder = $customer->sales->count() > 0 ? \Carbon\Carbon::parse($customer->sales->max('sale_date')) : null;
                                            $daysSince = $lastOrder ? $lastOrder->diffInDays(now()) : 999;
                                        @endphp
                                        @if($daysSince <= 30)
                                            <span class="badge bg-success">Active</span>
                                        @elseif($daysSince <= 90)
                                            <span class="badge bg-warning">Inactive</span>
                                        @else
                                            <span class="badge bg-danger">At Risk</span>
                                        @endif
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
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Customer Acquisition Chart
const acquisitionCtx = document.getElementById('acquisitionChart').getContext('2d');
const acquisitionChart = new Chart(acquisitionCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($monthlyNewCustomers, 'month')),
        datasets: [{
            label: 'New Customers',
            data: @json(array_column($monthlyNewCustomers, 'count')),
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79, 70, 229, 0.1)',
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#4f46e5',
            pointBorderColor: '#4f46e5',
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

// Segment Distribution Chart
const segmentCtx = document.getElementById('segmentChart').getContext('2d');
const segmentChart = new Chart(segmentCtx, {
    type: 'doughnut',
    data: {
        labels: @json($segmentStats->pluck('segment')->toArray()),
        datasets: [{
            data: @json($segmentStats->pluck('count')->toArray()),
            backgroundColor: [
                '#fbbf24', // VIP - Gold
                '#10b981', // Loyal - Green
                '#3b82f6', // Regular - Blue
                '#ef4444', // At Risk - Red
                '#8b5cf6'  // New - Purple
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
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
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