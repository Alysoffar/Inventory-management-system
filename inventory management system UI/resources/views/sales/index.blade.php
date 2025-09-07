@extends('layouts.app')

@section('title', 'Sales Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2 fw-semibold">💰 Sales Management</h2>
                <p class="mb-0 text-muted">Track and manage your sales transactions</p>
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
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-shopping-cart text-primary mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $totalSales ?? 0 }}</h2>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">Total Sales</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+12.5% from last month</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-dollar-sign text-success mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">${{ number_format($totalRevenue ?? 0, 2) }}</h2>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">Total Revenue</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+8.3% from last month</small>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="ri-money-dollar-circle-line font-size-24"></i>
                            </span>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-calendar-day text-info mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">{{ $todaySales ?? 0 }}</h2>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">Today's Sales</p>
                    <small class="text-success"><i class="fas fa-arrow-up me-1"></i>+5.2% from yesterday</small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <i class="fas fa-chart-bar text-warning mb-2" style="font-size: 2rem;"></i>
                    <h2 class="mb-1 fw-bold" style="font-size: 2rem;">${{ number_format($averageSale ?? 0, 2) }}</h2>
                    <p class="mb-1 text-muted" style="font-size: 0.9rem;">Average Sale</p>
                    <small class="text-danger"><i class="fas fa-arrow-down me-1"></i>-2.1% from last month</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header py-3 px-4">
            <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title">Sales List</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                <i class="ri-add-line align-middle me-1"></i> New Sale
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
                <div class="col-md-6">
                    <h5 class="mb-0 fw-semibold">Sales History</h5>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                            <i class="fas fa-filter me-2"></i> Filters
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Filters -->
            <div class="collapse" id="filtersCollapse">
                <form method="GET" action="{{ route('sales.index') }}" class="row g-3 mb-4 p-3 bg-light rounded">
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
            </div>

            <!-- Sales Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Sale ID</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Customer</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Date</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Sale Info</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Total Amount</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Status</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales ?? [] as $sale)
                        <tr>
                            <td class="py-2 px-3">
                                <strong style="font-size: 1.1rem;">#{{ str_pad($sale->id ?? 0, 6, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td class="py-2 px-3">
                                <div>
                                    <strong style="font-size: 1.1rem;">{{ $sale->customer->name ?? 'N/A' }}</strong>
                                    @if(isset($sale->customer->email))
                                        <br><small class="text-muted">{{ $sale->customer->email }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-2 px-3" style="font-size: 1rem;">
                                        {{ $sale->created_at ? $sale->created_at->format('M d, Y') : 'N/A' }}
                                        <br><small class="text-muted">{{ $sale->created_at ? $sale->created_at->format('h:i A') : '' }}</small>
                                    </td>
                                    <td class="py-2 px-3" style="font-size: 1rem;">
                                        <div>
                                            <strong style="font-size: 1.1rem;">Sale #{{ str_pad($sale->id ?? 0, 6, '0', STR_PAD_LEFT) }}</strong>
                                            <br><small class="text-muted">{{ $sale->sale_date ? $sale->sale_date->format('M d, Y') : 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td class="py-2 px-3">
                                        <strong class="text-success" style="font-size: 1.2rem;">${{ number_format($sale->total_amount ?? 0, 2) }}</strong>
                                    </td>
                                    <td class="py-2 px-3">
                                        <span class="badge bg-{{ ($sale->status ?? 'pending') === 'completed' ? 'success' : (($sale->status ?? 'pending') === 'pending' ? 'warning' : 'secondary') }}" style="font-size: 0.9rem;">
                                            {{ ucfirst($sale->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-3">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('sales.show', $sale->id ?? 1) }}">
                                                    <i class="ri-eye-line align-middle me-2"></i> View
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('sales.edit', $sale->id ?? 1) }}">
                                                    <i class="ri-edit-line align-middle me-2"></i> Edit
                                                </a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete({{ $sale->id ?? 1 }})">
                                                    <i class="ri-delete-bin-line align-middle me-2"></i> Delete
                                                </a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="py-4">
                                            <i class="ri-shopping-cart-line display-4 text-muted"></i>
                                            <h5 class="mt-3">No Sales Found</h5>
                                            <p class="text-muted">There are no sales records to display.</p>
                                            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                                <i class="ri-add-line align-middle me-1"></i> Create First Sale
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
