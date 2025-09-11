@echo off
REM ====================================================
REM AI-Powered Inventory Management System
REM Quick Setup Script for Windows
REM ====================================================

echo 🚀 Setting up AI-Powered Inventory Management System...

REM Check if we're in the right directory
if not exist "inventory management system UI" (
    echo ❌ Error: Please run this script from the project root directory
    pause
    exit /b 1
)

REM Install Python dependencies
echo 📦 Installing Python dependencies...
python -m pip install -r requirements.txt
if errorlevel 1 (
    echo ❌ Error: Failed to install Python dependencies
    pause
    exit /b 1
)

REM Setup Laravel
echo 🔧 Setting up Laravel application...
cd "inventory management system UI"

REM Install PHP dependencies
composer install --no-dev --optimize-autoloader
if errorlevel 1 (
    echo ❌ Error: Failed to install PHP dependencies
    pause
    exit /b 1
)

REM Setup environment
if not exist ".env" (
    copy .env.example .env
    echo ✅ Created .env file
)

REM Generate application key
php artisan key:generate

REM Setup database
if not exist "database\database.sqlite" (
    type nul > database\database.sqlite
    echo ✅ Created SQLite database
)

REM Run migrations
php artisan migrate --force

REM Seed database with sample data
php artisan db:seed --force

REM Cache config for better performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo 🎉 Setup complete!
echo.
echo 📚 Quick Start:
echo 1. Start AI API: python ai_prediction_api.py
echo 2. Start Web Server: cd "inventory management system UI" ^&^& php artisan serve
echo 3. Open browser: http://localhost:8000
echo.
echo 🔐 Default Login:
echo    Email: admin@example.com
echo    Password: password
echo.
echo ✨ Happy inventory managing!
pause
