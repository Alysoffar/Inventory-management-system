@extends('layouts.app')

@section('title', 'Sale Details - Inventory Management System')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Sale Details</h5>
            </div>
            <div class="card-body">
                <!-- Sale Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Sale ID:</th>
                                <td><strong>#{{ $sale->id }}</strong></td>
                            </tr>
                            <tr>
                                <th>Date:</th>
                                <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <th>Customer:</th>
                                <td>{{ $sale->customer->name }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-success">{{ ucfirst($sale->status ?? 'completed') }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Sale Summary</h6>
                            <p class="mb-1"><strong>Total Items:</strong> {{ $sale->saleItems->sum('quantity') }}</p>
                            <p class="mb-0"><strong>Total Amount:</strong> <span class="h5">${{ number_format($sale->total_amount, 2) }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Products in Sale -->
                <h6><i class="fas fa-boxes me-2"></i>Products</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->saleItems as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    @if($item->product->sku)
                                        <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->unit_price, 2) }}</td>
                                <td><strong>${{ number_format($item->total_price, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th colspan="3">Total Amount:</th>
                                <th>${{ number_format($sale->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($sale->notes)
                <div class="mt-3">
                    <h6><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                    <div class="alert alert-light">
                        {{ $sale->notes }}
                    </div>
                </div>
                @endif

                <div class="mt-4">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Sales
                    </a>
                    <a href="{{ route('sales.edit', $sale->id) }}" class="btn btn-primary ms-2">
                        <i class="fas fa-edit me-1"></i>Edit Sale
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
