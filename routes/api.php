<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Public menu (accessible to all authenticated users)
    Route::get('/menu', [MenuController::class, 'index']);
    Route::get('/menu/{menuItem}', [MenuController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);

    // Customer routes
    Route::prefix('customer')->group(function () {
        Route::get('/orders', [OrderController::class, 'history']);
    });

    // Cashier and Admin routes
    Route::middleware('role:cashier,admin')->group(function () {
        // Orders
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/queue', [OrderController::class, 'queue']);
        Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']);
        
        // Inventory
        Route::get('/inventory/low-stock', [MenuController::class, 'lowStock']);
        Route::post('/inventory/adjust', [InventoryController::class, 'adjustStock']);
    });

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // Categories
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Menu management
        Route::post('/menu', [MenuController::class, 'store']);
        Route::put('/menu/{menuItem}', [MenuController::class, 'update']);
        Route::delete('/menu/{menuItem}', [MenuController::class, 'destroy']);
        Route::patch('/menu/{menuItem}/toggle', [MenuController::class, 'toggleAvailability']);

        // Inventory
        Route::get('/inventory/logs', [InventoryController::class, 'logs']);
        Route::post('/inventory/bulk-restock', [InventoryController::class, 'bulkRestock']);

        // Reports
        Route::get('/reports/sales', [ReportController::class, 'sales']);
        Route::get('/reports/best-selling', [ReportController::class, 'bestSelling']);
        Route::get('/reports/category-breakdown', [ReportController::class, 'categoryBreakdown']);
        Route::get('/reports/order-trend', [ReportController::class, 'orderTrend']);
        Route::get('/reports/summary', [ReportController::class, 'summary']);

        // Orders (full access)
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
    });
});