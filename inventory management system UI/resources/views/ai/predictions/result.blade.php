@extends('layouts.app')

@section('title', 'AI Prediction Results - ' . $product->name)

@section('content')
<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-chart-line me-3"></i>Prediction Results
            </h1>
            <p class="page-subtitle">AI-powered forecast for <strong>{{ $product->name }}</strong></p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('ai.predictions.create') }}" class="btn btn-outline-primary">
                    <i class="fas fa-plus me-2"></i>New Prediction
                </a>
                <button class="btn btn-success" onclick="exportResults()">
                    <i class="fas fa-download me-2"></i>Export
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Prediction Summary Cards -->
<div class="row section-spacing">
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-magic fa-2x mb-3"></i>
                <h3>{{ number_format($prediction['prediction']['predicted_sales'], 1) }}</h3>
                <p>Predicted Sales</p>
                <small class="text-light">Units for next period</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card border-{{ $prediction['prediction']['confidence_level'] == 'HIGH' ? 'success' : 'warning' }}">
            <div class="card-body text-center">
                <i class="fas fa-shield-alt fa-2x mb-3"></i>
                <h3>{{ $prediction['prediction']['confidence_level'] }}</h3>
                <p>Confidence Level</p>
                <small class="text-light">{{ $prediction['prediction']['model_accuracy'] }} accurate</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card border-info">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x mb-3"></i>
                <h3>{{ number_format($prediction['inventory_status']['days_of_stock'], 1) }}</h3>
                <p>Days of Stock</p>
                <small class="text-light">At current demand</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card border-{{ $prediction['inventory_status']['should_reorder'] ? 'warning' : 'success' }}">
            <div class="card-body text-center">
                <i class="fas fa-{{ $prediction['inventory_status']['should_reorder'] ? 'exclamation-triangle' : 'check-circle' }} fa-2x mb-3"></i>
                <h3>{{ $prediction['inventory_status']['should_reorder'] ? 'REORDER' : 'OK' }}</h3>
                <p>Stock Status</p>
                <small class="text-light">
                    {{ $prediction['inventory_status']['should_reorder'] ? 'Action needed' : 'Stock sufficient' }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Analysis -->
<div class="row section-spacing">
    <!-- Main Results -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-analytics me-2"></i>Detailed Analysis</h5>
            </div>
            <div class="card-body">
                <!-- Prediction Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary">Inventory Forecast</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Current Stock:</strong></td>
                                <td>{{ number_format($prediction['inventory_status']['current_stock']) }} units</td>
                            </tr>
                            <tr>
                                <td><strong>Predicted Sales:</strong></td>
                                <td>{{ number_format($prediction['prediction']['predicted_sales'], 1) }} units</td>
                            </tr>
                            <tr>
                                <td><strong>Safety Stock:</strong></td>
                                <td>{{ number_format($prediction['inventory_status']['safety_stock_level']) }} units</td>
                            </tr>
                            <tr class="table-{{ $prediction['inventory_status']['should_reorder'] ? 'warning' : 'success' }}">
                                <td><strong>Reorder Quantity:</strong></td>
                                <td>{{ number_format($prediction['inventory_status']['recommended_order_qty']) }} units</td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-success">Financial Impact</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Potential Revenue:</strong></td>
                                <td>${{ number_format($prediction['financial_impact']['potential_revenue'], 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lost Sales Risk:</strong></td>
                                <td class="text-danger">${{ number_format($prediction['financial_impact']['lost_sales_risk'], 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Carrying Cost/Day:</strong></td>
                                <td>${{ number_format($prediction['financial_impact']['carrying_cost_per_day'], 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Reorder Investment:</strong></td>
                                <td>${{ number_format($prediction['financial_impact']['reorder_cost'], 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Visual Chart Placeholder -->
                <div class="chart-container mb-4">
                    <h6 class="text-info">Demand Forecast Visualization</h6>
                    <canvas id="demandChart" width="400" height="200"></canvas>
                </div>

                <!-- Input Parameters Used -->
                <div class="alert alert-light">
                    <h6><i class="fas fa-cog me-2"></i>Prediction Parameters</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled small">
                                <li><strong>Product:</strong> {{ $product->name }}</li>
                                <li><strong>Category:</strong> {{ $product->category ?? 'General' }}</li>
                                <li><strong>Current Stock:</strong> {{ $predictionData['current_stock'] }} units</li>
                                <li><strong>Expected Demand:</strong> {{ $predictionData['expected_demand'] }} units</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled small">
                                <li><strong>Price:</strong> ${{ number_format($predictionData['price'], 2) }}</li>
                                <li><strong>Prediction Date:</strong> {{ $predictionData['date'] }}</li>
                                <li><strong>Model Version:</strong> LSTM v1.0</li>
                                <li><strong>Generated:</strong> {{ $prediction['prediction']['prediction_date'] }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations Sidebar -->
    <div class="col-md-4">
        <!-- Action Recommendations -->
        <div class="card content-spacing">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-lightbulb me-2"></i>AI Recommendations</h5>
            </div>
            <div class="card-body">
                @if(isset($prediction['recommendations']) && count($prediction['recommendations']) > 0)
                    @foreach($prediction['recommendations'] as $recommendation)
                        <div class="alert alert-{{ $recommendation['priority'] == 'URGENT' ? 'danger' : ($recommendation['priority'] == 'HIGH' ? 'warning' : 'info') }} p-3 mb-3">
                            <div class="d-flex align-items-start">
                                <i class="{{ $recommendation['icon'] ?? 'fas fa-info-circle' }} me-2 mt-1"></i>
                                <div>
                                    <h6 class="alert-heading mb-1">{{ $recommendation['action'] }}</h6>
                                    <p class="small mb-1">{{ $recommendation['description'] }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $recommendation['timeframe'] }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>All Good!</strong><br>
                        <small>No immediate actions required. Your inventory levels look healthy.</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Risk Assessment -->
        <div class="card content-spacing">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-shield-alt me-2"></i>Risk Assessment</h5>
            </div>
            <div class="card-body">
                @if(isset($prediction['risk_factors']) && count($prediction['risk_factors']) > 0)
                    @foreach($prediction['risk_factors'] as $risk)
                        <div class="border-start border-{{ $risk['level'] == 'HIGH' ? 'danger' : 'warning' }} border-3 ps-3 mb-3">
                            <h6 class="text-{{ $risk['level'] == 'HIGH' ? 'danger' : 'warning' }}">{{ $risk['type'] }}</h6>
                            <p class="small mb-1">{{ $risk['description'] }}</p>
                            <small class="text-muted">Impact: {{ $risk['impact'] }}</small>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-success mb-0">Low Risk Profile</p>
                        <small class="text-muted">No significant risks identified</small>
                    </div>
                @endif
            </div>
        </div>

        <!-- Model Performance -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-brain me-2"></i>Model Performance</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-primary">{{ $prediction['model_info']['mape'] }}</h5>
                            <small class="text-muted">MAPE Error</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success">{{ $prediction['model_info']['r2_score'] }}</h5>
                        <small class="text-muted">R² Score</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="small text-muted">
                    <p><strong>Last Updated:</strong> {{ $prediction['model_info']['last_updated'] }}</p>
                    <p class="mb-0"><strong>Algorithm:</strong> LSTM Neural Network with time series analysis</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row section-spacing">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h6 class="mb-1">Ready to take action?</h6>
                        <p class="text-muted mb-0">Use these recommendations to optimize your inventory management</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="btn-group">
                            @if($prediction['inventory_status']['should_reorder'])
                                <button class="btn btn-warning" onclick="createPurchaseOrder()">
                                    <i class="fas fa-shopping-cart me-2"></i>Create Purchase Order
                                </button>
                            @endif
                            <button class="btn btn-primary" onclick="regeneratePrediction()">
                                <i class="fas fa-redo me-2"></i>Regenerate
                            </button>
                            <button class="btn btn-success" onclick="exportResults()">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    createDemandChart();
});

function createDemandChart() {
    const ctx = document.getElementById('demandChart').getContext('2d');
    
    // Generate sample forecast data
    const labels = [];
    const currentStock = {{ $prediction['inventory_status']['current_stock'] }};
    const predictedSales = {{ $prediction['prediction']['predicted_sales'] }};
    const daysOfStock = {{ $prediction['inventory_status']['days_of_stock'] }};
    
    const stockData = [];
    const demandData = [];
    
    for (let i = 0; i <= 14; i++) {
        const date = new Date();
        date.setDate(date.getDate() + i);
        labels.push(date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
        
        // Simulate stock depletion
        const dailyDemand = predictedSales / 7;
        const remainingStock = Math.max(0, currentStock - (dailyDemand * i));
        stockData.push(remainingStock);
        demandData.push(dailyDemand);
    }
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Projected Stock Level',
                    data: stockData,
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Daily Demand',
                    data: demandData,
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: false,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: '14-Day Stock and Demand Forecast'
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Units'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });
}

function regeneratePrediction() {
    if (confirm('Are you sure you want to regenerate this prediction? This will create a new forecast.')) {
        window.location.href = '{{ route("ai.predictions.create") }}?product_id={{ $product->id }}';
    }
}

function createPurchaseOrder() {
    const orderQty = {{ $prediction['inventory_status']['recommended_order_qty'] }};
    const productId = {{ $product->id }};
    
    if (confirm(`Create a purchase order for ${orderQty} units of {{ $product->name }}?`)) {
        // Redirect to purchase creation with pre-filled data
        window.location.href = `/purchases/create?product_id=${productId}&quantity=${orderQty}&suggested=true`;
    }
}

function exportResults() {
    alert('Export functionality will be implemented soon. This will generate a comprehensive PDF report.');
}
</script>
@endsection
