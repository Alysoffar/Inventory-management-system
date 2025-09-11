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
use App\Http\Controllers\AuthController;

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

// Authentication Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/pending', [AuthController::class, 'pending'])->name('pending');
});

// Standard Laravel auth routes (for middleware compatibility)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Public approval route (accessible from email without login)
    Route::get('/approve-user/{id}', [App\Http\Controllers\AdminController::class, 'approveUser'])->name('approve-user-get');
    
    // Protected admin routes (require authentication)
    Route::middleware(['auth'])->group(function () {
        Route::get('/pending-users', [App\Http\Controllers\AdminController::class, 'pendingUsers'])->name('pending-users');
        Route::post('/approve-user/{id}', [App\Http\Controllers\AdminController::class, 'approveUser'])->name('approve-user');
        Route::delete('/reject-user/{id}', [App\Http\Controllers\AdminController::class, 'rejectUser'])->name('reject-user');
        Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('users');
    });
});

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Protected Routes (require authentication)
Route::middleware(['auth'])->group(function () {
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
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
        Route::get('/export/sales-pdf', [ReportController::class, 'exportSalesPdf'])->name('export.sales-pdf');
        Route::get('/export/inventory-pdf', [ReportController::class, 'exportInventoryPdf'])->name('export.inventory-pdf');
    });

    // AI Prediction Routes
    Route::prefix('ai')->name('ai.')->group(function () {
        // Prediction Dashboard
        Route::get('/predictions', [AIPredictionController::class, 'index'])->name('predictions.index');
        Route::get('/predictions/create', [AIPredictionController::class, 'create'])->name('predictions.create');
        Route::get('/predictions/export', [AIPredictionController::class, 'export'])->name('predictions.export');
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
});

// Test email route (remove in production)
Route::get('/test-email', function () {
    try {
        // Get the first user or create a dummy one for testing
        $user = App\Models\User::first();
        if (!$user) {
            $user = new App\Models\User([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'company' => 'Test Company',
                'phone' => '1234567890'
            ]);
            $user->id = 1; // Fake ID for testing
        }

        Mail::to('alysoffar06@gmail.com')->send(new App\Mail\AccountApprovalRequest($user));
        return response()->json([
            'status' => 'success',
            'message' => 'Test email sent successfully to alysoffar06@gmail.com!',
            'mail_driver' => config('mail.default'),
            'to' => 'alysoffar06@gmail.com',
            'approval_link' => route('admin.approve-user-get', $user->id)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send email: ' . $e->getMessage(),
            'mail_driver' => config('mail.default'),
            'config' => [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
            ]
        ], 500);
    }
});

// Test approval page route
Route::get('/test-approval/{id}', function ($id) {
    $user = App\Models\User::find($id);
    $message = "✅ This is a test approval page for user ID: {$id}";
    $type = 'success';
    
    return view('admin.approval-result', compact('user', 'message', 'type'));
});

// Debug routes - Remove in production
Route::get('/debug/users', function () {
    $users = App\Models\User::all();
    $output = '<h1>All Users Debug</h1>';
    
    foreach ($users as $user) {
        $output .= '<div style="margin: 10px; padding: 10px; border: 1px solid #ccc;">';
        $output .= '<strong>ID:</strong> ' . $user->id . '<br>';
        $output .= '<strong>Name:</strong> ' . $user->name . '<br>';
        $output .= '<strong>Email:</strong> ' . $user->email . '<br>';
        $output .= '<strong>Status:</strong> ' . $user->status . '<br>';
        $output .= '<strong>Role:</strong> ' . ($user->role ?? 'No role') . '<br>';
        
        if ($user->status === 'pending') {
            $output .= '<a href="/debug/approve-user/' . $user->id . '" style="background: green; color: white; padding: 5px 10px; text-decoration: none; margin: 5px;">Approve User</a>';
        }
        $output .= '</div>';
    }
    
    return $output;
});

Route::get('/debug/approve-user/{id}', function ($id) {
    $user = App\Models\User::findOrFail($id);
    $user->status = 'approved';
    $user->save();
    
    return redirect('/debug/users')->with('message', 'User ' . $user->name . ' approved successfully!');
});

Route::get('/debug/clear-logs', function () {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        file_put_contents($logFile, '');
        return 'Log file cleared successfully!';
    }
    return 'Log file not found.';
});
