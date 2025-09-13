@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<style>
/* AWS Cloudscape Design System - Reports Dashboard */
:root {
    --aws-color-blue-600: #146eb4;
    --aws-color-blue-700: #0972d3;
    --aws-color-grey-900: #16191f;
    --aws-color-grey-600: #5f6b7a;
    --aws-color-grey-200: #e9ebed;
    --aws-color-green-600: #037f0c;
    --aws-color-orange-600: #b7740e;
    --aws-color-red-600: #d13212;
}

.reports-dashboard .card {
    border: 1px solid var(--aws-color-grey-200);
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    min-height: 180px;
    transition: all 0.2s ease;
}

.reports-dashboard .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.reports-dashboard .card-body {
    padding: 24px;
}

.reports-dashboard .metric-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 12px;
}

.reports-dashboard .metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #212529 !important;
    margin-bottom: 8px;
}

.reports-dashboard .metric-change {
    font-size: 1rem;
    color: #212529 !important;
}

.reports-dashboard .card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 16px;
}

.reports-dashboard .card-text {
    font-size: 1.1rem;
    color: #212529 !important;
    margin-bottom: 20px;
}

.reports-dashboard .btn {
    font-size: 1rem;
    font-weight: 500;
    padding: 12px 20px;
    border-radius: 6px;
}

