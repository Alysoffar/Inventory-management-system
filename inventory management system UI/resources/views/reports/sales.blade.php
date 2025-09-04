@extends('layouts.app')

@section('title', 'Sales Reports - Inventory Management System')

@section('content')
<div class="row mb-4">
    <div class="col-md-6"><h2><i class="fas fa-chart-bar me-2"></i>Sales Reports</h2></div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-success" onclick="exportReport()"><i class="fas fa-file-pdf me-1"></i>Export PDF</button>
    </div>
</div>

<!-- Date Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.sales') }}">
            <div class="row">
                <div class="col-md-4"><label>Start Date</label><input type="date" name="start_date" class="form-control" value="{{ request('start_date', $startDate) }}"></div>
                <div class="col-md-4"><label>End Date</label><input type="date" name="end_date" class="form-control" value="{{ request('end_date', $endDate) }}"></div>
                <div class="col-md-4 d-flex align-items-end"><button class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button></div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card stats-card text-center">
            <div class="card-body"><i class="fas fa-shopping-cart fa-2x mb-3"></i><h3>{{ $totalSales }}</h3><p>Total Sales</p></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card stats-card text-center">
            <div class="card-body"><i class="fas fa-dollar-sign fa-2x mb-3"></i><h3>${{ number_format($totalRevenue, 2) }}</h3><p>Total Revenue</p></div>
        </div>
    </div>
</div>

<!-- Sales Table -->
<div class="card">
    <div class="card-header"><h5>Sales Details</h5></div>
    <div class="card-body">
        @if($sales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead><tr><th>Date</th><th>Product</th><th>Customer</th><th>Quantity</th><th>Unit Price</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                <td>{{ $sale->product->name }}</td>
                                <td>{{ $sale->customer->name }}</td>
                                <td>{{ $sale->quantity }}</td>
                                <td>${{ number_format($sale->unit_price, 2) }}</td>
                                <td>${{ number_format($sale->total_amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-muted">No sales found for the selected period.</p>
        @endif
    </div>
</div>

@push('scripts')
<script>
function exportReport() {
    // Placeholder for export functionality
    alert('Export functionality will be implemented soon. This will generate a PDF report of the sales data.');
}
</script>
@endpush
@endsection
