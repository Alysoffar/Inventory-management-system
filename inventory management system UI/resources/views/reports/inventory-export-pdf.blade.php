<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Report</title>
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
            border-bottom: 3px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #059669;
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
            background: #f0fdf4;
            padding: 12px 15px;
            border-left: 4px solid #059669;
            font-size: 18px;
            font-weight: bold;
            color: #047857;
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
            background: #f0fdf4;
            padding: 12px 8px;
            border: 1px solid #e5e7eb;
            font-weight: bold;
            color: #047857;
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
        
        .status-in-stock {
            color: #059669;
            font-weight: bold;
        }
        
        .status-low-stock {
            color: #f59e0b;
            font-weight: bold;
        }
        
        .status-out-stock {
            color: #dc2626;
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
            background: linear-gradient(135deg, #059669, #047857);
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
        
        .status-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .status-item {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        
        .status-item.in-stock {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-item.low-stock {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-item.out-stock {
            background: #fecaca;
            color: #991b1b;
        }
        
        .status-item.overstocked {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 Inventory Report</h1>
        <div class="subtitle">
            Generated on {{ $generated_at }}<br>
            Report Period: {{ $report_period }}
        </div>
    </div>

    <!-- Inventory Summary -->
    <div class="section">
        <div class="section-title">📊 Inventory Summary</div>
        <div class="summary-box">
            <div class="title">Total Stock Value</div>
            <div class="value">${{ number_format($inventory_summary['total_stock_value'], 2) }}</div>
        </div>
        
        <div class="metrics-grid">
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Total Products</span>
                    <span class="metric-value">{{ number_format($inventory_summary['total_products']) }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Low Stock Items</span>
                    <span class="metric-value status-low-stock">{{ $inventory_summary['low_stock_items'] }}</span>
                </div>
            </div>
            <div class="metrics-row">
                <div class="metric-item">
                    <span class="metric-label">Out of Stock</span>
                    <span class="metric-value status-out-stock">{{ $inventory_summary['out_of_stock_items'] }}</span>
                </div>
                <div class="metric-item">
                    <span class="metric-label">Overstocked</span>
                    <span class="metric-value">{{ $inventory_summary['overstocked_items'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Status Overview -->
    <div class="section">
        <div class="section-title">📋 Stock Status Overview</div>
        <div class="status-grid">
            <div class="status-item in-stock">
                <div style="font-size: 24px; font-weight: bold;">{{ $stock_status['in_stock'] }}</div>
                <div>In Stock</div>
            </div>
            <div class="status-item low-stock">
                <div style="font-size: 24px; font-weight: bold;">{{ $stock_status['low_stock'] }}</div>
                <div>Low Stock</div>
            </div>
            <div class="status-item out-stock">
                <div style="font-size: 24px; font-weight: bold;">{{ $stock_status['out_of_stock'] }}</div>
                <div>Out of Stock</div>
            </div>
            <div class="status-item overstocked">
                <div style="font-size: 24px; font-weight: bold;">{{ $stock_status['overstocked'] }}</div>
                <div>Overstocked</div>
            </div>
        </div>
    </div>

    <!-- High Value Items -->
    <div class="section">
        <div class="section-title">💎 High Value Items</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Stock Qty</th>
                    <th>Unit Cost</th>
                    <th>Total Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach($high_value_items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ number_format($item['stock']) }}</td>
                    <td class="amount">${{ number_format($item['unit_cost'], 2) }}</td>
                    <td class="amount">${{ number_format($item['total_value'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Low Stock Alert -->
    <div class="section">
        <div class="section-title">⚠️ Low Stock Alert</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Current Stock</th>
                    <th>Min Stock Level</th>
                    <th>Suggested Order Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach($low_stock_items as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="status-low-stock">{{ $item['current_stock'] }}</td>
                    <td>{{ $item['min_stock'] }}</td>
                    <td class="amount">{{ $item['suggested_order'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Categories Breakdown -->
    <div class="section">
        <div class="section-title">📂 Categories Breakdown</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Number of Items</th>
                    <th>Total Value</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category['name'] }}</td>
                    <td>{{ number_format($category['items']) }}</td>
                    <td class="amount">${{ number_format($category['value'], 2) }}</td>
                    <td>{{ number_format(($category['value'] / $inventory_summary['total_stock_value']) * 100, 1) }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <strong>Smart Inventory Management System</strong><br>
        Inventory Status Report<br>
        This report was automatically generated from your inventory data.
    </div>
</body>
</html>