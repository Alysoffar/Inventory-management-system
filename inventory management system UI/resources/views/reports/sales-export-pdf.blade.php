<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
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
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .data-table th {
            background: #f9fafb;
            padding: 12px 8px;
            border: 1px solid #e5e7eb;
            font-weight: bold;
            color: #374151;
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
        
        .summary-box {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
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
    </style>
</head>
<body>
    <div class="header">
        <h1>📊 Sales Report</h1>
        <div class="subtitle">
            Generated on {{ $generated_at }}<br>
            Report Period: {{ $report_period }}
        </div>
    </div>

    <!-- Sales Summary -->
    <div class="section">
        <div class="section-title">💰 Sales Summary</div>
        <div class="summary-box">
            <div class="title">Total Sales Revenue</div>
            <div class="value">${{ number_format($sales_summary['total_sales'], 2) }}</div>
        </div>
        
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Total Transactions</span>
                    <span class="metric-value">{{ number_format($sales_summary['total_transactions']) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Average Transaction Value</span>
                    <span class="metric-value">${{ number_format($sales_summary['average_transaction'], 2) }}</span>
                </div>
            </div>
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Total Items Sold</span>
                    <span class="metric-value">{{ number_format($sales_summary['total_items_sold']) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Tax Collected</span>
                    <span class="metric-value">${{ number_format($sales_summary['tax_collected'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="section">
        <div class="section-title">🏆 Top Selling Products</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity Sold</th>
                    <th>Revenue</th>
                    <th>Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($top_products as $product)
                <tr>
                    <td>{{ $product['name'] }}</td>
                    <td>{{ number_format($product['quantity']) }}</td>
                    <td class="amount">${{ number_format($product['revenue'], 2) }}</td>
                    <td class="amount">${{ number_format($product['profit'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Monthly Sales Breakdown -->
    <div class="section">
        <div class="section-title">📅 Monthly Sales Breakdown</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Sales Amount</th>
                    <th>Transactions</th>
                    <th>Avg. per Transaction</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthly_breakdown as $month)
                <tr>
                    <td>{{ $month['month'] }}</td>
                    <td class="amount">${{ number_format($month['sales'], 2) }}</td>
                    <td>{{ number_format($month['transactions']) }}</td>
                    <td class="amount">${{ number_format($month['sales'] / $month['transactions'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Payment Methods -->
    <div class="section">
        <div class="section-title">💳 Payment Methods Breakdown</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Percentage</th>
                    <th>Transactions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payment_methods as $method)
                <tr>
                    <td>{{ $method['method'] }}</td>
                    <td class="amount">${{ number_format($method['amount'], 2) }}</td>
                    <td>{{ number_format($method['percentage'], 1) }}%</td>
                    <td>{{ number_format($method['amount'] / 195.51) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <strong>Smart Inventory Management System</strong><br>
        Sales Analytics Report<br>
        This report was automatically generated from your sales data.
    </div>
</body>
</html>