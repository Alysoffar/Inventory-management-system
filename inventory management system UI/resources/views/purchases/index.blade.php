@extends('layouts.app')

@section('title', 'Purchases Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2 fw-semibold">🛒 Purchases Management</h2>
                <p class="mb-0 text-muted">Track and manage your purchase orders</p>
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
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-shopping-bag text-primary mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $totalPurchases ?? 0 }}</h2>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">Total Purchases</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+8.2% from last month</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-dollar-sign text-success mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">${{ number_format($totalCost ?? 0, 2) }}</h2>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">Total Cost</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+6.8% from last month</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-money-dollar-circle-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-calendar-alt text-info mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $todayPurchases ?? 0 }}</h2>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">Today's Purchases</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+3.1% from yesterday</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-chart-bar text-warning mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">${{ number_format($averagePurchase ?? 0, 2) }}</h2>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">Average Purchase</p>
                    <small class="text-danger"><i class="fas fa-arrow-down me-1"></i>-1.3% from previous month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Purchases List</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                <i class="ri-add-line align-middle me-1"></i> New Purchase
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="date_to" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="supplier" class="form-label">Supplier</label>
                            <select class="form-select" id="supplier" name="supplier">
                                <option value="">All Suppliers</option>
                                @if(isset($suppliers))
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="ri-search-line align-middle me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Purchases Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2 px-3" style="font-size: 0.9rem;">Purchase ID</th>
                                    <th class="py-2 px-3" style="font-size: 0.9rem;">Supplier</th>
                                    <th class="py-2 px-3" style="font-size: 0.9rem;">Date</th>
                                    <th class="py-2 px-3" style="font-size: 0.9rem;">Total Amount</th>
                                    <th class="py-2 px-3" style="font-size: 0.9rem;">Status</th>
                                    <th class="py-2 px-3" style="font-size: 0.9rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchases ?? [] as $purchase)
                                <tr>
                                    <td class="py-2 px-3">
                                        <strong style="font-size: 1.1rem;">#{{ str_pad($purchase->id ?? 0, 6, '0', STR_PAD_LEFT) }}</strong>
                                    </td>
                                    <td class="py-2 px-3">
                                        <div>
                                            <strong style="font-size: 1.1rem;">{{ $purchase->supplier->name ?? 'N/A' }}</strong>
                                            @if(isset($purchase->supplier->email))
                                                <br><small class="text-muted">{{ $purchase->supplier->email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2 px-3" style="font-size: 1rem;">
                                        {{ $purchase->created_at ? $purchase->created_at->format('M d, Y') : 'N/A' }}
                                        <br><small class="text-muted">{{ $purchase->created_at ? $purchase->created_at->format('h:i A') : '' }}</small>
                                    </td>
                                    <td class="py-2 px-3">
                                        <strong class="text-danger" style="font-size: 1.2rem;">${{ number_format($purchase->total_amount ?? 0, 2) }}</strong>
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="badge bg-{{ ($purchase->status ?? 'pending') === 'completed' ? 'success' : (($purchase->status ?? 'pending') === 'pending' ? 'warning' : 'secondary') }}" style="font-size: 0.9rem;">
                                            {{ ucfirst($purchase->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('purchases.show', $purchase->id ?? 1) }}">
                                                    <i class="ri-eye-line align-middle me-2"></i> View
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('purchases.edit', $purchase->id ?? 1) }}">
                                                    <i class="ri-edit-line align-middle me-2"></i> Edit
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $purchase->id ?? 1 }})">
                                                    <i class="ri-delete-bin-line align-middle me-2"></i> Delete
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="py-4">
                                            <i class="ri-shopping-bag-line display-4 text-muted"></i>
                                            <h5 class="mt-3">No Purchases Found</h5>
                                            <p class="text-muted">There are no purchase records to display.</p>
                                            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                                                <i class="ri-add-line align-middle me-1"></i> Create First Purchase
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
