@echo off
echo ========================================
echo  AI Inventory Management System Startup
echo ========================================
echo.

echo [1/4] Starting Python AI API Server...
cd /d "d:\WORK\Findo\AIMODEL"
start "AI API Server" cmd /k "python ai_prediction_api.py"
timeout /t 3 >nul

echo [2/4] Waiting for AI API to initialize...
timeout /t 5 >nul

echo [3/4] Starting Laravel Application Server...
cd /d "C:\xampp\htdocs\inventory-management-system"
start "Laravel Server" cmd /k "php artisan serve --host=127.0.0.1 --port=8000"
timeout /t 3 >nul

echo [4/4] Opening Application in Browser...
timeout /t 3 >nul
start http://localhost:8000

echo.
echo ========================================
echo  System Started Successfully!
echo ========================================
echo.
echo  Laravel App: http://localhost:8000
echo  AI API:      http://localhost:5000
echo.
echo  Press any key to stop all servers...
pause >nul

echo.
echo Stopping servers...
taskkill /F /FI "WindowTitle eq AI API Server*" 2>nul
taskkill /F /FI "WindowTitle eq Laravel Server*" 2>nul
echo Servers stopped.
pause
