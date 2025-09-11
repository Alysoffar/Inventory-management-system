<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved - Welcome!</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 16px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .welcome-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin: 20px 0;
        }
        .btn:hover {
            background: #0056b3;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
        .features {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .features ul {
            list-style-type: none;
            padding: 0;
        }
        .features li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .features li:last-child {
            border-bottom: none;
        }
        .features li i {
            color: #28a745;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Account Approved!</h1>
            <p>Welcome to the Inventory Management System</p>
        </div>
        
        <div class="content">
            <div class="welcome-box">
                <h2>Hello {{ $user->name }},</h2>
                <p>Great news! Your account has been approved and you now have access to our Inventory Management System.</p>
            </div>
            
            <p>You can now log in and start using all the powerful features of our platform:</p>
            
            <div class="features">
                <h3>What you can do:</h3>
                <ul>
                    <li>✅ Manage your product inventory</li>
                    <li>✅ Track sales and purchases</li>
                    <li>✅ Generate comprehensive reports</li>
                    <li>✅ View AI-powered predictions and insights</li>
                    <li>✅ Monitor stock levels and get alerts</li>
                    <li>✅ Manage customers and suppliers</li>
                </ul>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $loginUrl }}" class="btn">Login to Your Account</a>
            </div>
            
            <p><strong>Your login credentials:</strong></p>
            <p>Email: {{ $user->email }}<br>
            Password: [The password you created during registration]</p>
            
            <p>If you have any questions or need assistance getting started, please don't hesitate to contact our support team.</p>
            
            <div class="footer">
                <p>Thank you for choosing our Inventory Management System!</p>
                <p>This email was sent automatically by the system.</p>
            </div>
        </div>
    </div>
</body>
</html>
