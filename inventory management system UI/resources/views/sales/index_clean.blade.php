@extends('layouts.app')

@section('title', 'Sales Management')

@section('content')
<style>
/* AWS Cloudscape Design System - Sales Management */
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

.sales-management .card {
    border: 1px solid var(--aws-color-grey-200);
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    min-height: 180px;
    transition: all 0.2s ease;
}

.sales-management .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.sales-management .stats-card {
    min-height: 200px;
}

.sales-management .card-body {
    padding: 24px;
}

.sales-management .metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #212529 !important;
    margin-bottom: 8px;
}

.sales-management .metric-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 8px;
}

.sales-management .page-title {
    font-size: 2rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 8px;
}

.sales-management .page-subtitle {
    font-size: 1.1rem;
    color: #212529 !important;
    margin: 0;
}

.sales-management .card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #212529 !important;
    margin: 0;
}

.sales-management .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
    padding: 1.5rem;
}

.sales-management .btn {
    font-size: 1rem;
    font-weight: 500;
    padding: 10px 16px;
    border-radius: 6px;
}

.sales-management .form-control, .sales-management .form-select {
    font-size: 1rem;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid var(--aws-color-grey-200);
}

.sales-management th {
    font-size: 1rem !important;
    font-weight: 600;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
}

.sales-management td {
    font-size: 1.1rem !important;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    border-bottom: 1px solid #f8f9fc;
}

.sales-management .badge {
    font-size: 1rem;
    padding: 6px 12px;
}

.sales-management .btn-sm {
    font-size: 0.9rem;
    padding: 8px 12px;
}

.sales-management h1, .sales-management h2, .sales-management h3, .sales-management h4, .sales-management h5, .sales-management h6,
.sales-management p, .sales-management span, .sales-management div, .sales-management a, .sales-management li {
    color: #212529 !important;
}

.sales-management .text-muted {
    color: #5f6b7a !important;
}

.sales-management .empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.sales-management .empty-state i {
    font-size: 4rem;
    color: #5f6b7a;
    margin-bottom: 2rem;
}

.sales-management .empty-state h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 1rem;
}

.sales-management .empty-state p {
    font-size: 1.1rem;
    color: #5f6b7a !important;
    margin-bottom: 2rem;
}
</style>

<div class="container-fluid sales-management">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">💰 Sales Management</h2>
                <p class="page-subtitle">Track and manage your sales transactions</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> New Sale
                </a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-shopping-cart text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">{{ $totalSales ?? 0 }}</h2>
                    <p class="metric-label">Total Sales</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+12.5% from last month</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-dollar-sign text-success mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">${{ number_format($totalRevenue ?? 0, 2) }}</h2>
                    <p class="metric-label">Total Revenue</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+18.2% from last month</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-calendar-alt text-info mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">{{ $todaySales ?? 0 }}</h2>
                    <p class="metric-label">Today's Sales</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+5.7% from yesterday</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-chart-line text-warning mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">${{ number_format($averageSale ?? 0, 2) }}</h2>
                    <p class="metric-label">Average Sale</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+2.3% from previous month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="card-title">Sales List</h6>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i> New Sale
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Filters -->
            <form method="GET" class="row g-3 mb-4 p-3 bg-light rounded">
                <div class="col-md-3">
                    <label for="date_from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label for="customer" class="form-label">Customer</label>
                    <select class="form-select" id="customer" name="customer">
                        <option value="">All Customers</option>
                        @if(isset($customers))
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i> Filter
                        </button>
                    </div>
                </div>
            </form>

            <!-- Sales Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales ?? [] as $sale)
                        <tr>
                            <td>
                                <strong>#{{ str_pad($sale->id ?? 0, 6, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $sale->customer->name ?? 'N/A' }}</strong>
                                    @if(isset($sale->customer->email))
                                        <br><small class="text-muted">{{ $sale->customer->email }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                {{ $sale->created_at ? $sale->created_at->format('M d, Y') : 'N/A' }}
                                <br><small class="text-muted">{{ $sale->created_at ? $sale->created_at->format('h:i A') : '' }}</small>
                            </td>
                            <td>
                                <strong class="text-success">${{ number_format($sale->total_amount ?? 0, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ ($sale->status ?? 'completed') === 'completed' ? 'success' : (($sale->status ?? 'completed') === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($sale->status ?? 'completed') }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('sales.show', $sale->id ?? 1) }}">
                                            <i class="fas fa-eye me-2"></i> View
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('sales.edit', $sale->id ?? 1) }}">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $sale->id ?? 1 }})">
                                            <i class="fas fa-trash me-2"></i> Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h4>No Sales Found</h4>
                                    <p>There are no sales records to display.</p>
                                    <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Create First Sale
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($sales) && method_exists($sales, 'links'))
                <div class="row">
                    <div class="col">
                        <div class="mt-3">
                            {{ $sales->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this sale? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(saleId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/sales/${saleId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush
@endsection
