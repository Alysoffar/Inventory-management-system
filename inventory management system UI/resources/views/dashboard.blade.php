@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
/* 3D Bouncing Greeting Animation */
.greeting-container {
    margin-bottom: 2rem;
    text-align: center;
    perspective: 1000px;
}

.greeting-message {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 3rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    display: inline-block;
    font-size: 2.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    position: relative;
    overflow: hidden;
    animation: bounce3D 2s ease-in-out infinite;
    transform-style: preserve-3d;
}

.greeting-message::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shine 3s infinite;
}

.greeting-message .wave {
    display: inline-block;
    animation: wave 1.5s ease-in-out infinite;
}

.greeting-message .wave:nth-child(2) { animation-delay: 0.1s; }
.greeting-message .wave:nth-child(3) { animation-delay: 0.2s; }
.greeting-message .wave:nth-child(4) { animation-delay: 0.3s; }
.greeting-message .wave:nth-child(5) { animation-delay: 0.4s; }
.greeting-message .wave:nth-child(6) { animation-delay: 0.5s; }
.greeting-message .wave:nth-child(7) { animation-delay: 0.6s; }
.greeting-message .wave:nth-child(8) { animation-delay: 0.7s; }

/* Welcome Message Popup Animation */
.welcome-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
    color: white;
    padding: 3rem 4rem;
    border-radius: 25px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    z-index: 9999;
    text-align: center;
    font-size: 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 3px;
    animation: popupBounce 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    max-width: 90vw;
}

.welcome-popup::after {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background: linear-gradient(45deg, #ff6b6b, #ee5a24, #ff6b6b);
    border-radius: 25px;
    z-index: -1;
    animation: rotate 3s linear infinite;
}

.popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.7);
    z-index: 9998;
    opacity: 0;
    animation: fadeIn 0.5s ease forwards;
}

@keyframes popupBounce {
    0% {
        transform: translate(-50%, -50%) scale(0) rotate(-180deg);
        opacity: 0;
    }
    50% {
        transform: translate(-50%, -50%) scale(1.1) rotate(-10deg);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1) rotate(0deg);
        opacity: 1;
    }
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes fadeIn {
    to { opacity: 1; }
}

@keyframes bounce3D {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0) rotateX(0deg) rotateY(0deg);
    }
    40% {
        transform: translateY(-30px) rotateX(10deg) rotateY(5deg);
    }
    60% {
        transform: translateY(-15px) rotateX(-5deg) rotateY(-3deg);
    }
}

@keyframes wave {
    0%, 60%, 100% {
        transform: initial;
    }
    30% {
        transform: translateY(-15px) scale(1.1);
    }
}

@keyframes shine {
    0% {
        left: -100%;
    }
    100% {
        left: 100%;
    }
}

.greeting-subtitle {
    margin-top: 1rem;
    font-size: 1.2rem;
    color: #6c757d;
    font-weight: 500;
    animation: fadeInUp 1s ease-out 0.5s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .greeting-message {
        font-size: 1.8rem;
        padding: 1.5rem 2rem;
    }
    
    .greeting-subtitle {
        font-size: 1rem;
    }
    
    .welcome-popup {
        font-size: 1.5rem;
        padding: 2rem 3rem;
        letter-spacing: 2px;
    }
}
</style>

@if(session('welcome_message'))
<!-- Welcome Popup -->
<div class="popup-overlay" onclick="closeWelcomePopup()"></div>
<div class="welcome-popup" id="welcomePopup">
    {{ session('welcome_message') }}
    <div style="margin-top: 1rem; font-size: 1rem; font-weight: 400; letter-spacing: 1px; opacity: 0.9;">
        Click anywhere to continue
    </div>
</div>

<script>
function closeWelcomePopup() {
    const popup = document.getElementById('welcomePopup');
    const overlay = document.querySelector('.popup-overlay');
    
    popup.style.animation = 'popupBounce 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) reverse forwards';
    overlay.style.animation = 'fadeIn 0.3s ease reverse forwards';
    
    setTimeout(() => {
        popup.remove();
        overlay.remove();
    }, 500);
}

// Auto close after 5 seconds
setTimeout(() => {
    if (document.getElementById('welcomePopup')) {
        closeWelcomePopup();
    }
}, 5000);
</script>
@endif

<!-- 3D Greeting Message -->
<div class="greeting-container">
    <div class="greeting-message">
        @auth
            <span class="wave">H</span><span class="wave">E</span><span class="wave">L</span><span class="wave">L</span><span class="wave">O</span> 
            <span class="wave">{{ strtoupper(Auth::user()->name ?? 'USER') }}</span>
        @else
            <span class="wave">W</span><span class="wave">E</span><span class="wave">L</span><span class="wave">C</span><span class="wave">O</span><span class="wave">M</span><span class="wave">E</span>
        @endauth
    </div>
    <div class="greeting-subtitle">
        Welcome back to your Inventory Management System
    </div>
