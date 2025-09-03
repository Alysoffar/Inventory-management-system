@echo off
REM Inventory Management System Deployment Script for Windows
REM This script automates the deployment process on Windows/XAMPP

echo.
echo ==========================================
echo  Inventory Management System Deployment
echo ==========================================
echo.

REM Check if .env file exists
if not exist .env (
    echo [INFO] .env file not found. Copying from .env.example...
    copy .env.example .env
    echo [INFO] .env file created. Please update your database and email credentials.
    echo.
)

REM Install Composer dependencies
echo [INFO] Installing Composer dependencies...
where composer >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    composer install --optimize-autoloader --no-dev
) else (
    echo [ERROR] Composer not found. Please install Composer first.
    pause
    exit /b 1
)

REM Generate application key if not exists
findstr /C:"APP_KEY=base64:" .env >nul
if %ERRORLEVEL% NEQ 0 (
    echo [INFO] Generating application key...
    php artisan key:generate
)

REM Install Node.js dependencies
echo [INFO] Installing Node.js dependencies...
where npm >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    npm install
    npm run production
) else (
    echo [WARNING] Node.js/npm not found. Frontend assets may not be compiled.
)

REM Create storage directories
echo [INFO] Creating storage directories...
if not exist "storage\app\public" mkdir "storage\app\public"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"

REM Create symbolic link for public storage
echo [INFO] Creating storage symbolic link...
php artisan storage:link

REM Run database migrations
echo [INFO] Running database migrations...
php artisan migrate --force

REM Check if production mode
if "%1"=="production" (
    echo [INFO] Optimizing for production...
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache
)

REM Clear any existing caches
echo [INFO] Clearing caches...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo ==========================================
echo  Deployment completed successfully! ✅
echo ==========================================
echo.

REM Display next steps
echo 📋 Next Steps:
echo 1. Update your .env file with correct database credentials
echo 2. Configure your email settings for notifications
echo 3. Start Apache and MySQL in XAMPP Control Panel
echo 4. Access via: http://localhost/inventory-system/public
echo.
echo 🔗 To start development server:
echo    php artisan serve
echo.
echo 📧 Email notifications will be sent to: alysoffar06@gmail.com
echo.
echo Happy inventory managing! 📦
echo.

pause
