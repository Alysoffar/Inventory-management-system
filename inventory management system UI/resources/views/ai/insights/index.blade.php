@extends('layouts.app')

@section('title', 'AI Insights Dashboard')

@section('content')
<!-- Page Header -->
<div class="page-header section-spacing">
    <h1 class="page-title">
        <i class="fas fa-brain me-1"></i>AI Insights
    </h1>
    <p class="page-subtitle">Business Intelligence & Recommendations</p>
</div>

<!-- Key Metrics Cards -->
<div class="row section-spacing">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card h-100" style="min-height: 180px;">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="fas fa-chart-line mb-3" style="font-size: 2.5rem; color: #146eb4;"></i>
                <h3 style="font-size: 2rem; color: #212529; font-weight: bold;">{{ isset($insights['accuracy']) ? number_format($insights['accuracy'], 1) : '79.0' }}%</h3>
                <p style="font-size: 1.1rem; color: #212529; margin: 0;">Model Accuracy</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card h-100" style="min-height: 180px;">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="fas fa-exclamation-triangle mb-3" style="font-size: 2.5rem; color: #ff9900;"></i>
                <h3 style="font-size: 2rem; color: #212529; font-weight: bold;">{{ isset($insights['low_stock_count']) ? $insights['low_stock_count'] : '8' }}</h3>
                <p style="font-size: 1.1rem; color: #212529; margin: 0;">Low Stock Items</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card h-100" style="min-height: 180px;">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="fas fa-robot mb-3" style="font-size: 2.5rem; color: #232f3e;"></i>
                <h3 style="font-size: 2rem; color: #212529; font-weight: bold;">{{ isset($insights['predictions_made']) ? $insights['predictions_made'] : '24' }}</h3>
                <p style="font-size: 1.1rem; color: #212529; margin: 0;">Predictions Made</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stats-card h-100" style="min-height: 180px;">
            <div class="card-body text-center d-flex flex-column justify-content-center">
                <i class="fas fa-dollar-sign mb-3" style="font-size: 2.5rem; color: #037f0c;"></i>
                <h3 style="font-size: 2rem; color: #212529; font-weight: bold;">${{ isset($insights['cost_savings']) ? number_format($insights['cost_savings'], 0) : '15420' }}</h3>
                <p style="font-size: 1.1rem; color: #212529; margin: 0;">Cost Savings</p>
            </div>
        </div>
    </div>
</div>

