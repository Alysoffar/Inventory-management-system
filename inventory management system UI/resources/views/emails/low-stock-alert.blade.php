<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Low Stock Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .product-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
        .critical {
            color: #dc3545;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="alert-icon">⚠️</div>
            <h1>LOW STOCK ALERT</h1>
            <p>Immediate attention required</p>
        </div>
        
        <p>Dear Admin,</p>
        
        <p>This is an automated notification to inform you that one of your products is running low on inventory and requires immediate attention.</p>
        
        <div class="product-info">
            <h3 style="margin-top: 0; color: #dc3545;">Product Details</h3>
            
            <div class="info-row">
                <span class="info-label">Product Name:</span>
                <span class="info-value">{{ $product->name }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">SKU:</span>
                <span class="info-value">{{ $product->sku }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Current Stock:</span>
                <span class="info-value critical">{{ $currentStock }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Minimum Level:</span>
                <span class="info-value">{{ $minimumLevel }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Location:</span>
                <span class="info-value">{{ $location }}</span>
            </div>
            
            @if($product->auto_reorder)
            <div class="info-row">
                <span class="info-label">Auto Reorder:</span>
                <span class="info-value" style="color: #28a745;">Enabled ({{ $product->reorder_quantity }} units)</span>
            </div>
            @else
            <div class="info-row">
                <span class="info-label">Auto Reorder:</span>
                <span class="info-value" style="color: #dc3545;">Disabled</span>
            </div>
            @endif
        </div>
        
        @if($product->auto_reorder)
        <p><strong>Action Taken:</strong> Automatic restock has been triggered for this product. You will receive another notification once the restock is completed.</p>
        @else
        <p><strong>Action Required:</strong> Please manually restock this product as auto-reorder is disabled.</p>
        @endif
        
        <p>This alert was generated on {{ now()->format('F j, Y \a\t g:i A') }}.</p>
        
        <div class="footer">
            <p>This is an automated message from your Inventory Tracking System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
