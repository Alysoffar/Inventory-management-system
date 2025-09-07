<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inventory Management System')</title>
    
    <!-- AWS Cloudscape Design System Styles -->
    <link rel="stylesheet" href="https://d2u22qwz52vq8m.cloudfront.net/css/cloudscape-design-tokens/index.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Leaflet for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Custom Styles -->
    @yield('styles')
    
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
            
            /* Status Colors */
            --aws-green: #037f0c;
            --aws-red: #d13313;
            --aws-yellow: #8d6e00;
            
            /* Spacing */
            --space-xxs: 2px;
            --space-xs: 4px;
            --space-s: 8px;
            --space-m: 16px;
            --space-l: 24px;
            --space-xl: 32px;
            --space-xxl: 48px;
        }
        
        body {
            font-family: "Amazon Ember", "Helvetica Neue", "Roboto", "Arial", sans-serif;
            background-color: var(--aws-grey-100);
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.4;
            color: var(--aws-grey-900);
        }
        
        /* AWS-style sidebar */
        .sidebar { 
            background: var(--aws-squid-ink);
            min-height: 100vh; 
            color: white;
            box-shadow: 2px 0 8px rgba(0,0,0,0.12);
            position: fixed;
            top: 0;
            left: 0;
            width: 64px;
            z-index: 1000;
            transition: width 0.2s ease;
            overflow: hidden;
            border-right: 1px solid var(--aws-grey-300);
        }
        
        .sidebar:hover {
            width: 240px;
            box-shadow: 2px 0 16px rgba(0,0,0,0.15);
        }
        
        .sidebar .sidebar-brand {
            padding: var(--space-m);
            border-bottom: 1px solid var(--aws-grey-700);
            display: flex;
            align-items: center;
        }
        
        .sidebar .sidebar-brand i {
            color: var(--aws-orange);
            font-size: 24px;
            margin-right: var(--space-s);
        }
        
        .sidebar .sidebar-brand span {
            font-size: 16px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.2s ease 0.1s;
        }
        
        .sidebar:hover .sidebar-brand span {
            opacity: 1;
        }
        
        /* AWS-style navigation */
        .nav-link { 
            color: #ffffff !important; 
            padding: var(--space-s) var(--space-m) !important; 
            border-radius: 0; 
            margin: 0; 
            transition: all 0.15s ease;
            font-weight: 400;
            font-size: 14px;
            display: flex;
            align-items: center;
            white-space: nowrap;
            position: relative;
            border-left: 3px solid transparent;
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: var(--space-s);
            font-size: 16px;
            flex-shrink: 0;
        }
        
        .nav-link span {
            opacity: 0;
            transition: opacity 0.2s ease 0.1s;
        }
        
        .sidebar:hover .nav-link span {
            opacity: 1;
        }
        
        .nav-link:hover, .nav-link.active { 
            background-color: var(--aws-grey-800) !important;
            color: var(--aws-orange) !important;
            border-left-color: var(--aws-orange);
        }
        
        .nav-link.active {
            background-color: var(--aws-grey-700) !important;
            border-left-color: var(--aws-orange);
        }
        
        /* Tooltip for collapsed sidebar */
        .sidebar:not(:hover) .nav-link::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 70px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--aws-grey-900);
            color: white;
            padding: var(--space-xs) var(--space-s);
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1001;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease;
        }
        
        .sidebar:not(:hover) .nav-link:hover::after {
            opacity: 1;
        }
        
        /* Navigation groups */
        .nav-group {
            border-bottom: 1px solid var(--aws-grey-700);
            margin-bottom: var(--space-s);
            padding-bottom: var(--space-s);
        }
        
        .nav-group:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .main-content {
            margin-left: 64px;
            min-height: 100vh;
            transition: margin-left 0.2s ease;
        }
        
        .content-wrapper {
            padding: var(--space-l) var(--space-xl);
            max-width: 100%;
        }
        
        /* AWS-style cards */
        .card { 
            border: 1px solid var(--aws-grey-300);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
            transition: all 0.15s ease;
            margin-bottom: var(--space-m);
            background: white;
        }
        
        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
        }
        
        .card-header { 
            background: white;
            color: var(--aws-grey-900);
            border-bottom: 1px solid var(--aws-grey-300);
            border-radius: 8px 8px 0 0 !important;
            font-weight: 600;
            padding: var(--space-m);
            font-size: 16px;
        }
        
        .card-body {
            padding: var(--space-m);
        }
        
        .card-title {
            margin-bottom: var(--space-s);
            font-weight: 600;
            color: var(--aws-grey-900);
            font-size: 16px;
        }
        
        /* AWS-style buttons */
        .btn { 
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.15s ease;
            padding: var(--space-s) var(--space-m);
            font-size: 14px;
            border: 1px solid transparent;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: var(--space-xs);
        }
        
        .btn-primary { 
            background-color: var(--aws-orange);
            border-color: var(--aws-orange);
            color: white;
        }
        
        .btn-primary:hover { 
            background-color: #e88b00;
            border-color: #e88b00;
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: white;
            border-color: var(--aws-grey-400);
            color: var(--aws-grey-900);
        }
        
        .btn-secondary:hover {
            background-color: var(--aws-grey-100);
            border-color: var(--aws-grey-500);
        }
        
        .btn-success {
            background-color: var(--aws-green);
            border-color: var(--aws-green);
            color: white;
        }
        
        .btn-warning {
            background-color: var(--aws-yellow);
            border-color: var(--aws-yellow);
            color: white;
        }
        
        .btn-danger {
            background-color: var(--aws-red);
            border-color: var(--aws-red);
            color: white;
        }
        
        /* AWS-style page header */
        .page-header {
            margin-bottom: var(--space-xl);
            padding-bottom: var(--space-m);
            border-bottom: 1px solid var(--aws-grey-300);
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 300;
            color: var(--aws-grey-900);
            margin-bottom: var(--space-xs);
        }
        
        .page-subtitle {
            font-size: 14px;
            color: var(--aws-grey-600);
        }
        
        /* AWS-style alerts */
        .alert { 
            border-radius: 4px;
            border: 1px solid;
            padding: var(--space-m);
            margin-bottom: var(--space-m);
        }
        
        .alert-success {
            background-color: #f0f9f0;
            border-color: #4caf50;
            color: var(--aws-green);
        }
        
        .alert-danger {
            background-color: #fff5f5;
            border-color: #f44336;
            color: var(--aws-red);
        }
        
        .alert-warning {
            background-color: #fffdf0;
            border-color: #ff9800;
            color: var(--aws-yellow);
        }
        
        /* AWS-style tables */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            background: white;
            border: 1px solid var(--aws-grey-300);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table thead th {
            background: var(--aws-grey-100);
            color: var(--aws-grey-900);
            border-bottom: 1px solid var(--aws-grey-300);
            font-weight: 600;
            padding: var(--space-m);
            font-size: 14px;
        }
        
        .table tbody td {
            padding: var(--space-m);
            border-bottom: 1px solid var(--aws-grey-200);
            vertical-align: middle;
        }
        
        .table tbody tr:hover {
            background-color: var(--aws-grey-100);
        }
        
        .table tbody tr:last-child td {
            border-bottom: none;
        }
        
        /* AWS-style form controls */
        .form-control, .form-select {
            border: 1px solid var(--aws-grey-400);
            border-radius: 4px;
            padding: var(--space-s) var(--space-s);
            font-size: 14px;
            transition: all 0.15s ease;
            background: white;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--aws-orange);
            box-shadow: 0 0 0 2px rgba(255, 153, 0, 0.2);
            outline: none;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--aws-grey-900);
            margin-bottom: var(--space-xs);
        }
        
        /* AWS-style badges */
        .badge {
            font-size: 12px;
            font-weight: 500;
            padding: var(--space-xs) var(--space-s);
            border-radius: 4px;
        }
        
        .badge-primary { 
            background: var(--aws-orange); 
            color: white;
        }
        
        .badge-success { 
            background: var(--aws-green); 
            color: white;
        }
        
        .badge-warning { 
            background: var(--aws-yellow); 
            color: white;
        }
        
        .badge-danger { 
            background: var(--aws-red); 
            color: white;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: var(--space-m);
            }
            
            .main-content {
                margin-left: 64px;
            }
        }
        
        /* Loading states */
        .loading-skeleton {
            background: linear-gradient(90deg, var(--aws-grey-200) 25%, var(--aws-grey-100) 50%, var(--aws-grey-200) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
            overflow: hidden;
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
            margin-right: 0.5rem;
            font-size: 0.9rem;
            flex-shrink: 0;
        }
        
        .nav-link span {
            opacity: 0;
            transition: opacity 0.3s ease 0.1s;
        }
        
        .sidebar:hover .nav-link span {
            opacity: 1;
        }
        
        .nav-link:hover, .nav-link.active { 
            background: rgba(255,255,255,0.15) !important; 
            color: white !important;
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .main-content {
            margin-left: 60px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        .content-wrapper {
            padding: 0.5rem 0.75rem;
            max-width: 100%;
        }
        
        .card { 
            border: none; 
            box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.05); 
            border-radius: 6px;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }
        
        .card:hover {
            box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.08);
            transform: translateY(-1px);
        }
        
        .card-header { 
            background: var(--primary-gradient);
            color: white; 
            border-radius: 6px 6px 0 0 !important;
            font-weight: 600;
            padding: 0.4rem 0.6rem;
            border-bottom: none;
            font-size: 0.75rem;
        }
        
        .card-body {
            padding: 0.5rem;
        }
        
        .card-title {
            margin-bottom: 0.4rem;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.8rem;
        }
        
        .row {
            margin-left: -0.25rem;
            margin-right: -0.25rem;
        }
        
        .row > * {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        
        .btn { 
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.3rem 0.6rem;
            font-size: 0.7rem;
            border: none;
            margin: 0.1rem;
        }
        
        .btn-primary { 
            background: var(--primary-gradient);
        }
        
        .btn-primary:hover { 
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .btn-success {
            background: var(--success-gradient);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #46a8f5 0%, #00e5f2 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        .btn-warning {
            background: var(--warning-gradient);
            color: white;
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #40e070 0%, #35f5d0 100%);
            color: white;
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background: var(--secondary-gradient);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #f085f5 0%, #f54865 100%);
            transform: translateY(-1px);
        }
        
        .btn-group {
            margin: 0.5rem 0;
        }
        
        .btn-group .btn {
            margin: 0;
        }
        
        .alert { 
            border-radius: 10px;
            border: none;
            font-weight: 500;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }
        
        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }
        
        .low-stock { 
            background-color: #fff2f2; 
            border-left: 4px solid #dc3545; 
        }
        
        .stats-card { 
            background: var(--primary-gradient);
            color: white; 
            border-radius: 6px;
        }
        
        .stats-card .card-body {
            padding: 0.5rem 0.4rem;
        }
        
        .stats-card h3 {
            font-size: 1rem;
            margin-bottom: 0.15rem;
        }
        
        .stats-card p {
            font-size: 0.65rem;
            margin-bottom: 0;
        }
        
        .stats-card i {
            font-size: 1rem !important;
            margin-bottom: 0.3rem !important;
        }
        
        /* Ultra-compact dashboard styles */
        .content-wrapper {
            padding: 0.5rem 0.75rem;
            max-width: 100%;
        }
        
        .g-1 > * {
            padding: 0.125rem;
        }
        
        .row.g-1 {
            margin-left: -0.125rem;
            margin-right: -0.125rem;
        }
        
        .table td, .table th {
            padding: 0.25rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .card {
            margin-bottom: 0.5rem;
        }
        
        .badge {
            font-size: 0.65rem;
            padding: 0.25em 0.5em;
        }
        
        .page-title {
            font-size: 1.25rem;
            margin-bottom: 0.15rem;
            line-height: 1.2;
        }
        
        .page-subtitle {
            font-size: 0.7rem;
            color: #6c757d;
            margin-bottom: 0;
        }
        
        /* Minimize table spacing */
        .table {
            font-size: 0.7rem;
        }
        
        .table th, .table td {
            padding: 0.3rem 0.4rem;
            vertical-align: middle;
        }
        
        /* Ultra-compact forms */
        .form-control, .form-select {
            padding: 0.3rem 0.5rem;
            font-size: 0.7rem;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
        }
        
        .form-label {
            font-size: 0.7rem;
            margin-bottom: 0.2rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .shadow {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
        }
        
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        
        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }
        
        .text-gray-800 {
            color: #5a5c69 !important;
        }
        
        .text-gray-300 {
            color: #dddfeb !important;
        }
        
        /* Badge styles */
        .badge {
            border-radius: 6px;
            font-weight: 500;
        }
        
        .badge-primary { background: var(--primary-gradient); }
        .badge-success { background: var(--success-gradient); }
        .badge-warning { background: var(--warning-gradient); color: white; }
        .badge-danger { background: var(--secondary-gradient); }
        .badge-secondary { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
        
        /* Table improvements */
        .table {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .table thead th {
            background: var(--primary-gradient);
            color: white;
            border: none;
            font-weight: 600;
            padding: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
        }
        
        .table tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .table-responsive {
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.1);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-group {
            margin-bottom: 0.75rem;
        }
        
        .input-group {
            margin-bottom: 1rem;
        }
        
        .input-group-text {
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 8px 0 0 8px;
        }
        
        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        
        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }
        
        /* Notification styles */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Page header styles */
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
        
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        /* Stats card improvements */
        .stats-card { 
            background: var(--primary-gradient);
            color: white; 
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.75rem 1.5rem rgba(0,0,0,0.15);
        }
        
        .stats-card .card-body {
            padding: 2rem 1.5rem;
        }
        
        .stats-card h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-card p {
            margin-bottom: 0;
            font-size: 0.875rem;
            opacity: 0.9;
        }
        
        .stats-card i {
            opacity: 0.8;
        }
        
        /* Spacing utilities */
        .section-spacing {
            margin-bottom: 2.5rem;
        }
        
        .content-spacing {
            margin-bottom: 1.5rem;
        }
        
        .element-spacing {
            margin-bottom: 1rem;
        }
        
        /* Ultra-compact responsive design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            
            .sidebar:hover {
                width: 100%;
            }
            
            .sidebar h4 {
                opacity: 1;
            }
            
            .sidebar .nav-link span {
                opacity: 1;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-wrapper {
                padding: 0.4rem;
            }
            
            .page-title {
                font-size: 1rem;
            }
            
            .stats-card .card-body {
                padding: 0.4rem;
            }
            
            .stats-card h3 {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.65rem;
            }
        }
        
        @media (max-width: 576px) {
            .content-wrapper {
                padding: 0.25rem;
            }
            
            .card-body {
                padding: 0.3rem;
            }
            
            .stats-card .card-body {
                padding: 0.3rem;
            }
            
            .stats-card h3 {
                font-size: 0.8rem;
            }
            
            .stats-card p {
                font-size: 0.6rem;
            }
            
            .table tbody td, .table thead th {
                padding: 0.25rem 0.15rem;
                font-size: 0.65rem;
            }
            
            .page-title {
                font-size: 0.9rem;
            }
            
            .btn {
                padding: 0.2rem 0.4rem;
                font-size: 0.6rem;
            }
        }
    </style>
</head>
<body>
<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-brand">
                <i class="fas fa-warehouse"></i>
                <span>Inventory MS</span>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" data-tooltip="Dashboard">
                    <i class="fas fa-th-large"></i> <span>Dashboard</span>
                </a>
                
                <div class="nav-group">
                    <a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.dashboard') }}" data-tooltip="Inventory">
                        <i class="fas fa-warehouse"></i> <span>Inventory</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}" data-tooltip="Products">
                        <i class="fas fa-boxes"></i> <span>Products</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('inventory.map') ? 'active' : '' }}" href="{{ route('inventory.map') }}" data-tooltip="Live Tracking">
                        <i class="fas fa-map-marked-alt"></i> <span>Live Tracking</span>
                    </a>
                </div>
                
                <div class="nav-group">
                    <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}" data-tooltip="Customers">
                        <i class="fas fa-user-friends"></i> <span>Customers</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}" data-tooltip="Suppliers">
                        <i class="fas fa-truck-loading"></i> <span>Suppliers</span>
                    </a>
                </div>
                
                <div class="nav-group">
                    <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}" data-tooltip="Sales">
                        <i class="fas fa-shopping-cart"></i> <span>Sales</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}" data-tooltip="Purchases">
                        <i class="fas fa-shopping-bag"></i> <span>Purchases</span>
                    </a>
                </div>
                
                <div class="nav-group">
                    <a class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}" href="{{ route('ai.predictions.index') }}" data-tooltip="AI Predictions">
                        <i class="fas fa-brain"></i> <span>AI Predictions</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}" data-tooltip="Analytics">
                        <i class="fas fa-chart-line"></i> <span>Analytics</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-wrapper">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@yield('scripts')
</body>
</html>
