<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auto Restock Notification</title>
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
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .success-icon {
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
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .highlight {
            background-color: #d4edda;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #c3e6cb;
            margin: 20px 0;
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
            <div class="success-icon">✅</div>
            <h1>AUTO RESTOCK COMPLETED</h1>
            <p>Inventory has been automatically replenished</p>
        </div>
        
        <p>Dear Admin,</p>
        
        <p>This is a confirmation that automatic restocking has been successfully completed for one of your products.</p>
        
        <div class="highlight">
            <h4 style="margin-top: 0; color: #155724;">✓ Restock Operation Successful</h4>
            <p style="margin-bottom: 0;">Your inventory has been automatically updated to prevent stockouts.</p>
        </div>
        
        <div class="product-info">
            <h3 style="margin-top: 0; color: #28a745;">Product Details</h3>
            
            <div class="info-row">
                <span class="info-label">Product Name:</span>
                <span class="info-value">{{ $product->name }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">SKU:</span>
                <span class="info-value">{{ $product->sku }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Restocked Quantity:</span>
                <span class="info-value success">+{{ $restockedQuantity }} units</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">New Stock Level:</span>
                <span class="info-value success">{{ $newStockLevel }} units</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Minimum Level:</span>
                <span class="info-value">{{ $product->minimum_stock_level }} units</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Location:</span>
                <span class="info-value">{{ $location }}</span>
            </div>
            
            <div class="info-row">
                <span class="info-label">Restock Time:</span>
                <span class="info-value">{{ $product->last_restocked_at->format('F j, Y \a\t g:i A') }}</span>
            </div>
        </div>
        
        <p><strong>Next Steps:</strong></p>
        <ul>
            <li>The inventory levels have been updated in your system</li>
            <li>All related records and logs have been automatically created</li>
            <li>You can view the detailed inventory log in your dashboard</li>
        </ul>
        
        <p>This restock operation was completed on {{ now()->format('F j, Y \a\t g:i A') }}.</p>
        
        <div class="footer">
            <p>This is an automated message from your Inventory Tracking System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
