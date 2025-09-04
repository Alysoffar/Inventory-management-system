@extends('layouts.app')

@section('title', 'AI Inventory Predictions - Smart Forecasting')

@section('content')
<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-robot me-3"></i>AI Inventory Predictions
            </h1>
            <p class="page-subtitle">Advanced machine learning-powered inventory forecasting and recommendations</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('ai.predictions.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Prediction
                </a>
                <a href="{{ route('ai.insights') }}" class="btn btn-success">
                    <i class="fas fa-lightbulb me-2"></i>AI Insights
                </a>
            </div>
        </div>
    </div>
</div>

<!-- AI Status & Performance Cards -->
<div class="row section-spacing">
    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-brain fa-2x mb-3"></i>
                <h3 id="model-accuracy">{{ number_format($averageAccuracy, 1) }}%</h3>
                <p>Model Accuracy</p>
                <small class="text-light">Based on historical data</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x mb-3"></i>
                <h3>{{ $totalPredictions }}</h3>
                <p>Total Predictions</p>
                <small class="text-light">This month</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-server fa-2x mb-3"></i>
                <h3 id="api-status">
                    <span class="spinner-border spinner-border-sm" role="status"></span>
                </h3>
                <p>API Status</p>
                <small class="text-light">Real-time monitoring</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x mb-3"></i>
                <h3>~2.5s</h3>
                <p>Avg Response Time</p>
                <small class="text-light">Lightning fast predictions</small>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row section-spacing">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-zap me-2"></i>Quick Prediction</h5>
            </div>
            <div class="card-body">
                <form id="quick-prediction-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Product</label>
                            <select class="form-select" name="product_id" required>
                                <option value="">Select Product...</option>
                                <option value="1">Sample Product A</option>
                                <option value="2">Sample Product B</option>
                                <option value="3">Sample Product C</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Current Stock</label>
                            <input type="number" class="form-control" name="current_stock" value="150" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Expected Demand</label>
                            <input type="number" class="form-control" name="expected_demand" value="120" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-magic me-1"></i>Predict
                            </button>
                        </div>
                    </div>
                </form>
                
                <div id="quick-result" class="mt-4" style="display: none;">
                    <div class="alert alert-info">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Predicted Sales:</strong>
                                <div class="h5 text-primary" id="predicted-sales">--</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Stock Duration:</strong>
                                <div class="h6" id="stock-duration">--</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Reorder Status:</strong>
                                <div class="h6" id="reorder-status">--</div>
                            </div>
                            <div class="col-md-3">
                                <strong>Confidence:</strong>
                                <div class="h6" id="confidence-level">--</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>How It Works</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <i class="fas fa-database text-primary me-3 mt-1"></i>
                    <div>
                        <strong>Data Collection</strong>
                        <p class="small text-muted mb-0">AI analyzes historical sales, inventory levels, and market trends</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <i class="fas fa-brain text-success me-3 mt-1"></i>
                    <div>
                        <strong>ML Processing</strong>
                        <p class="small text-muted mb-0">Advanced LSTM neural networks process complex patterns</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <i class="fas fa-chart-line text-warning me-3 mt-1"></i>
                    <div>
                        <strong>Smart Recommendations</strong>
                        <p class="small text-muted mb-0">Get actionable insights and reorder suggestions</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Predictions -->
<div class="row section-spacing">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title"><i class="fas fa-history me-2"></i>Recent Predictions</h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" onclick="refreshPredictions()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <a href="{{ route('ai.predictions.create') }}" class="btn btn-outline-success">
                        <i class="fas fa-plus"></i> New
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(count($recentPredictions) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Predicted Sales</th>
                                    <th>Confidence</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPredictions as $prediction)
                                <tr>
                                    <td><strong>{{ $prediction['product_name'] }}</strong></td>
                                    <td>
                                        <span class="h6 text-primary">{{ number_format($prediction['predicted_sales'], 1) }}</span>
                                        <small class="text-muted">units</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $prediction['confidence'] == 'HIGH' ? 'success' : 'warning' }}">
                                            {{ $prediction['confidence'] }}
                                        </span>
                                    </td>
                                    <td>{{ $prediction['date'] }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $prediction['status'] }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-success btn-sm" title="Regenerate">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No predictions yet</h5>
                        <p class="text-muted">Start by creating your first AI-powered inventory prediction</p>
                        <a href="{{ route('ai.predictions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create First Prediction
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- AI Features Showcase -->
<div class="row section-spacing">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-magic fa-3x text-primary mb-3"></i>
                <h5>Smart Forecasting</h5>
                <p class="text-muted">LSTM neural networks analyze complex patterns in your inventory data</p>
                <a href="{{ route('ai.predictions.create') }}" class="btn btn-outline-primary">Try Now</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-lightbulb fa-3x text-success mb-3"></i>
                <h5>Business Intelligence</h5>
                <p class="text-muted">Get actionable recommendations to optimize your inventory strategy</p>
                <a href="{{ route('ai.insights') }}" class="btn btn-outline-success">View Insights</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-shield-alt fa-3x text-warning mb-3"></i>
                <h5>Risk Management</h5>
                <p class="text-muted">Identify potential stockouts and overstock situations before they happen</p>
                <button class="btn btn-outline-warning" onclick="showRiskAnalysis()">Learn More</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Check API status on page load
    checkApiStatus();
    
    // Quick prediction form
    $('#quick-prediction-form').on('submit', function(e) {
        e.preventDefault();
        makeQuickPrediction();
    });
});

