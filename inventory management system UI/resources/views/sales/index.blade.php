@extends('layouts.app')

@section('title', 'Sales Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Sales Management</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sales</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Sales</p>
                            <h4 class="mb-2">{{ $totalSales ?? 0 }}</h4>
                            <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><i class="ri-arrow-right-up-line me-1 align-middle"></i>+12.5%</span>from previous month</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="ri-shopping-cart-line font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Total Revenue</p>
                            <h4 class="mb-2">${{ number_format($totalRevenue ?? 0, 2) }}</h4>
                            <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><i class="ri-arrow-right-up-line me-1 align-middle"></i>+8.3%</span>from previous month</p>
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
            <div class="card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Today's Sales</p>
                            <h4 class="mb-2">{{ $todaySales ?? 0 }}</h4>
                            <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><i class="ri-arrow-right-up-line me-1 align-middle"></i>+5.2%</span>from yesterday</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="ri-calendar-line font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Average Sale</p>
                            <h4 class="mb-2">${{ number_format($averageSale ?? 0, 2) }}</h4>
                            <p class="text-muted mb-0"><span class="text-danger fw-bold font-size-12 me-2"><i class="ri-arrow-right-down-line me-1 align-middle"></i>-2.1%</span>from previous month</p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="ri-bar-chart-line font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
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
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="ri-search-line align-middle me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Sales Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Sale Info</th>
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
                                        <div>
                                            <strong>Sale #{{ str_pad($sale->id ?? 0, 6, '0', STR_PAD_LEFT) }}</strong>
                                            <br><small class="text-muted">{{ $sale->sale_date ? $sale->sale_date->format('M d, Y') : 'N/A' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">${{ number_format($sale->total_amount ?? 0, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ ($sale->status ?? 'pending') === 'completed' ? 'success' : (($sale->status ?? 'pending') === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($sale->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>
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
