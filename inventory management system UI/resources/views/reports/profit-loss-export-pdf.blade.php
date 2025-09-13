<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profit & Loss Report</title>
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
        
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 22px;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .report-info {
            font-size: 12px;
            color: #6b7280;
        }
        
        .summary-section {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .summary-title {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 5px;
        }
        
        .metrics-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .metrics-row {
            display: table-row;
        }
        
        .metric-box {
            display: table-cell;
            width: 24%;
            text-align: center;
            padding: 15px;
            margin: 1%;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            vertical-align: top;
        }
        
        .metric-value {
            font-size: 20px;
            font-weight: bold;
            color: #059669;
            margin-bottom: 5px;
        }
        
        .metric-value.negative {
            color: #dc2626;
        }
        
        .metric-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 5px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        .data-table th {
            background: #2563eb;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .amount.positive {
            color: #059669;
        }
        
        .amount.negative {
            color: #dc2626;
        }
        
        .percentage {
            text-align: center;
            font-weight: bold;
            color: #2563eb;
        }
        
        .ai-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
        }
        
        .ai-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .ai-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            background: white;
            border-radius: 50%;
            display: inline-block;
        }
        
        .prediction-grid {
            display: table;
            width: 100%;
        }
        
        .prediction-row {
            display: table-row;
        }
        
        .prediction-item {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 10px;
        }
        
        .prediction-value {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .prediction-label {
            font-size: 11px;
            opacity: 0.9;
        }
        
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #d1d5db;
            font-size: 10px;
            color: #6b7280;
        }
        
        .cost-breakdown {
            display: table;
            width: 100%;
        }
        
        .cost-row {
            display: table-row;
        }
        
        .cost-item {
            display: table-cell;
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .cost-label {
            font-weight: bold;
            color: #374151;
        }
        
        .cost-amount {
            text-align: right;
            font-weight: bold;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">Inventory Management System</div>
        <div class="report-title">Profit & Loss Report</div>
        <div class="report-info">
            Generated on: {{ $generated_at }}<br>
            Report Period: {{ $report_period }}
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="summary-section">
        <div class="summary-title">Financial Performance Summary</div>
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-box">
                    <div class="metric-value">${{ number_format($current_period['revenue'], 2) }}</div>
                    <div class="metric-label">Total Revenue</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">${{ number_format($current_period['gross_profit'], 2) }}</div>
                    <div class="metric-label">Gross Profit</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">${{ number_format($current_period['net_profit'], 2) }}</div>
                    <div class="metric-label">Net Profit</div>
                </div>
                <div class="metric-box">
                    <div class="metric-value">{{ number_format($current_period['margin_percentage'], 1) }}%</div>
                    <div class="metric-label">Profit Margin</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Breakdown -->
    <div class="table-section">
        <div class="section-title">Revenue Breakdown by Category</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Revenue</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenue_breakdown as $item)
                <tr>
                    <td class="cost-label">{{ $item['category'] }}</td>
                    <td class="amount positive">${{ number_format($item['amount'], 2) }}</td>
                    <td class="percentage">{{ number_format($item['percentage'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Cost Analysis -->
    <div class="table-section">
        <div class="section-title">Cost Analysis</div>
        <div class="cost-breakdown">
            <div class="cost-row">
                <div class="cost-item cost-label">Cost of Goods Sold</div>
                <div class="cost-item cost-amount">${{ number_format($cost_analysis['cost_of_goods_sold'], 2) }}</div>
            </div>
            <div class="cost-row">
                <div class="cost-item cost-label">Operating Expenses</div>
                <div class="cost-item cost-amount">${{ number_format($cost_analysis['operating_expenses'], 2) }}</div>
            </div>
            <div class="cost-row">
                <div class="cost-item cost-label">Administrative Costs</div>
                <div class="cost-item cost-amount">${{ number_format($cost_analysis['administrative_costs'], 2) }}</div>
            </div>
            <div class="cost-row">
                <div class="cost-item cost-label">Marketing Expenses</div>
                <div class="cost-item cost-amount">${{ number_format($cost_analysis['marketing_expenses'], 2) }}</div>
            </div>
            <div class="cost-row">
                <div class="cost-item cost-label">Other Expenses</div>
                <div class="cost-item cost-amount">${{ number_format($cost_analysis['other_expenses'], 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Monthly Comparison -->
    <div class="table-section">
        <div class="section-title">Monthly Performance Comparison</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Revenue</th>
                    <th>Net Profit</th>
                    <th>Margin %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_comparison as $month)
                <tr>
                    <td class="cost-label">{{ $month['month'] }}</td>
                    <td class="amount positive">${{ number_format($month['revenue'], 2) }}</td>
                    <td class="amount positive">${{ number_format($month['profit'], 2) }}</td>
                    <td class="percentage">{{ number_format($month['margin'], 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- AI Predictions -->
    <div class="ai-section">
        <div class="ai-title">
            <span class="ai-icon"></span>
            AI Financial Predictions
        </div>
        <div class="prediction-grid">
            <div class="prediction-row">
                <div class="prediction-item">
                    <div class="prediction-value">${{ number_format($ai_predictions['predicted_revenue'], 2) }}</div>
                    <div class="prediction-label">Predicted Revenue</div>
                </div>
                <div class="prediction-item">
                    <div class="prediction-value">${{ number_format($ai_predictions['predicted_profit'], 2) }}</div>
                    <div class="prediction-label">Predicted Profit</div>
                </div>
                <div class="prediction-item">
                    <div class="prediction-value">{{ number_format($ai_predictions['confidence_level'], 1) }}%</div>
                    <div class="prediction-label">Confidence Level</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Inventory Management System</strong> - Comprehensive Business Analytics</p>
        <p>This report contains confidential financial information. Generated automatically by AI-powered analytics.</p>
        <p>Report ID: {{ $report_period }} | Generated: {{ $generated_at }}</p>
    </div>
</body>
</html>