function checkApiStatus() {
    $.ajax({
        url: '/api/ai/health',
        method: 'GET',
        success: function(response) {
            if (response.status === 'healthy') {
                $('#api-status').html('<i class="fas fa-check-circle text-light"></i>');
            } else {
                $('#api-status').html('<i class="fas fa-exclamation-triangle text-warning"></i>');
            }
        },
        error: function() {
            $('#api-status').html('<i class="fas fa-times-circle text-danger"></i>');
        }
    });
}

function makeQuickPrediction() {
    const formData = new FormData(document.getElementById('quick-prediction-form'));
    formData.append('price', '25.50'); // Default price
    formData.append('prediction_date', new Date().toISOString().split('T')[0]);
    
    // Show loading state
    const submitBtn = $('#quick-prediction-form button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Predicting...').prop('disabled', true);
    
    $.ajax({
        url: '/ai/predict',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                displayQuickResult(response.prediction);
            } else {
                alert('Prediction failed: ' + (response.error || 'Unknown error'));
            }
        },
        error: function(xhr) {
            const error = xhr.responseJSON?.error || 'Prediction service unavailable';
            alert('Error: ' + error);
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
}

function displayQuickResult(prediction) {
    $('#predicted-sales').text(prediction.prediction.predicted_sales + ' units');
    $('#stock-duration').text(prediction.inventory_status.days_of_stock + ' days');
    $('#reorder-status').html(
        prediction.inventory_status.should_reorder 
            ? '<span class="badge bg-warning">Reorder Needed</span>'
            : '<span class="badge bg-success">Stock OK</span>'
    );
    $('#confidence-level').html(
        '<span class="badge bg-' + 
        (prediction.prediction.confidence_level === 'HIGH' ? 'success' : 'warning') + 
        '">' + prediction.prediction.confidence_level + '</span>'
    );
    
    $('#quick-result').slideDown();
}

function refreshPredictions() {
    location.reload();
}

function showRiskAnalysis() {
    // Show modal or navigate to risk analysis page
    alert('Risk analysis feature coming soon! This will show detailed risk assessments for your inventory.');
}

// Auto-refresh API status every 30 seconds
setInterval(checkApiStatus, 30000);
</script>
@endsection