.reports-dashboard .avatar-title {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.reports-dashboard .avatar-title i {
    font-size: 32px;
}

.reports-dashboard h1, .reports-dashboard h2, .reports-dashboard h3, .reports-dashboard h4, .reports-dashboard h5, .reports-dashboard h6,
.reports-dashboard p, .reports-dashboard span, .reports-dashboard div, .reports-dashboard a, .reports-dashboard li {
    color: #212529 !important;
}

.reports-dashboard .page-title-box h4 {
    font-size: 2rem;
    font-weight: 600;
    color: #212529 !important;
}

.reports-dashboard .breadcrumb-item a {
    color: var(--aws-color-blue-600) !important;
    font-size: 1rem;
}

.reports-dashboard .breadcrumb-item.active {
    color: #212529 !important;
    font-size: 1rem;
}
</style>

<div class="container-fluid reports-dashboard">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">📊 Reports Dashboard</h4>
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
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="metric-label">Total Sales</p>
                        <h4 class="metric-value">${{ number_format($reportsOverview['total_sales'] ?? 0, 2) }}</h4>
                        <p class="metric-change mb-0">
                            <span class="text-success fw-bold me-2">
                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>+12.5%
                            </span>
                            <span>from last month</span>
                        </p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-primary rounded-3">
                            <i class="ri-line-chart-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="metric-label">Total Purchases</p>
                        <h4 class="metric-value">${{ number_format($reportsOverview['total_purchases'] ?? 0, 2) }}</h4>
                        <p class="metric-change mb-0">
                            <span class="text-info fw-bold me-2">
                                <i class="ri-arrow-right-up-line me-1 align-middle"></i>+8.3%
                            </span>
                            <span>from last month</span>
                        </p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-success rounded-3">
                            <i class="ri-shopping-bag-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="metric-label">Stock Value</p>
                        <h4 class="metric-value">${{ number_format($reportsOverview['current_stock_value'] ?? 0, 2) }}</h4>
                        <p class="metric-change mb-0">
                            <span class="text-warning fw-bold me-2">
                                <i class="ri-arrow-right-down-line me-1 align-middle"></i>-2.1%
                            </span>
                            <span>from last month</span>
                        </p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-info rounded-3">
                            <i class="ri-archive-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="metric-label">Low Stock Items</p>
                        <h4 class="metric-value">{{ $reportsOverview['low_stock_items'] ?? 0 }}</h4>
                        <p class="metric-change mb-0">
                            <span class="text-danger fw-bold me-2">
                                <i class="ri-alert-line me-1 align-middle"></i>Needs attention
                            </span>
                        </p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-danger rounded-3">
                            <i class="ri-error-warning-line"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Grid -->
    <div class="row mb-4">
        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="ri-bar-chart-box-line me-2 text-primary"></i>Sales Reports
                    </h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Generate detailed sales reports with various filters and date ranges to analyze performance and trends.</p>
                    <div class="d-grid gap-3">
                        <a href="{{ route('reports.sales') }}" class="btn btn-primary">
                            <i class="ri-bar-chart-box-line me-2"></i> Sales Report
                        </a>
                        <a href="{{ route('reports.monthly-sales') }}" class="btn btn-outline-primary">
                            <i class="ri-calendar-line me-2"></i> Monthly Sales
                        </a>
                        <a href="{{ route('reports.customer-analysis') }}" class="btn btn-outline-primary">
                            <i class="ri-user-line me-2"></i> Customer Analysis
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="ri-archive-line me-2 text-success"></i>Inventory Reports
                    </h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Monitor stock levels, valuation, and inventory movements to optimize your inventory management.</p>
                    <div class="d-grid gap-3">
                        <a href="{{ route('reports.inventory') }}" class="btn btn-success">
                            <i class="ri-archive-line me-2"></i> Inventory Report
                        </a>
                        <a href="{{ route('reports.low-stock-alert') }}" class="btn btn-outline-success">
                            <i class="ri-alert-line me-2"></i> Low Stock Alert
                        </a>
                        <a href="{{ route('reports.stock-valuation') }}" class="btn btn-outline-success">
                            <i class="ri-money-dollar-circle-line me-2"></i> Stock Valuation
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="ri-pie-chart-line me-2 text-info"></i>Financial Reports
                    </h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Analyze financial performance and profitability with comprehensive financial insights.</p>
                    <div class="d-grid gap-3">
                        <a href="{{ route('reports.profit-loss') }}" class="btn btn-info">
                            <i class="ri-pie-chart-line me-2"></i> Profit & Loss
                        </a>
                        <a href="{{ route('reports.purchase-analysis') }}" class="btn btn-outline-info">
                            <i class="ri-shopping-bag-line me-2"></i> Purchase Analysis
                        </a>
                        <a href="{{ route('reports.cost-analysis') }}" class="btn btn-outline-info">
                            <i class="ri-calculator-line me-2"></i> Cost Analysis
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
                <div class="card-header bg-light border-bottom">
                    <h4 class="card-title mb-0">
                        <i class="ri-dashboard-line me-2 text-primary"></i>Quick Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-primary" onclick="generateReport('sales')">
                                    <i class="ri-download-line me-2"></i> Export Sales Data
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-success" onclick="generateReport('inventory')">
                                    <i class="ri-download-line me-2"></i> Export Inventory
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-info" onclick="generateReport('customers')">
                                    <i class="ri-download-line me-2"></i> Export Customers
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-warning" onclick="scheduleReport()">
                                    <i class="ri-calendar-schedule-line me-2"></i> Schedule Reports
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="d-grid">
                                <button type="button" class="btn btn-soft-danger" onclick="generateAIExport()">
                                    <i class="ri-file-pdf-line me-2"></i> AI Export
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
    // Show loading state
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="ri-loader-4-line me-2 spinner-border spinner-border-sm"></i> Generating PDF...';
    button.disabled = true;
    
    // Determine the export URL based on type
    let exportUrl = '';
    switch(type) {
        case 'sales':
            exportUrl = '{{ route("reports.export.sales-pdf") }}';
            break;
        case 'inventory':
            exportUrl = '{{ route("reports.export.inventory-pdf") }}';
            break;
        case 'customers':
            exportUrl = '{{ route("reports.export.customers-pdf") }}';
            break;
        default:
            alert('Export type not supported: ' + type);
            button.innerHTML = originalText;
            button.disabled = false;
            return;
    }
    
    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = exportUrl;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Reset button after a delay
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}

function scheduleReport() {
    // Placeholder for report scheduling
    alert('Report scheduling feature will be implemented soon.');
}

function generateAIExport() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="ri-loader-4-line me-2 spinner-border spinner-border-sm"></i> Generating PDF...';
    button.disabled = true;
    
    // Create a form to submit to the AI export route
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("reports.ai-export") }}';
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
    
    // Reset button after a delay
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
        document.body.removeChild(form);
    }, 2000);
}
</script>
@endpush
@endsection
