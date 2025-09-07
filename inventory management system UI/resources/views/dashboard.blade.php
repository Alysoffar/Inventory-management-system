@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <h2 class="mb-2 fw-semibold">
        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
    </h2>
    <p class="mb-0 text-muted">Inventory Management System Overview</p>
</div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-boxes text-primary mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $totalProducts }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Products</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-dollar-sign text-success mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">${{ number_format($todayRevenue, 0) }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Revenue</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-users text-info mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $totalCustomers }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Customers</p>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $lowStockCount }}</h2>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">Low Stock</p>
            </div>
        </div>
    </div>
</div>

<!-- Today's Sales Card -->
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-uppercase fw-semibold text-muted mb-1" style="font-size: 0.8rem;">Today's Sales</div>
                    <div class="h2 mb-0 fw-bold" style="font-size: 1.8rem;">{{ $todaySalesCount }}</div>
                </div>
                <div>
                    <i class="fas fa-shopping-cart fa-2x text-primary opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts and Data Section -->
<div class="row g-3 mb-3">
    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-chart-line me-2"></i>Revenue</h6>
            </div>
            <div class="card-body p-3">
                <canvas id="revenueChart" width="100" height="35"></canvas>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="col-xl-4 col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-exclamation-triangle me-2"></i>Low Stock</h6>
            </div>
            <div class="card-body p-3">
                @if($lowStockProducts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($lowStockProducts as $product)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                            <div>
                                <strong class="text-dark" style="font-size: 1.1rem;">{{ $product->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $product->category }}</small>
                            </div>
                            <span class="badge bg-danger" style="font-size: 0.9rem;">{{ $product->quantity }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="mb-0 text-muted">All products are well stocked!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="row g-3">
    <!-- Recent Sales -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-shopping-cart me-2"></i>Recent Sales</h6>
            </div>
            <div class="card-body p-3">
                @if($recentSales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="py-2" style="font-size: 0.9rem;">Product</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Customer</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Amount</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSales as $sale)
                                <tr>
                                    <td class="py-2"><strong style="font-size: 1.1rem;">{{ $sale->product->name }}</strong></td>
                                    <td class="py-2" style="font-size: 1rem;">{{ $sale->customer->name }}</td>
                                    <td class="py-2" style="font-size: 1.1rem;">${{ number_format($sale->total_amount, 0) }}</td>
                                    <td class="py-2" style="font-size: 1rem;">{{ $sale->created_at->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('sales.index') }}" class="btn btn-primary btn-sm">View All</a>
                    </div>
                @else
                    <p class="text-muted text-center">No recent sales found.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3 px-4">
                <h5 class="mb-0 fw-semibold"><i class="fas fa-shopping-bag me-2"></i>Recent Purchases</h5>
            </div>
            <div class="card-body p-4">
                @if($recentPurchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="py-3">Product</th>
                                    <th class="py-3">Supplier</th>
                                    <th class="py-3">Cost</th>
                                    <th class="py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPurchases as $purchase)
                                <tr>
                                    <td class="py-3">{{ $purchase->product->name }}</td>
                                    <td class="py-3">{{ $purchase->supplier->name }}</td>
                                    <td class="py-3">${{ number_format($purchase->total_cost, 2) }}</td>
                                    <td class="py-3">{{ $purchase->created_at->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('purchases.index') }}" class="btn btn-primary">View All</a>
                    </div>
                @else
                    <p class="text-muted text-center">No recent purchases found.</p>
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
