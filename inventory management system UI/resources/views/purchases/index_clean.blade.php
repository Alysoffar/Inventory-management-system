@extends('layouts.app')

@section('title', 'Purchases Management')

@section('content')
<style>
/* AWS Cloudscape Design System - Purchases Management */
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

.purchases-management .card {
    border: 1px solid var(--aws-color-grey-200);
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    min-height: 180px;
    transition: all 0.2s ease;
}

.purchases-management .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.purchases-management .stats-card {
    min-height: 200px;
}

.purchases-management .card-body {
    padding: 24px;
}

.purchases-management .metric-value {
    font-size: 2rem;
    font-weight: 700;
    color: #212529 !important;
    margin-bottom: 8px;
}

.purchases-management .metric-label {
    font-size: 1.1rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 8px;
}

.purchases-management .page-title {
    font-size: 2rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 8px;
}

.purchases-management .page-subtitle {
    font-size: 1.1rem;
    color: #212529 !important;
    margin: 0;
}

.purchases-management .card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #212529 !important;
    margin: 0;
}

.purchases-management .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
    padding: 1.5rem;
}

.purchases-management .btn {
    font-size: 1rem;
    font-weight: 500;
    padding: 10px 16px;
    border-radius: 6px;
}

.purchases-management .form-control, .purchases-management .form-select {
    font-size: 1rem;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid var(--aws-color-grey-200);
}

.purchases-management th {
    font-size: 1rem !important;
    font-weight: 600;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
}

.purchases-management td {
    font-size: 1rem !important;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    border-bottom: 1px solid var(--aws-color-grey-200);
}

.purchases-management .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--aws-color-grey-600);
}

.purchases-management .empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.purchases-management .empty-state h4 {
    margin-bottom: 0.5rem;
    color: var(--aws-color-grey-900);
}

.purchases-management .empty-state p {
    margin-bottom: 2rem;
}
</style>

<div class="container-fluid purchases-management">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">🛒 Purchases Management</h2>
                <p class="page-subtitle">Track and manage your purchase orders</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> New Purchase
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
                    <i class="fas fa-shopping-bag text-primary mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">{{ $totalPurchases ?? 0 }}</h2>
                    <p class="metric-label">Total Purchases</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+8.2% from last month</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-dollar-sign text-success mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">${{ number_format($totalCost ?? 0, 2) }}</h2>
                    <p class="metric-label">Total Cost</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+6.8% from last month</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-calendar-alt text-info mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">{{ $todayPurchases ?? 0 }}</h2>
                    <p class="metric-label">Today's Purchases</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+12.1% from yesterday</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="fas fa-chart-line text-warning mb-3" style="font-size: 2.5rem;"></i>
                    <h2 class="metric-value">${{ number_format($averagePurchase ?? 0, 2) }}</h2>
                    <p class="metric-label">Average Purchase</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+4.3% from last month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Purchases List</h3>
        </div>
        <div class="card-body">
            <!-- Filter Form -->
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Supplier</label>
                        <select name="supplier" class="form-select">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="ordered" {{ request('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" style="background-color: #007bff; border-color: #007bff; color: white;">
                                <i class="fas fa-filter me-2"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Purchases Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Purchase ID</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases ?? [] as $purchase)
                        <tr>
                            <td>
                                <strong>#{{ str_pad($purchase->id ?? 0, 6, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $purchase->supplier->name ?? 'N/A' }}</strong>
                                    @if(isset($purchase->supplier->email))
                                        <br><small class="text-muted">{{ $purchase->supplier->email }}</small>
                                    @endif
                                </div>
                                <br><small class="text-muted">
                                    @if($purchase->purchaseItems->count() > 0)
                                        {{ $purchase->purchaseItems->count() }} item(s)
                                    @endif
                                </small>
                            </td>
                            <td>
                                {{ $purchase->order_date ? $purchase->order_date->format('M d, Y') : 'N/A' }}
                                @if($purchase->order_date)
                                    <br><small class="text-muted">{{ $purchase->order_date->format('h:i A') }}</small>
                                @endif
                            </td>
                            <td>
                                <strong class="text-danger">${{ number_format($purchase->total_amount ?? 0, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ ($purchase->status ?? 'pending') === 'completed' ? 'success' : (($purchase->status ?? 'pending') === 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($purchase->status ?? 'pending') }}
                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('purchases.show', $purchase->id ?? 1) }}">
                                            <i class="fas fa-eye me-2"></i> View
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('purchases.edit', $purchase->id ?? 1) }}">
                                            <i class="fas fa-edit me-2"></i> Edit
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $purchase->id ?? 1 }})">
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
                                    <i class="fas fa-shopping-bag"></i>
                                    <h4>No Purchases Found</h4>
                                    <p>There are no purchase records to display.</p>
                                    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Create First Purchase
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($purchases) && method_exists($purchases, 'links'))
                <div class="row">
                    <div class="col">
                        <div class="mt-3">
                            {{ $purchases->links() }}
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
                Are you sure you want to delete this purchase? This action cannot be undone.
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
function confirmDelete(purchaseId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/purchases/${purchaseId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endpush
@endsection