<!-- AI Recommendations -->
<div class="row section-spacing">
    <div class="col-lg-8">
        <div class="card" style="min-height: 300px;">
            <div class="card-header" style="background: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
                <h5 class="card-title mb-0" style="color: #212529; font-size: 1.3rem; font-weight: 600;">
                    <i class="fas fa-lightbulb me-2" style="color: #ff9900;"></i>AI Recommendations
                </h5>
            </div>
            <div class="card-body" style="padding: 2rem;">
                @if(isset($insights['recommendations']) && count($insights['recommendations']) > 0)
                    @foreach($insights['recommendations'] as $recommendation)
                    <div class="alert alert-info mb-3" style="padding: 1rem; font-size: 1rem; border-left: 4px solid #146eb4;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3" style="font-size: 1.2rem; color: #146eb4;"></i>
                            <div style="color: #212529;">
                                <strong style="font-size: 1.1rem;">{{ $recommendation['type'] ?? 'Recommendation' }}</strong><br>
                                <span style="font-size: 1rem;">{{ $recommendation['message'] ?? $recommendation }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="alert alert-info mb-3" style="padding: 1rem; font-size: 1rem; border-left: 4px solid #146eb4;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-3" style="font-size: 1.2rem; color: #146eb4;"></i>
                            <div style="color: #212529;">
                                <strong style="font-size: 1.1rem;">Stock Optimization</strong><br>
                                <span style="font-size: 1rem;">Consider increasing inventory for high-demand products to prevent stockouts.</span>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-warning mb-3" style="padding: 1rem; font-size: 1rem; border-left: 4px solid #ff9900;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.2rem; color: #ff9900;"></i>
                            <div style="color: #212529;">
                                <strong style="font-size: 1.1rem;">Reorder Alert</strong><br>
                                <span style="font-size: 1rem;">8 products are below minimum stock levels and require immediate attention.</span>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-success mb-3" style="padding: 1rem; font-size: 1rem; border-left: 4px solid #037f0c;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3" style="font-size: 1.2rem; color: #037f0c;"></i>
                            <div style="color: #212529;">
                                <strong style="font-size: 1.1rem;">Cost Efficiency</strong><br>
                                <span style="font-size: 1rem;">AI predictions have reduced holding costs by 15% this quarter.</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card" style="min-height: 300px;">
            <div class="card-header" style="background: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
                <h5 class="card-title mb-0" style="color: #212529; font-size: 1.3rem; font-weight: 600;">
                    <i class="fas fa-cogs me-2" style="color: #232f3e;"></i>Model Performance
                </h5>
            </div>
            <div class="card-body" style="padding: 2rem;">
                <div class="mb-4">
                    <div class="d-flex justify-content-between" style="font-size: 1rem; color: #212529;">
                        <span style="font-weight: 600;">Accuracy</span>
                        <span style="font-weight: bold; color: #037f0c;">{{ isset($insights['accuracy']) ? number_format($insights['accuracy'], 1) : '79.0' }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ isset($insights['accuracy']) ? $insights['accuracy'] : '79' }}%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between" style="font-size: 1rem; color: #212529;">
                        <span style="font-weight: 600;">MAPE Score</span>
                        <span style="font-weight: bold; color: #ff9900;">{{ isset($insights['mape']) ? number_format($insights['mape'], 1) : '21.0' }}%</span>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: {{ isset($insights['mape']) ? (100 - $insights['mape']) : '79' }}%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between" style="font-size: 1rem; color: #212529;">
                        <span style="font-weight: 600;">R² Score</span>
                        <span style="font-weight: bold; color: #146eb4;">{{ isset($insights['r2_score']) ? number_format($insights['r2_score'], 3) : '0.994' }}</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-info" style="width: {{ isset($insights['r2_score']) ? ($insights['r2_score'] * 100) : '99.4' }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Risk Analysis -->
<div class="row section-spacing">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-shield-alt me-1"></i>Risk Analysis</h6>
            </div>
            <div class="card-body">
                @if(isset($insights['risk_analysis']) && count($insights['risk_analysis']) > 0)
                    @foreach($insights['risk_analysis'] as $risk)
                    <div class="d-flex justify-content-between align-items-center mb-2" style="padding: 0.3rem; border-left: 3px solid {{ $risk['level'] === 'high' ? '#dc3545' : ($risk['level'] === 'medium' ? '#ffc107' : '#28a745') }};">
                        <div style="font-size: 0.7rem;">
                            <strong>{{ $risk['product'] ?? 'Unknown Product' }}</strong><br>
                            <small class="text-muted">{{ $risk['description'] ?? 'Risk analysis available' }}</small>
                        </div>
                        <span class="badge {{ $risk['level'] === 'high' ? 'bg-danger' : ($risk['level'] === 'medium' ? 'bg-warning' : 'bg-success') }}" style="font-size: 0.6rem;">
                            {{ ucfirst($risk['level'] ?? 'low') }}
                        </span>
                    </div>
                    @endforeach
                @else
                    <div class="d-flex justify-content-between align-items-center mb-2" style="padding: 0.3rem; border-left: 3px solid #dc3545;">
                        <div style="font-size: 0.7rem;">
                            <strong>Widget A</strong><br>
                            <small class="text-muted">High demand with low stock levels</small>
                        </div>
                        <span class="badge bg-danger" style="font-size: 0.6rem;">High</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="padding: 0.3rem; border-left: 3px solid #ffc107;">
                        <div style="font-size: 0.7rem;">
                            <strong>Widget B</strong><br>
                            <small class="text-muted">Moderate stockout risk</small>
                        </div>
                        <span class="badge bg-warning" style="font-size: 0.6rem;">Medium</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="padding: 0.3rem; border-left: 3px solid #28a745;">
                        <div style="font-size: 0.7rem;">
                            <strong>Widget C</strong><br>
                            <small class="text-muted">Optimal stock levels maintained</small>
                        </div>
                        <span class="badge bg-success" style="font-size: 0.6rem;">Low</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="fas fa-chart-pie me-1"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-1">
                    <a href="{{ route('ai.predictions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-magic me-1"></i>New Prediction
                    </a>
                    <a href="{{ route('ai.predictions.index') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-list me-1"></i>View All Predictions
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-boxes me-1"></i>Manage Inventory
                    </a>
                    <a href="{{ route('reports.inventory') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-file-alt me-1"></i>Generate Report
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Predictions -->
<div class="row section-spacing">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background: #f8f9fc; border-bottom: 1px solid #e3e6f0;">
                <h5 class="card-title mb-0" style="color: #212529; font-size: 1.3rem; font-weight: 600;">
                    <i class="fas fa-history me-2" style="color: #146eb4;"></i>Recent Predictions
                </h5>
            </div>
            <div class="card-body" style="padding: 2rem;">
                @if(isset($insights['recent_predictions']) && count($insights['recent_predictions']) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-2">
                            <thead style="background: #f8f9fc;">
                                <tr>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Product</th>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Current Stock</th>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Predicted Demand</th>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Recommendation</th>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($insights['recent_predictions'] as $prediction)
                                <tr>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;"><strong>{{ $prediction['product'] ?? 'Unknown' }}</strong></td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">{{ $prediction['current_stock'] ?? 'N/A' }}</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">{{ $prediction['predicted_demand'] ?? 'N/A' }}</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">{{ $prediction['recommendation'] ?? 'N/A' }}</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">{{ $prediction['date'] ?? now()->format('M d') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover mb-2">
                            <thead style="background: #f8f9fc;">
                                <tr>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Product</th>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Current Stock</th>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Predicted Demand</th>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Recommendation</th>
                                    <th style="font-size: 1rem; color: #212529; font-weight: 600; padding: 1rem 0.75rem;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;"><strong>Widget A</strong></td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">150</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">120</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">Maintain Stock</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">{{ now()->format('M d') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;"><strong>Widget B</strong></td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">45</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">60</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">Reorder Soon</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">{{ now()->subHours(2)->format('M d') }}</td>
                                </tr>
                                <tr>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;"><strong>Widget C</strong></td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">25</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">35</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">Urgent Reorder</td>
                                    <td style="font-size: 1rem; color: #212529; padding: 1rem 0.75rem;">{{ now()->subHours(4)->format('M d') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <div class="text-center mt-4">
                    <a href="{{ route('ai.predictions.index') }}" class="btn btn-primary" style="font-size: 1rem; padding: 0.75rem 2rem;">View All Predictions</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Auto-refresh insights every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endsection
