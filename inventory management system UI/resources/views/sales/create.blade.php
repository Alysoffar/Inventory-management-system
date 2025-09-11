@extends('layouts.app')

@section('title', 'Create New Sale')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="h3 text-dark">Create New Sale</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart"></i> Sale Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('sales.store') }}" method="POST" id="saleForm">
                        @csrf
                        
                        <!-- Customer Selection -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="customer_id" class="form-label fw-bold">Customer *</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" 
                                        id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->email }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="sale_date" class="form-label fw-bold">Sale Date *</label>
                                <input type="datetime-local" class="form-control @error('sale_date') is-invalid @enderror"
                                       id="sale_date" name="sale_date" value="{{ old('sale_date', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('sale_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Products Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Products</h6>
                            </div>
                            <div class="card-body">
                                <div id="productList">
                                    <!-- Product items will be added here -->
                                </div>
                                <button type="button" class="btn btn-outline-primary" id="addProduct">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="notes" class="form-label fw-bold">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6>Sale Summary</h6>
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

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Sales
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Sale
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
    <div class="product-item border rounded p-3 mb-3">
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Product</label>
                <select class="form-select product-select" name="products[__INDEX__][product_id]" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-stock="{{ $product->quantity }}">
                            {{ $product->name }} (Stock: {{ $product->quantity }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control quantity-input" 
                       name="products[__INDEX__][quantity]" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unit Price</label>
                <input type="number" class="form-control price-input" 
                       name="products[__INDEX__][unit_price]" step="0.01" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label">Total</label>
                <input type="text" class="form-control item-total" readonly>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-product">
                    <i class="fas fa-trash"></i>
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
    const addProductBtn = document.getElementById('addProduct');
    const productList = document.getElementById('productList');
    const template = document.getElementById('productItemTemplate');

    // Add first product row
    addProductRow();

    addProductBtn.addEventListener('click', addProductRow);

    function addProductRow() {
        const clone = template.content.cloneNode(true);
        
        // Replace __INDEX__ with actual index
        clone.innerHTML = clone.innerHTML.replace(/__INDEX__/g, productIndex);
        
        productList.appendChild(clone);
        
        // Add event listeners to the new row
        const newRow = productList.lastElementChild;
        setupProductRow(newRow);
        
        productIndex++;
    }

    function setupProductRow(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const priceInput = row.querySelector('.price-input');
        const totalInput = row.querySelector('.item-total');
        const removeBtn = row.querySelector('.remove-product');

        productSelect.addEventListener('change', function() {
            const selectedOption = this.selectedOptions[0];
            if (selectedOption.value) {
                const price = parseFloat(selectedOption.getAttribute('data-price'));
                priceInput.value = price.toFixed(2);
                calculateItemTotal();
            } else {
                priceInput.value = '';
                totalInput.value = '';
            }
            calculateSummary();
        });

        quantityInput.addEventListener('input', function() {
            calculateItemTotal();
            calculateSummary();
        });

        removeBtn.addEventListener('click', function() {
            if (productList.children.length > 1) {
                row.remove();
                calculateSummary();
            }
        });

        function calculateItemTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            totalInput.value = '$' + total.toFixed(2);
        }
    }

    function calculateSummary() {
        let subtotal = 0;
        
        document.querySelectorAll('.product-item').forEach(function(item) {
            const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(item.querySelector('.price-input').value) || 0;
            subtotal += quantity * price;
        });
        
        const tax = subtotal * 0.1; // 10% tax
        const total = subtotal + tax;
        
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('tax').textContent = '$' + tax.toFixed(2);
        document.getElementById('total').textContent = '$' + total.toFixed(2);
    }
});
</script>
@endsection
