@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<style>
/* AWS Cloudscape Design System - Suppliers Management */
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

.suppliers-management .card {
    border: 1px solid var(--aws-color-grey-200);
    border-radius: 8px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.suppliers-management .card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.suppliers-management .card-header {
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
    padding: 1.5rem;
}

.suppliers-management .card-body {
    padding: 0;
}

.suppliers-management .page-title {
    font-size: 2rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 8px;
}

.suppliers-management .page-subtitle {
    font-size: 1.1rem;
    color: #212529 !important;
    margin: 0;
}

.suppliers-management .card-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #212529 !important;
    margin: 0;
}

.suppliers-management .btn {
    font-size: 1rem;
    font-weight: 500;
    padding: 10px 16px;
    border-radius: 6px;
}

.suppliers-management .form-control {
    font-size: 1rem;
    padding: 10px 12px;
    border-radius: 6px;
    border: 1px solid var(--aws-color-grey-200);
}

.suppliers-management th {
    font-size: 1rem !important;
    font-weight: 600;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    background: #f8f9fc;
    border-bottom: 1px solid var(--aws-color-grey-200);
}

.suppliers-management td {
    font-size: 1.1rem !important;
    color: #212529 !important;
    padding: 1rem 1rem !important;
    border-bottom: 1px solid #f8f9fc;
}

.suppliers-management .badge {
    font-size: 1rem;
    padding: 6px 12px;
}

.suppliers-management .btn-sm {
    font-size: 0.9rem;
    padding: 8px 12px;
}

.suppliers-management h1, .suppliers-management h2, .suppliers-management h3, .suppliers-management h4, .suppliers-management h5, .suppliers-management h6,
.suppliers-management p, .suppliers-management span, .suppliers-management div, .suppliers-management a, .suppliers-management li {
    color: #212529 !important;
}

.suppliers-management .text-muted {
    color: #5f6b7a !important;
}

.suppliers-management .empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.suppliers-management .empty-state i {
    font-size: 4rem;
    color: #5f6b7a;
    margin-bottom: 2rem;
}

.suppliers-management .empty-state h4 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #212529 !important;
    margin-bottom: 1rem;
}

.suppliers-management .empty-state p {
    font-size: 1.1rem;
    color: #5f6b7a !important;
    margin-bottom: 2rem;
}
</style>

<div class="container-fluid suppliers-management">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">🚚 Suppliers Management</h2>
                    <p class="page-subtitle">Manage your supplier database</p>
                </div>
                <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Add New Supplier
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="card-title">All Suppliers</h6>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="{{ route('suppliers.index') }}" class="d-flex">
                        <input type="text" name="search" class="form-control me-3"
                               placeholder="Search suppliers..." value="{{ request('search') }}">
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
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Orders</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr>
                            <td>{{ $supplier->id }}</td>
                            <td>
                                <strong>{{ $supplier->name }}</strong>
                            </td>
                            <td>{{ $supplier->email }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>
                                <span class="badge bg-info">{{ $supplier->purchases_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this supplier?')">
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
                                    <i class="fas fa-truck-loading"></i>
                                    <h4>No suppliers found</h4>
                                    <p>Start by adding your first supplier to the system.</p>
                                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Add First Supplier
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($suppliers->hasPages())
        <div class="card-footer py-3 px-4">
            {{ $suppliers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
