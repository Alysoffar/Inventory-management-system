@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<style>
/* AWS Cloudscape Design System - Customers Management */
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

.customers-management .card {
    border: 1px solid var(--aws-color-grey-200);
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.customers-management .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.customers-management .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
    padding: 1.5rem;
}

.customers-management .card-body {
    padding: 0;
}

.customers-management .page-title {
    font-size: 2rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 8px;
}

.customers-management .page-subtitle {
    font-size: 1.1rem;
    color: #212529 !important;
    margin: 0;
}

.customers-management .card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #212529 !important;
    margin: 0;
}

.customers-management .btn {
    font-size: 1rem;
    font-weight: 500;
    padding: 10px 16px;
    border-radius: 6px;
}

.customers-management .form-control {
    font-size: 1rem;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid var(--aws-color-grey-200);
}

.customers-management th {
    font-size: 1rem !important;
    font-weight: 600;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
}

.customers-management td {
    font-size: 1.1rem !important;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    border-bottom: 1px solid #f8f9fc;
}

.customers-management .badge {
    font-size: 1rem;
    padding: 6px 12px;
}

.customers-management .btn-sm {
    font-size: 0.9rem;
    padding: 8px 12px;
}

.customers-management h1, .customers-management h2, .customers-management h3, .customers-management h4, .customers-management h5, .customers-management h6,
.customers-management p, .customers-management span, .customers-management div, .customers-management a, .customers-management li {
    color: #212529 !important;
}

.customers-management .text-muted {
    color: #5f6b7a !important;
}

.customers-management .empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.customers-management .empty-state i {
    font-size: 4rem;
    color: #5f6b7a;
    margin-bottom: 2rem;
}

.customers-management .empty-state h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 1rem;
}

.customers-management .empty-state p {
    font-size: 1.1rem;
    color: #5f6b7a !important;
    margin-bottom: 2rem;
}
</style>

<div class="container-fluid customers-management">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">👥 Customers Management</h2>
                    <p class="page-subtitle">Manage your customer database</p>
                </div>
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Add New Customer
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="card-title">All Customers</h6>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('customers.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control me-3"
                               placeholder="Search customers..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i> Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Orders</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>
                                <strong>{{ $customer->name }}</strong>
                            </td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>
                                <span class="badge bg-info">{{ $customer->sales_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('customers.edit', $customer->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Edit Customer" style="color: #0d6efd; border-color: #0d6efd;">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('customers.show', $customer->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" style="color: #6c757d; border-color: #6c757d;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer->id) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="color: #dc3545; border-color: #dc3545;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <h4>No customers found</h4>
                                    <p>Start by adding your first customer to the system.</p>
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
