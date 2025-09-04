@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Reports Dashboard</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Sales</p>
                            <h4 class="mb-2">${{ number_format($reportsOverview['total_sales'] ?? 0, 2) }}</h4>
                            <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><i class="ri-arrow-right-up-line me-1 align-middle"></i>+12.5%</span>from last month</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-line-chart-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Purchases</p>
                            <h4 class="mb-2">${{ number_format($reportsOverview['total_purchases'] ?? 0, 2) }}</h4>
                            <p class="text-muted mb-0"><span class="text-info fw-bold font-size-12 me-2"><i class="ri-arrow-right-up-line me-1 align-middle"></i>+8.3%</span>from last month</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-shopping-bag-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Stock Value</p>
                            <h4 class="mb-2">${{ number_format($reportsOverview['current_stock_value'] ?? 0, 2) }}</h4>
                            <p class="text-muted mb-0"><span class="text-warning fw-bold font-size-12 me-2"><i class="ri-arrow-right-down-line me-1 align-middle"></i>-2.1%</span>from last month</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="ri-archive-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Low Stock Items</p>
                            <h4 class="mb-2">{{ $reportsOverview['low_stock_items'] ?? 0 }}</h4>
                            <p class="text-muted mb-0"><span class="text-danger fw-bold font-size-12 me-2"><i class="ri-alert-line me-1 align-middle"></i>Needs attention</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-danger rounded-3">
                                <i class="ri-error-warning-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="row">
        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Sales Reports</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Generate detailed sales reports with various filters and date ranges.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.sales') }}" class="btn btn-primary">
                            <i class="ri-bar-chart-box-line me-1"></i> Sales Report
                        </a>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="ri-calendar-line me-1"></i> Monthly Sales
                        </a>
                        <a href="#" class="btn btn-outline-primary">
                            <i class="ri-user-line me-1"></i> Customer Analysis
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Inventory Reports</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Monitor stock levels, valuation, and inventory movements.</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('reports.inventory') }}" class="btn btn-success">
                            <i class="ri-archive-line me-1"></i> Inventory Report
                        </a>
                        <a href="#" class="btn btn-outline-success">
                            <i class="ri-alert-line me-1"></i> Low Stock Alert
                        </a>
                        <a href="#" class="btn btn-outline-success">
                            <i class="ri-money-dollar-circle-line me-1"></i> Stock Valuation
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Financial Reports</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Analyze financial performance and profitability.</p>
                    <div class="d-grid gap-2">
                        <a href="#" class="btn btn-info">
                            <i class="ri-pie-chart-line me-1"></i> Profit & Loss
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="ri-shopping-bag-line me-1"></i> Purchase Analysis
                        </a>
                        <a href="#" class="btn btn-outline-info">
                            <i class="ri-calculator-line me-1"></i> Cost Analysis
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-primary" onclick="generateReport('sales')">
                                    <i class="ri-download-line me-1"></i> Export Sales Data
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-success" onclick="generateReport('inventory')">
                                    <i class="ri-download-line me-1"></i> Export Inventory
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-info" onclick="generateReport('customers')">
                                    <i class="ri-download-line me-1"></i> Export Customers
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-warning" onclick="scheduleReport()">
                                    <i class="ri-calendar-schedule-line me-1"></i> Schedule Reports
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function generateReport(type) {
    // Placeholder for report generation
    alert('Generating ' + type + ' report... This feature will be implemented soon.');
}

function scheduleReport() {
    // Placeholder for report scheduling
    alert('Report scheduling feature will be implemented soon.');
}
</script>
@endpush
@endsection