</div>

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

        @auth
            @if(auth()->user()->role === 'admin' || auth()->user()->email === 'alysoffar06@gmail.com')
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 text-center">
                            @if($pendingUsersCount > 0)
                                <i class="fas fa-user-clock text-warning mb-2" style="font-size: 2rem;"></i>
                                <h2 class="mb-1 fw-bold text-warning" style="font-size: 2rem;">{{ $pendingUsersCount }}</h2>
                                <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                                    <a href="{{ route('admin.pending-users') }}" class="text-decoration-none">
                                        Pending Approvals
                                    </a>
                                </p>
                            @else
                                <i class="fas fa-user-check text-success mb-2" style="font-size: 2rem;"></i>
                                <h2 class="mb-1 fw-bold text-success" style="font-size: 2rem;">0</h2>
                                <p class="mb-0 text-muted" style="font-size: 0.9rem;">All Users Approved</p>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body p-3 text-center">
                            <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                            <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $lowStockCount }}</h2>
                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">Low Stock</p>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3 text-center">
                        <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                        <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $lowStockCount }}</h2>
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;">Low Stock</p>
                    </div>
                </div>
            </div>
        @endauth
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
                            <span class="badge bg-danger" style="font-size: 0.9rem;">{{ $product->stock_quantity }}</span>
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
<div class="row g-3 mb-3">
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
                        <a href="{{ route('sales.index') }}" class="btn btn-primary btn-sm">View All Sales</a>
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
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-shopping-bag me-2"></i>Recent Purchases</h6>
            </div>
            <div class="card-body p-3">
                @if($recentPurchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="py-2" style="font-size: 0.9rem;">Product</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Supplier</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Cost</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPurchases as $purchase)
                                <tr>
                                    <td class="py-2" style="font-size: 1rem;">{{ $purchase->product->name }}</td>
                                    <td class="py-2" style="font-size: 1rem;">{{ $purchase->supplier->name }}</td>
                                    <td class="py-2" style="font-size: 1.1rem;">${{ number_format($purchase->total_cost, 2) }}</td>
                                    <td class="py-2" style="font-size: 1rem;">{{ $purchase->created_at->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('purchases.index') }}" class="btn btn-primary btn-sm">View All Purchases</a>
                    </div>
                @else
                    <p class="text-muted text-center">No recent purchases found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Top Products & Recent Customers Section -->
<div class="row g-3 mb-3">
    <!-- Top Selling Products -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-trophy me-2"></i>Top Selling Products</h6>
            </div>
            <div class="card-body p-3">
                @if($topProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="py-2" style="font-size: 0.9rem;">Product</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Category</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Sold</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                <tr>
                                    <td class="py-2"><strong style="font-size: 1rem;">{{ $product['name'] }}</strong></td>
                                    <td class="py-2" style="font-size: 0.9rem;">{{ $product['category'] }}</td>
                                    <td class="py-2" style="font-size: 1rem;">{{ $product['quantity_sold'] }}</td>
                                    <td class="py-2" style="font-size: 1.1rem;">${{ number_format($product['revenue'], 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">View All Products</a>
                    </div>
                @else
                    <p class="text-muted text-center">No sales data available.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Customers -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-users me-2"></i>Recent Customers</h6>
            </div>
            <div class="card-body p-3">
                @if($recentCustomers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="py-2" style="font-size: 0.9rem;">Customer</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Orders</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Total Spent</th>
                                    <th class="py-2" style="font-size: 0.9rem;">Last Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentCustomers as $customer)
                                <tr>
                                    <td class="py-2">
                                        <div>
                                            <strong style="font-size: 1rem;">{{ $customer['name'] }}</strong>
                                            <br><small class="text-muted">{{ $customer['email'] }}</small>
                                        </div>
                                    </td>
                                    <td class="py-2" style="font-size: 1rem;">{{ $customer['orders_count'] }}</td>
                                    <td class="py-2" style="font-size: 1.1rem;">${{ number_format($customer['total_spent'], 0) }}</td>
                                    <td class="py-2" style="font-size: 0.9rem;">
                                        {{ $customer['last_purchase'] ? $customer['last_purchase']->format('M d') : 'Never' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-2">
                        <a href="{{ route('customers.index') }}" class="btn btn-primary btn-sm">View All Customers</a>
                    </div>
                @else
                    <p class="text-muted text-center">No customers found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Business Summary Section -->
<div class="row g-3 mb-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-2 px-3">
                <h6 class="mb-0 fw-semibold"><i class="fas fa-chart-bar me-2"></i>Business Summary</h6>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                            <i class="fas fa-dollar-sign text-success fa-2x mb-2"></i>
                            <h4 class="mb-1 text-success">${{ number_format($totalSales, 0) }}</h4>
                            <p class="mb-0 text-muted">Total Sales</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                            <i class="fas fa-shopping-cart text-danger fa-2x mb-2"></i>
                            <h4 class="mb-1 text-danger">${{ number_format($totalPurchases, 0) }}</h4>
                            <p class="mb-0 text-muted">Total Purchases</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                            <i class="fas fa-chart-line text-primary fa-2x mb-2"></i>
                            <h4 class="mb-1 text-primary">${{ number_format($totalProfit, 0) }}</h4>
                            <p class="mb-0 text-muted">Total Profit</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                            <i class="fas fa-boxes text-info fa-2x mb-2"></i>
                            <h4 class="mb-1 text-info">${{ number_format($inventoryStats['total_value'], 0) }}</h4>
                            <p class="mb-0 text-muted">Inventory Value</p>
                        </div>
                    </div>
                </div>
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
