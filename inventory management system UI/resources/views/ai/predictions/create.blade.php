@extends('layouts.app')

@section('title', 'Create AI Prediction - Advanced Forecasting')

@section('content')
<!-- Page Header -->
<div class="page-header section-spacing">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="page-title">
                <i class="fas fa-magic me-3"></i>Create AI Prediction
            </h1>
            <p class="page-subtitle">Generate intelligent inventory forecasts with machine learning</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('ai.predictions.index') }}" class="btn btn-outline-secondary" style="color: #6c757d; border-color: #6c757d;">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Prediction Form -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-cogs me-2"></i>Prediction Parameters</h5>
            </div>
            <div class="card-body">
                <form id="prediction-form" action="{{ route('ai.predict') }}" method="POST">
                    @csrf
                    
                    <!-- Product Selection -->
                    <div class="form-group">
                        <label class="form-label">Select Product</label>
                        <select class="form-select" name="product_id" id="product-select" required>
                            <option value="">Choose a product for prediction...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-name="{{ $product->name }}"
                                        data-category="{{ $product->category }}"
                                        data-price="{{ $product->price }}"
                                        data-stock="{{ $product->quantity }}">
                                    {{ $product->name }} - {{ $product->category }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Choose the product you want to generate predictions for</div>
                    </div>

                    <div class="row">
                        <!-- Current Inventory -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Current Stock Level</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="current_stock" id="current-stock" 
                                           min="0" step="1" required>
                                    <span class="input-group-text">units</span>
                                </div>
                                <div class="form-text">Enter the current inventory quantity</div>
                            </div>
                        </div>

                        <!-- Expected Demand -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Expected Weekly Demand</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="expected_demand" id="expected-demand" 
                                           min="0" step="0.1" required>
                                    <span class="input-group-text">units</span>
                                </div>
                                <div class="form-text">Estimated demand for the next week</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Price -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Unit Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="price" id="price" 
                                           min="0" step="0.01" required>
                                </div>
                                <div class="form-text">Current selling price per unit</div>
                            </div>
                        </div>

                        <!-- Prediction Date -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Prediction Date</label>
                                <input type="date" class="form-control" name="prediction_date" id="prediction-date" 
                                       value="{{ date('Y-m-d') }}" required>
                                <div class="form-text">Date for which to generate prediction</div>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Options (Collapsible) -->
                    <div class="form-group">
                        <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="collapse" 
                                data-bs-target="#advanced-options" style="color: #0dcaf0; border-color: #0dcaf0;">
                            <i class="fas fa-sliders-h me-2"></i>Advanced Options
                        </button>
                    </div>

                    <div class="collapse" id="advanced-options">
                        <div class="card border-info mt-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Seasonality Factor</label>
                                            <select class="form-select" name="seasonality">
                                                <option value="Spring">Spring</option>
                                                <option value="Summer">Summer</option>
                                                <option value="Autumn" selected>Autumn</option>
                                                <option value="Winter">Winter</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Weather Condition</label>
                                            <select class="form-select" name="weather_condition">
                                                <option value="Sunny" selected>Sunny</option>
                                                <option value="Cloudy">Cloudy</option>
                                                <option value="Rainy">Rainy</option>
                                                <option value="Snowy">Snowy</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Holiday/Promotion</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="holiday_promotion" 
                                                       id="holiday-promotion">
                                                <label class="form-check-label" for="holiday-promotion">
                                                    Active promotion or holiday period
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Region</label>
                                            <select class="form-select" name="region">
                                                <option value="North" selected>North</option>
                                                <option value="South">South</option>
                                                <option value="East">East</option>
                                                <option value="West">West</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #0d6efd; border-color: #0d6efd; color: white;">
                            <i class="fas fa-magic me-2"></i>Generate AI Prediction
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-lg ms-2" onclick="resetForm()" style="color: #6c757d; border-color: #6c757d;">
                            <i class="fas fa-undo me-2"></i>Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar with Tips and Info -->
    <div class="col-md-4">
        <!-- Prediction Tips -->
        <div class="card content-spacing">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-lightbulb me-2"></i>Prediction Tips</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>For Best Results:</h6>
                    <ul class="mb-0 small">
                        <li>Use accurate current stock levels</li>
                        <li>Base demand estimates on recent sales data</li>
                        <li>Consider seasonal variations</li>
                        <li>Account for promotions and holidays</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Important:</h6>
                    <p class="small mb-0">AI predictions are estimates based on historical patterns. Always combine with business judgment for final decisions.</p>
                </div>
            </div>
        </div>

        <!-- Model Information -->
        <div class="card content-spacing">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-brain me-2"></i>AI Model Info</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h5 class="text-primary">85.5%</h5>
                            <small class="text-muted">Accuracy</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success">LSTM</h5>
                        <small class="text-muted">Neural Network</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="small text-muted">
                    <p><strong>Features Used:</strong></p>
                    <ul class="list-unstyled">
                        <li>• Historical sales data</li>
                        <li>• Inventory levels</li>
                        <li>• Seasonal patterns</li>
                        <li>• Price factors</li>
                        <li>• External conditions</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="fas fa-history me-2"></i>Recent Predictions</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div class="badge bg-success me-2">HIGH</div>
                    <small>Product A - 125.5 units</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div class="badge bg-warning me-2">MED</div>
                    <small>Product B - 78.2 units</small>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div class="badge bg-success me-2">HIGH</div>
                    <small>Product C - 203.8 units</small>
                </div>
                <a href="{{ route('ai.predictions.index') }}" class="btn btn-outline-primary btn-sm w-100 mt-2" style="color: #0d6efd; border-color: #0d6efd;">
                    View All Predictions
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-fill form when product is selected
    $('#product-select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        
        if (selectedOption.val()) {
            const stock = selectedOption.data('stock') || 0;
            const price = selectedOption.data('price') || 0;
            const category = selectedOption.data('category');
            
            $('#current-stock').val(stock);
            $('#price').val(price);
            
            // Estimate demand based on category (simple heuristic)
            let estimatedDemand = Math.round(stock * 0.3); // 30% of stock as weekly demand
            if (category === 'Groceries') estimatedDemand = Math.round(stock * 0.5);
            if (category === 'Electronics') estimatedDemand = Math.round(stock * 0.1);
            
            $('#expected-demand').val(estimatedDemand);
        }
    });
    
    // Form submission with loading state
    $('#prediction-form').on('submit', function(e) {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.html('<span class="spinner-border spinner-border-sm"></span> Generating Prediction...')
                 .prop('disabled', true);
        
        // Let the form submit normally, but show loading state
        setTimeout(function() {
            if (submitBtn.length) {
                submitBtn.html(originalText).prop('disabled', false);
            }
        }, 5000);
    });
    
    // Form validation
    $('#prediction-form input[required]').on('blur', function() {
        validateField($(this));
    });
});

function validateField(field) {
    const value = parseFloat(field.val());
    const fieldName = field.attr('name');
    
    // Remove existing validation classes
    field.removeClass('is-valid is-invalid');
    
    if (field.val() === '') {
        field.addClass('is-invalid');
        return false;
    }
    
    // Specific validations
    if (fieldName === 'current_stock' && value < 0) {
        field.addClass('is-invalid');
        return false;
    }
    
    if (fieldName === 'expected_demand' && value <= 0) {
        field.addClass('is-invalid');
        return false;
    }
    
    if (fieldName === 'price' && value <= 0) {
        field.addClass('is-invalid');
        return false;
    }
    
    field.addClass('is-valid');
    return true;
}

function resetForm() {
    $('#prediction-form')[0].reset();
    $('#prediction-form .is-valid, #prediction-form .is-invalid').removeClass('is-valid is-invalid');
    $('#product-select').val('').trigger('change');
}

// Auto-set today's date
$('#prediction-date').val(new Date().toISOString().split('T')[0]);
</script>
@endsection
