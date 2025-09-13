<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Analytics Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #2563eb;
            font-size: 28px;
            margin: 0;
            font-weight: bold;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .section-title {
            background: #f8fafc;
            padding: 12px 15px;
            border-left: 4px solid #2563eb;
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
        }
        
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .metrics-row {
            display: table-row;
        }
        
        .metric-item {
            display: table-cell;
            width: 50%;
            padding: 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        
        .metric-label {
            font-weight: bold;
            color: #374151;
            display: block;
            margin-bottom: 5px;
        }
        
        .metric-value {
            font-size: 18px;
            color: #059669;
            font-weight: bold;
        }
        
        .metric-value.negative {
            color: #dc2626;
        }
        
        .performance-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        
        .badge-excellent {
            background: #dcfce7;
            color: #166534;
        }
        
        .badge-good {
            background: #fef3c7;
            color: #92400e;
        }
        
        .list-item {
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .list-item:last-child {
            border-bottom: none;
        }
        
        .product-performance {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .product-row {
            display: table-row;
        }
        
        .product-cell {
            display: table-cell;
            padding: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .product-cell.header {
            background: #f9fafb;
            font-weight: bold;
            color: #374151;
        }
        
        .growth-positive {
            color: #059669;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        
        .ai-confidence {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        
        .ai-confidence .title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .ai-confidence .score {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🤖 AI Analytics Report</h1>
        <div class="subtitle">
            Generated on {{ $generated_at }}<br>
            Report Period: {{ $report_period }}
        </div>
    </div>

    <!-- Executive Summary -->
    <div class="section">
        <div class="section-title">📊 Executive Summary</div>
        <div class="ai-confidence">
            <div class="title">AI Model Confidence Level</div>
            <div class="score">{{ number_format($ai_predictions['confidence_level'], 1) }}%</div>
        </div>
        
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Predicted Revenue</span>
                    <span class="metric-value">${{ number_format($ai_predictions['predicted_revenue'], 2) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Predicted Net Profit</span>
                    <span class="metric-value">${{ number_format($ai_predictions['predicted_net_profit'], 2) }}</span>
                </div>
            </div>
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Predicted Margin</span>
                    <span class="metric-value">{{ number_format($ai_predictions['predicted_margin'], 1) }}%</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Prediction Accuracy</span>
                    <span class="metric-value">{{ number_format($ai_predictions['accuracy_score'], 1) }}%</span>
                    <span class="performance-badge badge-excellent">Excellent</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Performance -->
    <div class="section">
        <div class="section-title">📈 Current Performance Metrics</div>
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Total Sales</span>
                    <span class="metric-value">${{ number_format($current_metrics['total_sales'], 2) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Total Profit</span>
                    <span class="metric-value">${{ number_format($current_metrics['total_profit'], 2) }}</span>
                </div>
            </div>
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Margin Percentage</span>
                    <span class="metric-value">{{ number_format($current_metrics['margin_percentage'], 1) }}%</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Inventory Turnover</span>
                    <span class="metric-value">{{ number_format($current_metrics['inventory_turnover'], 1) }}x</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Products -->
    <div class="section">
        <div class="section-title">🏆 Top Performing Products</div>
        <div class="product-performance">
            <div class="product-row">
                <div class="product-cell header">Product Name</div>
                <div class="product-cell header">Sales Volume</div>
                <div class="product-cell header">Growth Rate</div>
            </div>
            @foreach($insights['top_performing_products'] as $product)
            <div class="product-row">
                <div class="product-cell">{{ $product['name'] }}</div>
                <div class="product-cell">${{ number_format($product['sales']) }}</div>
                <div class="product-cell growth-positive">{{ $product['growth'] }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- AI Insights & Recommendations -->
    <div class="section">
        <div class="section-title">🧠 AI Insights & Recommendations</div>
        
        <h4 style="color: #dc2626; margin-bottom: 10px;">⚠️ Risk Factors</h4>
        @foreach($insights['risk_factors'] as $risk)
        <div class="list-item">• {{ $risk }}</div>
        @endforeach
        
        <h4 style="color: #059669; margin-top: 25px; margin-bottom: 10px;">💡 Strategic Recommendations</h4>
        @foreach($insights['recommendations'] as $recommendation)
        <div class="list-item">• {{ $recommendation }}</div>
        @endforeach
    </div>

    <!-- Future Forecasts -->
    <div class="section">
        <div class="section-title">🔮 AI-Powered Forecasts</div>
        
        <h4 style="margin-bottom: 15px;">Next Quarter Projections</h4>
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Expected Sales</span>
                    <span class="metric-value">${{ number_format($forecasts['next_quarter']['expected_sales'], 2) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Projected Profit</span>
                    <span class="metric-value">${{ number_format($forecasts['next_quarter']['projected_profit'], 2) }}</span>
                </div>
            </div>
        </div>
        
        <h4 style="margin-top: 25px; margin-bottom: 15px;">Annual Projections</h4>
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Annual Revenue</span>
                    <span class="metric-value">${{ number_format($forecasts['yearly_projection']['annual_revenue'], 2) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Annual Profit</span>
                    <span class="metric-value">${{ number_format($forecasts['yearly_projection']['annual_profit'], 2) }}</span>
                </div>
            </div>
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Growth Rate</span>
                    <span class="metric-value growth-positive">{{ $forecasts['yearly_projection']['growth_rate'] }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Inventory Investment Needed</span>
                    <span class="metric-value">${{ number_format($forecasts['next_quarter']['inventory_needs'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Model Performance -->
    <div class="section">
        <div class="section-title">⚙️ AI Model Performance</div>
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Prediction Accuracy</span>
                    <span class="metric-value">{{ number_format($model_performance['prediction_accuracy'], 1) }}%</span>
                    <span class="performance-badge badge-excellent">Excellent</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Model Confidence</span>
                    <span class="metric-value">{{ number_format($model_performance['model_confidence'], 1) }}%</span>
                </div>
            </div>
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Last Model Training</span>
                    <span class="metric-value" style="font-size: 14px;">{{ $model_performance['last_trained'] }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Training Data Points</span>
                    <span class="metric-value">{{ number_format($model_performance['training_data_points']) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <strong>Smart Inventory Management System</strong><br>
        AI-Powered Analytics & Predictions<br>
        This report was automatically generated using advanced machine learning algorithms.
    </div>
</body>
</html>