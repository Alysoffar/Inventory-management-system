<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Inventory Management System</title>
    <!-- AWS Cloudscape Design System Styles -->
    <link rel="stylesheet" href="https://d2u22qwz52vq8m.cloudfront.net/css/cloudscape-design-tokens/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            /* AWS Design System Colors */
            --aws-squid-ink: #232f3e;
            --aws-orange: #ff9900;
            --aws-light-blue: #146eb4;
            --aws-dark-blue: #232f3d;
            --aws-grey-100: #fafbfc;
            --aws-grey-200: #f2f3f3;
            --aws-grey-300: #e9ebed;
            --aws-grey-400: #d5dbdb;
            --aws-grey-500: #879596;
            --aws-grey-600: #687078;
            --aws-grey-700: #414b53;
            --aws-grey-800: #2b3137;
            --aws-grey-900: #161b1f;
        }
        
        body {
            font-family: "Amazon Ember", "Helvetica Neue", "Roboto", "Arial", sans-serif;
            background-color: var(--aws-grey-100);
            min-height: 100vh;
            font-size: 16px;
            color: var(--aws-grey-900);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
            width: 100%;
            max-width: 500px;
        }
        
        .register-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
            overflow: hidden;
            border: 1px solid var(--aws-grey-300);
        }
        
        .register-header {
            background: var(--aws-squid-ink);
            color: white;
            padding: 32px 24px 24px;
            text-align: center;
            border-bottom: 1px solid var(--aws-grey-300);
        }
        
        .register-header .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .register-header .brand i {
            color: var(--aws-orange);
            font-size: 32px;
            margin-right: 12px;
        }
        
        .register-header h1 {
            font-size: 24px;
            margin: 0;
            font-weight: 600;
            color: white;
        }
        
        .register-header p {
            font-size: 14px;
            margin: 8px 0 0;
            opacity: 0.8;
            color: var(--aws-grey-300);
        }
        
        .register-body {
            padding: 32px 24px;
            background: white;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--aws-grey-900);
            margin-bottom: 8px;
            font-size: 14px;
            display: block;
        }
        
        .form-control {
            border: 1px solid var(--aws-grey-400);
            border-radius: 4px;
            padding: 12px 16px;
            font-size: 14px;
            width: 100%;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
            background: white;
            color: var(--aws-grey-900);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--aws-light-blue);
            box-shadow: 0 0 0 2px rgba(20, 110, 180, 0.2);
        }
        
        .form-control:hover {
            border-color: var(--aws-grey-600);
        }
        
        .btn-primary {
            background: var(--aws-light-blue);
            border: 1px solid var(--aws-light-blue);
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 600;
            width: 100%;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background: #0f5a96;
            border-color: #0f5a96;
            color: white;
        }
        
        .btn-primary:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(20, 110, 180, 0.3);
        }
        
        .divider {
            text-align: center;
            margin: 24px 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--aws-grey-300);
        }
        
        .divider span {
            background: white;
            padding: 0 16px;
            color: var(--aws-grey-600);
            font-size: 14px;
        }
        
        .login-link {
            text-align: center;
            margin-top: 16px;
        }
        
        .login-link a {
            color: var(--aws-light-blue);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }
        
        .login-link a:hover {
            color: #0f5a96;
            text-decoration: underline;
        }
        
        .alert {
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            padding: 12px 16px;
            border: 1px solid;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .password-requirements {
            font-size: 12px;
            color: var(--aws-grey-600);
            margin-top: 5px;
        }
        
        .approval-notice {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #856404;
        }
        
        .approval-notice i {
            color: var(--aws-orange);
        }
        
        .mb-3 {
            margin-bottom: 16px;
        }
        
        .mb-4 {
            margin-bottom: 24px;
        }
        
        .aws-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 1px solid var(--aws-grey-300);
            color: var(--aws-grey-600);
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="brand">
                    <i class="fas fa-user-plus"></i>
                    <h1>Create Account</h1>
                </div>
                <p>Join our inventory management system</p>
            </div>
            
            <div class="register-body">
                <div class="approval-notice">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Account Approval Required:</strong> Your registration will be reviewed by our administrator before access is granted.
                </div>
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form method="POST" action="{{ route('auth.register') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="{{ old('name') }}" required autofocus placeholder="Enter your full name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required placeholder="Enter your email address">
                    </div>
                    
                    <div class="mb-3">
                        <label for="company" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company" name="company" 
                               value="{{ old('company') }}" required placeholder="Enter your company name">
                    </div>
                    
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number (Optional)</label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="{{ old('phone') }}" placeholder="Enter your phone number">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Create a secure password">
                        <div class="password-requirements">
                            Password must be at least 8 characters long
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required placeholder="Confirm your password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>
                
                <div class="divider">
                    <span>Already have an account?</span>
                </div>
                
                <div class="login-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign in to your account
                    </a>
                </div>
                
                <div class="aws-footer">
                    <p>© 2025 Inventory Management System. Built with AWS Design System.</p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
