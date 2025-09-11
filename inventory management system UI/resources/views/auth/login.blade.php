<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventory Management System</title>
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
        
        .login-container {
            width: 100%;
            max-width: 400px;
        }
        
        .login-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
            overflow: hidden;
            border: 1px solid var(--aws-grey-300);
        }
        
        .login-header {
            background: var(--aws-squid-ink);
            color: white;
            padding: 32px 24px 24px;
            text-align: center;
            border-bottom: 1px solid var(--aws-grey-300);
        }
        
        .login-header .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .login-header .brand i {
            color: var(--aws-orange);
            font-size: 32px;
            margin-right: 12px;
        }
        
        .login-header h1 {
            font-size: 24px;
            margin: 0;
            font-weight: 600;
            color: white;
        }
        
        .login-header p {
            font-size: 14px;
            margin: 8px 0 0;
            opacity: 0.8;
            color: var(--aws-grey-300);
        }
        
        .login-body {
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
        
        .form-check {
            margin: 16px 0;
        }
        
        .form-check-input {
            margin-right: 8px;
        }
        
        .form-check-label {
            font-size: 14px;
            color: var(--aws-grey-700);
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
        
        .register-link {
            text-align: center;
            margin-top: 16px;
        }
        
        .register-link a {
            color: var(--aws-light-blue);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
        }
        
        .register-link a:hover {
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
        
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        
        .mb-3 {
            margin-bottom: 16px;
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
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="brand">
                    <i class="fas fa-boxes"></i>
                    <h1>Inventory System</h1>
                </div>
                <p>Sign in to access your dashboard</p>
            </div>
            
            <div class="login-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif
                
                <form method="POST" action="{{ route('auth.login') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Enter your password">
                    </div>
                    
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">
                            Keep me signed in
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>
                
                <div class="divider">
                    <span>New to our platform?</span>
                </div>
                
                <div class="register-link">
                    <a href="{{ route('auth.register') }}">
                        <i class="fas fa-user-plus me-2"></i>Create your account
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
