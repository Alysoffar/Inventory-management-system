@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2 fw-semibold">👥 Customers Management</h2>
                    <p class="mb-0 text-muted">Manage your customer database</p>
                </div>
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Add New Customer
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header py-2 px-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-0 fw-semibold">All Customers</h6>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('customers.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control me-2"
                               placeholder="Search customers..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search me-2"></i> Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">ID</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Name</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Email</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Phone</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Total Orders</th>
                            <th class="py-2 px-3" style="font-size: 0.9rem;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td class="py-2 px-3" style="font-size: 1rem;">{{ $customer->id }}</td>
                            <td class="py-2 px-3">
                                <strong style="font-size: 1.1rem;">{{ $customer->name }}</strong>
                            </td>
                            <td class="py-2 px-3" style="font-size: 1rem;">{{ $customer->email }}</td>
                            <td class="py-2 px-3" style="font-size: 1rem;">{{ $customer->phone }}</td>
                            <td class="py-2 px-3">
                                <span class="badge bg-info" style="font-size: 0.9rem;">{{ $customer->sales_count ?? 0 }}</span>
                            </td>
                                                        <td class="py-2 px-3">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('customers.show', $customer->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                    </a>
                                    <a href="{{ route('customers.edit', $customer->id) }}" 
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash" style="font-size: 0.8rem;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-center">
                                    <i class="fas fa-users fa-3x text-muted mb-4"></i>
                                    <h4 class="text-muted mb-3">No customers found</h4>
                                    <p class="text-muted mb-4">Start by adding your first customer to the system.</p>
                                    <a href="{{ route('customers.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add First Customer
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
        <div class="card-footer py-3 px-4">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
