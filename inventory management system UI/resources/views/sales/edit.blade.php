@extends('layouts.app')

@section('title', 'Edit Sale - Inventory Management System')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Sale</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sales.update', $sale) }}" id="saleForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Product</label>
                                <select class="form-select @error('product_id') is-invalid @enderror"
                                        id="product_id" name="product_id" required onchange="updateProductInfo()">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                data-price="{{ $product->price }}"
                                                data-stock="{{ $product->quantity }}"
                                                {{ old('product_id', $sale->product_id) == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} (Stock: {{ $product->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror"
                                        id="customer_id" name="customer_id" required>
                                    <option value="">Select Customer</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                                {{ old('customer_id', $sale->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" min="1"
                                       class="form-control @error('quantity') is-invalid @enderror"
                                       id="quantity" name="quantity" value="{{ old('quantity', $sale->quantity) }}"
                                       required onchange="calculateTotal()">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="stockInfo"></small>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="unit_price" class="form-label">Unit Price ($)</label>
                                <input type="number" step="0.01" readonly
                                       class="form-control" id="unit_price" value="{{ old('unit_price', $sale->unit_price) }}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="total_amount" class="form-label">Total Amount ($)</label>
                                <input type="number" step="0.01" readonly
                                       class="form-control" id="total_amount" value="{{ old('total_amount', $sale->total_amount) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="sale_date" class="form-label">Sale Date</label>
                        <input type="date" class="form-control @error('sale_date') is-invalid @enderror"
                               id="sale_date" name="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" required>
                        @error('sale_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back to Sales
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateProductInfo() {
    const select = document.getElementById('product_id');
    const option = select.options[select.selectedIndex];

    if (option.value) {
        const price = parseFloat(option.dataset.price);
        const stock = parseInt(option.dataset.stock);

        document.getElementById('unit_price').value = price.toFixed(2);
        document.getElementById('quantity').max = stock;
        document.getElementById('stockInfo').textContent = `Available: ${stock} units`;

        calculateTotal();
    } else {
        document.getElementById('unit_price').value = '0.00';
        document.getElementById('total_amount').value = '0.00';
        document.getElementById('stockInfo').textContent = '';
        document.getElementById('quantity').max = '';
    }
}

function calculateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const total = quantity * unitPrice;

    document.getElementById('total_amount').value = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', updateProductInfo);
</script>
@endsection
