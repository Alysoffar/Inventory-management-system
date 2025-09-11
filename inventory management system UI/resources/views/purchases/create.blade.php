@extends('layouts.app')

@section('title', 'Create New Purchase')

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
    
    .table {
        font-size: 16px !important;
    }
    
    .table th {
        font-size: 16px !important;
        font-weight: 600 !important;
        padding: 15px !important;
    }
    
    .table td {
        padding: 15px !important;
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
            <h2 class="h2 text-dark">Create New Purchase Order</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}">Purchases</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-shopping-bag"></i> Purchase Order Information</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
                        @csrf
                        
                        <!-- Supplier Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="supplier_id" class="form-label">Supplier *</label>
                                <select class="form-select @error('supplier_id') is-invalid @enderror" 
                                        id="supplier_id" name="supplier_id" required>
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }} - {{ $supplier->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="order_date" class="form-label">Order Date *</label>
                                <input type="date" class="form-control @error('order_date') is-invalid @enderror"
                                       id="order_date" name="order_date" value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="expected_date" class="form-label fw-bold">Expected Delivery Date</label>
                                <input type="date" class="form-control @error('expected_date') is-invalid @enderror"
                                       id="expected_date" name="expected_date" value="{{ old('expected_date') }}">
                                @error('expected_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-bold">Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="ordered" {{ old('status') == 'ordered' ? 'selected' : '' }}>Ordered</option>
                                    <option value="received" {{ old('status') == 'received' ? 'selected' : '' }}>Received</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-boxes"></i> Products to Order</h5>
                            </div>
                            <div class="card-body">
                                <div id="productList">
                                    <!-- Product items will be added here -->
                                </div>
                                <button type="button" class="btn btn-primary btn-lg" id="addProduct">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="notes" class="form-label fw-bold">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Special instructions, delivery requirements, etc.">{{ old('notes') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Purchase Summary</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal:</span>
                                            <span id="subtotal">$0.00</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Tax (10%):</span>
                                            <span id="tax">$0.00</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span id="total">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields for calculations -->
                        <input type="hidden" id="total_amount" name="total_amount" value="0">

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Purchases
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Selection Template -->
<template id="productItemTemplate">
    <div class="product-item border rounded p-4 mb-3 bg-light">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label">Product *</label>
                <select class="form-select product-select" name="products[__INDEX__][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-cost="{{ $product->cost ?? $product->price }}">
                            {{ $product->name }} (Stock: {{ $product->quantity ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantity *</label>
                <input type="number" class="form-control quantity-input" 
                       name="products[__INDEX__][quantity]" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit Cost *</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" class="form-control cost-input" 
                           name="products[__INDEX__][unit_cost]" step="0.01" min="0" required>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">Total</label>
                <input type="text" class="form-control item-total" readonly 
                       style="background-color: #f8f9fa; font-weight: bold;">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-product w-100">
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
        </div>
    </div>
</template>

@endsection

@section('scripts')
<script>
let productIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Purchase form loaded'); // Debug log
    
    const addProductBtn = document.getElementById('addProduct');
    const productList = document.getElementById('productList');
    const template = document.getElementById('productItemTemplate');
    
    console.log('Elements found:', {
        addProductBtn: !!addProductBtn,
        productList: !!productList,
        template: !!template
    }); // Debug log

    if (!addProductBtn || !productList || !template) {
        console.error('Required elements not found');
        return;
    }

    // Add first product row on load
    addProductRow();

    addProductBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Add product clicked'); // Debug log
        addProductRow();
    });

    function addProductRow() {
        console.log('Adding product row', productIndex); // Debug log
        
        const clone = template.content.cloneNode(true);
        
        // Replace __INDEX__ with actual index
        const tempDiv = document.createElement('div');
        tempDiv.appendChild(clone);
        tempDiv.innerHTML = tempDiv.innerHTML.replace(/__INDEX__/g, productIndex);
        
        // Add the modified content to productList
        while (tempDiv.firstChild) {
            productList.appendChild(tempDiv.firstChild);
        }
        
        // Setup the newly added row
        const newRow = productList.lastElementChild;
        if (newRow) {
            setupProductRow(newRow);
        }
        
        productIndex++;
        console.log('Product row added, new index:', productIndex); // Debug log
    }

    function setupProductRow(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const costInput = row.querySelector('.cost-input');
        const totalInput = row.querySelector('.item-total');
        const removeBtn = row.querySelector('.remove-product');

        if (!productSelect || !quantityInput || !costInput || !totalInput || !removeBtn) {
            console.error('Could not find all elements in product row');
            return;
        }

        productSelect.addEventListener('change', function() {
            const selectedOption = this.selectedOptions[0];
            if (selectedOption && selectedOption.value) {
                const cost = parseFloat(selectedOption.getAttribute('data-cost')) || 0;
                costInput.value = cost.toFixed(2);
                calculateItemTotal();
            } else {
                costInput.value = '';
                totalInput.value = '';
            }
            calculateSummary();
        });

        quantityInput.addEventListener('input', function() {
            calculateItemTotal();
            calculateSummary();
        });

        costInput.addEventListener('input', function() {
            calculateItemTotal();
            calculateSummary();
        });

        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (productList.children.length > 1) {
                row.remove();
                calculateSummary();
            } else {
                alert('You must have at least one product in the purchase order.');
            }
        });

        function calculateItemTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const cost = parseFloat(costInput.value) || 0;
            const total = quantity * cost;
            totalInput.value = '$' + total.toFixed(2);
        }
    }

    function calculateSummary() {
        let subtotal = 0;
        
        document.querySelectorAll('.product-item').forEach(function(item) {
            const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
            const cost = parseFloat(item.querySelector('.cost-input').value) || 0;
            subtotal += quantity * cost;
        });
        
        const tax = subtotal * 0.1; // 10% tax
        const total = subtotal + tax;
        
        const subtotalEl = document.getElementById('subtotal');
        const taxEl = document.getElementById('tax');
        const totalEl = document.getElementById('total');
        const totalAmountEl = document.getElementById('total_amount');
        
        if (subtotalEl) subtotalEl.textContent = '$' + subtotal.toFixed(2);
        if (taxEl) taxEl.textContent = '$' + tax.toFixed(2);
        if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
        if (totalAmountEl) totalAmountEl.value = total.toFixed(2);
    }

    // Initial calculation
    calculateSummary();
});
</script>
@endsection
