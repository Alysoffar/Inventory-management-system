<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AIPredictionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Inventory Management Routes
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('dashboard');
    Route::get('/map', [InventoryController::class, 'map'])->name('map');
    Route::get('/logs', [InventoryController::class, 'logs'])->name('logs');
    Route::get('/notifications', [InventoryController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [InventoryController::class, 'markNotificationRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [InventoryController::class, 'markAllNotificationsRead'])->name('notifications.read-all');
    Route::post('/check', [InventoryController::class, 'runInventoryCheck'])->name('check');
    Route::get('/analytics', [InventoryController::class, 'analytics'])->name('analytics');
    Route::get('/export', [InventoryController::class, 'export'])->name('export');
});

// Products Resource Routes with additional inventory routes
Route::resource('products', ProductController::class);
Route::prefix('products/{id}')->name('products.')->group(function () {
    Route::post('/adjust-stock', [ProductController::class, 'adjustStock'])->name('adjust-stock');
    Route::post('/manual-restock', [ProductController::class, 'manualRestock'])->name('manual-restock');
});

// Customers Resource Routes
Route::resource('customers', CustomerController::class);

// Suppliers Resource Routes
Route::resource('suppliers', SupplierController::class);

// Sales Resource Routes
Route::resource('sales', SaleController::class);

// Purchases Resource Routes
Route::resource('purchases', PurchaseController::class);

// Reports Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
    Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
    Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
    Route::get('/export/sales-pdf', [ReportController::class, 'exportSalesPdf'])->name('export.sales-pdf');
    Route::get('/export/inventory-pdf', [ReportController::class, 'exportInventoryPdf'])->name('export.inventory-pdf');
});

// AI Prediction Routes
Route::prefix('ai')->name('ai.')->group(function () {
    // Prediction Dashboard
    Route::get('/predictions', [AIPredictionController::class, 'index'])->name('predictions.index');
    Route::get('/predictions/create', [AIPredictionController::class, 'create'])->name('predictions.create');
    Route::post('/predict', [AIPredictionController::class, 'predict'])->name('predict');
    Route::post('/bulk-predict', [AIPredictionController::class, 'bulkPredict'])->name('bulk-predict');
    
    // AI Insights and Analytics
    Route::get('/insights', [AIPredictionController::class, 'insights'])->name('insights');
    
    // API endpoints for AI services
    Route::get('/health', [AIPredictionController::class, 'healthCheck'])->name('health');
});

// API Routes for AJAX calls
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    Route::get('/suppliers/search', [SupplierController::class, 'search'])->name('suppliers.search');
    Route::get('/products/{product}/stock', [ProductController::class, 'getStock'])->name('products.stock');
    Route::get('/products/map-data', [ProductController::class, 'mapData'])->name('products.map-data');
    
    // AI API endpoints
    Route::get('/ai/health', [AIPredictionController::class, 'healthCheck'])->name('ai.health');
});
