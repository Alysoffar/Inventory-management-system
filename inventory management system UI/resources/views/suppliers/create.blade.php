@extends('layouts.app')

@section('title', 'Add New Supplier')

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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h2 text-dark">Add New Supplier</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-truck"></i> Supplier Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="name" class="form-label">Company Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Suppliers
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
