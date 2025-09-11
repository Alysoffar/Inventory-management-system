@extends('layouts.app')

@section('title', 'Add New Product')

@section('styles')
<style>
    /* Enhanced form styling for better visibility */
    .form-control, .form-select {
        font-size: 18px !important;
        padding: 15px 20px !important;
        min-height: 55px !important;
        border: 2px solid #e0e6ed !important;
        border-radius: 8px !important;
        background-color: #ffffff !important;
        color: #232f3e !important;
        font-weight: 500 !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #ff9900 !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 153, 0, 0.25) !important;
        background-color: #ffffff !important;
        color: #232f3e !important;
    }
    
    .form-label {
        font-size: 16px !important;
        font-weight: 600 !important;
        color: #232f3e !important;
        margin-bottom: 10px !important;
    }
    
    .btn {
        font-size: 16px !important;
        padding: 12px 30px !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        min-height: 50px !important;
    }
    
    .btn-primary {
        background-color: #ff9900 !important;
        border-color: #ff9900 !important;
        color: #ffffff !important;
    }
    
    .btn-primary:hover {
        background-color: #e88900 !important;
        border-color: #e88900 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(255, 153, 0, 0.3) !important;
    }
    
    .btn-secondary {
        background-color: #687078 !important;
        border-color: #687078 !important;
        color: #ffffff !important;
    }
    
    .btn-secondary:hover {
        background-color: #5a6268 !important;
        border-color: #5a6268 !important;
    }
    
    .card {
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
    
    .card-header {
        font-size: 20px !important;
        font-weight: 600 !important;
        padding: 20px 30px !important;
        border-radius: 12px 12px 0 0 !important;
    }
    
    .card-body {
        padding: 30px !important;
    }
    
    h2, h3, h4, h5 {
        color: #232f3e !important;
        font-weight: 600 !important;
    }
    
    .breadcrumb {
        font-size: 16px !important;
    }
    
    textarea.form-control {
        min-height: 120px !important;
    }
    
    /* Input group styling */
    .input-group-text {
        font-size: 16px !important;
        padding: 15px 20px !important;
        background-color: #f8f9fa !important;
        border: 2px solid #e0e6ed !important;
        color: #232f3e !important;
        font-weight: 500 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 text-dark">Add New Product</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Product Information</h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('products.store') }}" method="POST">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label fw-bold">Product Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required
                                       placeholder="Enter product name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sku" class="form-label fw-bold">SKU *</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror"
                                       id="sku" name="sku" value="{{ old('sku') }}" required
                                       placeholder="Enter unique SKU">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label fw-bold">Category *</label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror"
                                       id="category" name="category" value="{{ old('category') }}" required
                                       placeholder="Enter product category">
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="supplier_id" class="form-label fw-bold">Supplier</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror"
                                        id="supplier_id" name="supplier_id">
                                    <option value="">Select a supplier (optional)</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="Enter product description">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pricing Information -->
                        <h6 class="fw-bold mt-4 mb-3"><i class="fas fa-dollar-sign"></i> Pricing Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label fw-bold">Selling Price ($) *</label>
                                <input type="number" step="0.01" min="0"
                                       class="form-control @error('price') is-invalid @enderror"
                                       id="price" name="price" value="{{ old('price') }}" required
                                       placeholder="0.00">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="cost_price" class="form-label fw-bold">Cost Price ($)</label>
                                <input type="number" step="0.01" min="0"
                                       class="form-control @error('cost_price') is-invalid @enderror"
                                       id="cost_price" name="cost_price" value="{{ old('cost_price') }}"
                                       placeholder="0.00">
                                @error('cost_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Stock Information -->
                        <h6 class="fw-bold mt-4 mb-3"><i class="fas fa-boxes"></i> Stock Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="stock_quantity" class="form-label fw-bold">Initial Stock Quantity *</label>
                                <input type="number" min="0"
                                       class="form-control @error('stock_quantity') is-invalid @enderror"
                                       id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="minimum_stock_level" class="form-label fw-bold">Minimum Stock Level *</label>
                                <input type="number" min="0"
                                       class="form-control @error('minimum_stock_level') is-invalid @enderror"
                                       id="minimum_stock_level" name="minimum_stock_level" value="{{ old('minimum_stock_level', 10) }}" required>
                                @error('minimum_stock_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="maximum_stock_level" class="form-label fw-bold">Maximum Stock Level *</label>
                                <input type="number" min="1"
                                       class="form-control @error('maximum_stock_level') is-invalid @enderror"
                                       id="maximum_stock_level" name="maximum_stock_level" value="{{ old('maximum_stock_level', 100) }}" required>
                                @error('maximum_stock_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reorder_quantity" class="form-label fw-bold">Reorder Quantity *</label>
                                <input type="number" min="1"
                                       class="form-control @error('reorder_quantity') is-invalid @enderror"
                                       id="reorder_quantity" name="reorder_quantity" value="{{ old('reorder_quantity', 50) }}" required>
                                @error('reorder_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-bold">Status *</label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Location Information -->
                        <h6 class="fw-bold mt-4 mb-3"><i class="fas fa-map-marker-alt"></i> Location Information</h6>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="location" class="form-label fw-bold">Location/Warehouse</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location') }}"
                                       placeholder="e.g., Warehouse A, Aisle 5">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="latitude" class="form-label fw-bold">Latitude</label>
                                <input type="number" step="0.000001"
                                       class="form-control @error('latitude') is-invalid @enderror"
                                       id="latitude" name="latitude" value="{{ old('latitude') }}"
                                       placeholder="e.g., 40.712776">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="longitude" class="form-label fw-bold">Longitude</label>
                                <input type="number" step="0.000001"
                                       class="form-control @error('longitude') is-invalid @enderror"
                                       id="longitude" name="longitude" value="{{ old('longitude') }}"
                                       placeholder="e.g., -74.005974">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Auto Reorder -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto_reorder" name="auto_reorder" value="1"
                                       {{ old('auto_reorder') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="auto_reorder">
                                    <i class="fas fa-sync-alt"></i> Enable Auto Reorder
                                </label>
                                <small class="form-text text-muted d-block">
                                    Automatically create reorder notifications when stock falls below minimum level
                                </small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left"></i> Back to Products
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Create Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
