@extends('layouts.app')

@section('title', 'Sale Details - Inventory Management System')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-eye me-2"></i>Sale Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Date:</th>
                        <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <th>Product:</th>
                        <td>{{ $sale->product->name }}</td>
                    </tr>
                    <tr>
                        <th>Customer:</th>
                        <td>{{ $sale->customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Quantity:</th>
                        <td>{{ $sale->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Unit Price ($):</th>
                        <td>{{ number_format($sale->unit_price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Amount ($):</th>
                        <td><strong>{{ number_format($sale->total_amount, 2) }}</strong></td>
                    </tr>
                </table>

                <div class="mt-3">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Sales
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
