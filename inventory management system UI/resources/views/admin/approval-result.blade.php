<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Approval - Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .approval-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            max-width: 600px;
            width: 90%;
            text-align: center;
        }
        
        .approval-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        
        .success { color: #28a745; }
        .info { color: #17a2b8; }
        .error { color: #dc3545; }
        
        .approval-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .approval-message {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .user-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        
        .btn-custom {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            background: #5a6fd8;
            color: white;
            transform: translateY(-2px);
        }
        
        .footer-info {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="approval-card">
        @if($type === 'success')
            <i class="fas fa-check-circle approval-icon success"></i>
            <h1 class="approval-title text-success">Approval Successful!</h1>
        @elseif($type === 'info')
            <i class="fas fa-info-circle approval-icon info"></i>
            <h1 class="approval-title text-info">Already Approved</h1>
        @else
            <i class="fas fa-exclamation-triangle approval-icon error"></i>
            <h1 class="approval-title text-danger">Approval Failed</h1>
        @endif
        
        <div class="approval-message">
            {{ $message }}
        </div>
        
        @if($user)
            <div class="user-info">
                <h5>User Information</h5>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Company:</strong> {{ $user->company }}</p>
                @if($user->phone)
                    <p><strong>Phone:</strong> {{ $user->phone }}</p>
                @endif
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $user->status === 'approved' ? 'success' : 'warning' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </p>
            </div>
        @endif
        
        <div class="mt-4">
            <a href="{{ route('login') }}" class="btn-custom">
                <i class="fas fa-sign-in-alt me-2"></i>
                Go to Login Page
            </a>
        </div>
        
        <div class="footer-info">
            <p>This action was performed for the Inventory Management System.</p>
            <p>If you have any questions, please contact the system administrator.</p>
        </div>
    </div>
</body>
</html>
