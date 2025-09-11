<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Account Registration - Approval Required</title>
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
            background: #232f3e;
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
        .user-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin: 20px 0;
        }
        .btn:hover {
            background: #218838;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Account Registration</h1>
            <p>Approval Required for Inventory Management System</p>
        </div>
        
        <div class="content">
            <h2>Hello Admin,</h2>
            
            <p>A new user has registered for the Inventory Management System and requires your approval to access the platform.</p>
            
            <div class="user-details">
                <h3>User Details:</h3>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Company:</strong> {{ $user->company }}</p>
                <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
                <p><strong>Registration Date:</strong> {{ $user->created_at->format('F j, Y \a\t g:i A') }}</p>
            </div>
            
            <p>Please review this registration request and decide whether to approve or reject the account.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('admin.approve-user-get', $user->id) }}" class="btn">Approve Account</a>
            </div>
            
            <p><strong>Note:</strong> You can also manage user accounts through the admin panel in the Inventory Management System.</p>
            
            <div class="footer">
                <p>This email was sent automatically by the Inventory Management System.</p>
                <p>If you did not expect this email, please contact your system administrator.</p>
            </div>
        </div>
    </div>
</body>
</html>
