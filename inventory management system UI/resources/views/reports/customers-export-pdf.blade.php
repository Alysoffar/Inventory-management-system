<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Report</title>
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
            border-bottom: 3px solid #7c3aed;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #7c3aed;
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
            background: #f3f4f6;
            padding: 12px 15px;
            border-left: 4px solid #7c3aed;
            font-size: 18px;
            font-weight: bold;
            color: #6b21a8;
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
            color: #7c3aed;
            font-weight: bold;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th {
            background: #f3f4f6;
            padding: 12px 8px;
            border: 1px solid #e5e7eb;
            font-weight: bold;
            color: #6b21a8;
            text-align: left;
        }
        
        .data-table td {
            padding: 10px 8px;
            border: 1px solid #e5e7eb;
            font-size: 14px;
        }
        
        .data-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .amount {
            color: #7c3aed;
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
        
        .summary-box {
            background: linear-gradient(135deg, #7c3aed, #6b21a8);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
        }
        
        .summary-box .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .summary-box .value {
            font-size: 32px;
            font-weight: bold;
        }
        
        .segment-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .segment-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        .segment-item.vip {
            background: #fef3c7;
            color: #92400e;
        }
        
        .segment-item.premium {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .segment-item.regular {
            background: #dcfce7;
            color: #166534;
        }
        
        .segment-item.new {
            background: #f3f4f6;
            color: #374151;
        }
        
        .percentage-bar {
            width: 100%;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .percentage-fill {
            height: 100%;
            background: #7c3aed;
            transition: width 0.3s;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>👥 Customers Report</h1>
        <div class="subtitle">
            Generated on {{ $generated_at }}<br>
            Report Period: {{ $report_period }}
        </div>
    </div>

    <!-- Customer Summary -->
    <div class="section">
        <div class="section-title">📊 Customer Summary</div>
        <div class="summary-box">
            <div class="title">Total Customers</div>
            <div class="value">{{ number_format($customer_summary['total_customers']) }}</div>
        </div>
        
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">New Customers (This Month)</span>
                    <span class="metric-value">{{ number_format($customer_summary['new_customers']) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Active Customers</span>
                    <span class="metric-value">{{ number_format($customer_summary['active_customers']) }}</span>
                </div>
            </div>
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Average Order Value</span>
                    <span class="metric-value">${{ number_format($customer_summary['average_order_value'], 2) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Customer Retention Rate</span>
                    <span class="metric-value">{{ number_format($customer_summary['customer_retention_rate'], 1) }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="section">
        <div class="section-title">🏆 Top Customers</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Total Orders</th>
                    <th>Total Spent</th>
                    <th>Last Order</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_customers as $customer)
                <tr>
                    <td>{{ $customer['name'] }}</td>
                    <td>{{ number_format($customer['orders']) }}</td>
                    <td class="amount">${{ number_format($customer['total_spent'], 2) }}</td>
                    <td>{{ $customer['last_order'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Customer Segmentation -->
    <div class="section">
        <div class="section-title">🎯 Customer Segmentation</div>
        <div class="segment-grid">
            @foreach($customer_segments as $segment)
            <div class="segment-item {{ strtolower(explode(' ', $segment['segment'])[0]) }}">
                <div style="font-size: 20px; font-weight: bold;">{{ $segment['count'] }}</div>
                <div style="font-size: 12px; margin: 5px 0;">{{ $segment['segment'] }}</div>
                <div style="font-size: 14px; font-weight: bold;">${{ number_format($segment['total_value'], 0) }}</div>
                <div class="percentage-bar">
                    <div class="percentage-fill" style="width: {{ $segment['percentage'] * 2.5 }}%;"></div>
                </div>
                <div style="font-size: 12px; margin-top: 3px;">{{ number_format($segment['percentage'], 1) }}%</div>
            </div>
            @endforeach
        </div>
        
        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer Segment</th>
                    <th>Customer Count</th>
                    <th>Percentage</th>
                    <th>Total Value</th>
                    <th>Avg. per Customer</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customer_segments as $segment)
                <tr>
                    <td>{{ $segment['segment'] }}</td>
                    <td>{{ number_format($segment['count']) }}</td>
                    <td>{{ number_format($segment['percentage'], 1) }}%</td>
                    <td class="amount">${{ number_format($segment['total_value'], 2) }}</td>
                    <td class="amount">${{ number_format($segment['total_value'] / $segment['count'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Geographic Distribution -->
    <div class="section">
        <div class="section-title">🌍 Geographic Distribution</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Region</th>
                    <th>Customer Count</th>
                    <th>Percentage</th>
                    <th>Market Share</th>
                </tr>
            </thead>
            <tbody>
                @foreach($geographic_data as $region)
                <tr>
                    <td>{{ $region['region'] }}</td>
                    <td>{{ number_format($region['customers']) }}</td>
                    <td>{{ number_format($region['percentage'], 1) }}%</td>
                    <td>
                        <div class="percentage-bar">
                            <div class="percentage-fill" style="width: {{ $region['percentage'] * 2.5 }}%;"></div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Customer Insights -->
    <div class="section">
        <div class="section-title">💡 Customer Insights</div>
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; border-left: 4px solid #7c3aed;">
            <h4 style="color: #6b21a8; margin-top: 0;">Key Findings:</h4>
            <ul style="color: #374151; line-height: 1.8;">
                <li><strong>{{ number_format($customer_summary['customer_retention_rate'], 1) }}%</strong> customer retention rate indicates strong customer loyalty</li>
                <li><strong>{{ number_format(($customer_segments[0]['count'] / $customer_summary['total_customers']) * 100, 1) }}%</strong> of customers are VIP/Premium, generating significant revenue</li>
                <li>Average order value of <strong>${{ number_format($customer_summary['average_order_value'], 2) }}</strong> shows healthy purchasing patterns</li>
                <li><strong>{{ number_format($customer_summary['new_customers']) }}</strong> new customers acquired this month</li>
                <li>Geographic distribution shows <strong>{{ $geographic_data[0]['region'] }}</strong> as the strongest market</li>
            </ul>
            
            <h4 style="color: #6b21a8;">Recommendations:</h4>
            <ul style="color: #374151; line-height: 1.8;">
                <li>Focus on retention programs for VIP and Premium customers</li>
                <li>Develop targeted campaigns for the {{ $geographic_data[0]['region'] }}</li>
                <li>Create loyalty rewards to move Regular customers to Premium tier</li>
                <li>Implement referral programs to leverage satisfied customers</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <strong>Smart Inventory Management System</strong><br>
        Customer Analytics Report<br>
        This report was automatically generated from your customer data.
    </div>
</body>
</html>