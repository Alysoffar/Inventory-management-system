@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="mb-1">
    <h5 class="mb-0 fw-semibold">
        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
    </h5>
    <p class="mb-0 small text-muted">Inventory Management System</p>
</div>

<!-- Statistics Cards -->
<div class="row g-1 mb-2">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-2">
                <i class="fas fa-boxes text-primary mb-1" style="font-size: 1.2rem;"></i>
                <h4 class="mb-0 fw-bold" style="font-size: 1.1rem;">{{ $totalProducts }}</h4>
                <p class="mb-0 small text-muted">Products</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-2">
                <i class="fas fa-dollar-sign text-success mb-1" style="font-size: 1.2rem;"></i>
                <h4 class="mb-0 fw-bold" style="font-size: 1.1rem;">${{ number_format($todayRevenue, 0) }}</h4>
                <p class="mb-0 small text-muted">Revenue</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-2">
                <i class="fas fa-users text-info mb-1" style="font-size: 1.2rem;"></i>
                <h4 class="mb-0 fw-bold" style="font-size: 1.1rem;">{{ $totalCustomers }}</h4>
                <p class="mb-0 small text-muted">Customers</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center p-2">
                <i class="fas fa-exclamation-triangle text-warning mb-1" style="font-size: 1.2rem;"></i>
                <h4 class="mb-0 fw-bold" style="font-size: 1.1rem;">{{ $lowStockCount }}</h4>
                <p class="mb-0 small text-muted">Low Stock</p>
            </div>
        </div>
    </div>
</div>

<!-- Today's Sales Card -->
<div class="row g-1 mb-2">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-2 d-flex justify-content-between align-items-center">
                <div>
                    <div class="small text-uppercase fw-semibold text-muted mb-0">Today's Sales</div>
                    <div class="h5 mb-0 fw-bold">{{ $todaySalesCount }}</div>
                </div>
                <div>
                    <i class="fas fa-shopping-cart fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Data Section -->
<div class="row g-1 mb-2"
<!-- Charts and Data Section -->
<div class="row g-1 mb-2">
    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-1 px-2">
                <h6 class="mb-0 small fw-semibold"><i class="fas fa-chart-line me-1"></i>Revenue</h6>
            </div>
            <div class="card-body p-2">
                <canvas id="revenueChart" width="100" height="25"></canvas>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-1 px-2">
                <h6 class="mb-0 small fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Low Stock</h6>
            </div>
            <div class="card-body p-2">
                @if($lowStockProducts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($lowStockProducts as $product)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 border-0">
                            <div>
                                <strong class="text-dark small">{{ $product->name }}</strong>
                                <br>
                                <small class="text-muted" style="font-size: 0.6rem;">{{ $product->category }}</small>
                            </div>
                            <span class="badge bg-danger small">{{ $product->quantity }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-2">
                        <i class="fas fa-check-circle fa-2x text-success mb-1"></i>
                        <p class="mb-0 small text-muted">All products are well stocked!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="row g-1">
    <!-- Recent Sales -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-1 px-2">
                <h6 class="mb-0 small fw-semibold"><i class="fas fa-shopping-cart me-1"></i>Recent Sales</h6>
            </div>
            <div class="card-body p-2">
                @if($recentSales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-1" style="font-size: 0.65rem;">
                            <thead>
                                <tr>
                                    <th class="py-1">Product</th>
                                    <th class="py-1">Customer</th>
                                    <th class="py-1">Amount</th>
                                    <th class="py-1">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSales as $sale)
                                <tr>
                                    <td class="py-1"><strong>{{ $sale->product->name }}</strong></td>
                                    <td class="py-1">{{ $sale->customer->name }}</td>
                                    <td class="py-1">${{ number_format($sale->total_amount, 0) }}</td>
                                    <td class="py-1">{{ $sale->created_at->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('sales.index') }}" class="btn btn-primary btn-sm">View All</a>
                    </div>
                @else
                    <p class="text-muted text-center small">No recent sales found.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-1 px-2">
                <h6 class="mb-0 small fw-semibold"><i class="fas fa-shopping-bag me-1"></i>Recent Purchases</h6>
            </div>
            <div class="card-body p-2">
                @if($recentPurchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-1" style="font-size: 0.65rem;">
                            <thead>
                                <tr>
                                    <th class="py-1">Product</th>
                                    <th class="py-1">Supplier</th>
                                    <th class="py-1">Cost</th>
                                    <th class="py-1">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPurchases as $purchase)
                                <tr>
                                    <td class="py-1">{{ $purchase->product->name }}</td>
                                    <td class="py-1">{{ $purchase->supplier->name }}</td>
                                    <td class="py-1">${{ number_format($purchase->total_cost, 2) }}</td>
                                    <td class="py-1">{{ $purchase->created_at->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('purchases.index') }}" class="btn btn-primary btn-sm">View All</a>
                    </div>
                @else
                    <p class="text-muted text-center small">No recent purchases found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(array_column($monthlyRevenue, 'month')),
            datasets: [{
                label: 'Revenue ($)',
                data: @json(array_column($monthlyRevenue, 'revenue')),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#667eea',
                pointRadius: 3,
                pointHoverRadius: 5
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
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 10
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#e3e6f0'
                    },
                    ticks: {
                        font: {
                            size: 10
                        },
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
