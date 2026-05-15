<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    Route::get('/funds', [\App\Http\Controllers\Api\FundController::class, 'index']);
    Route::get('/funds/{id}', [\App\Http\Controllers\Api\FundController::class, 'show']);

    Route::get('/transactions', [\App\Http\Controllers\Api\TransactionController::class, 'index']);
    Route::get('/transactions/categories', [\App\Http\Controllers\Api\TransactionController::class, 'categories']);
    Route::post('/transactions', [\App\Http\Controllers\Api\TransactionController::class, 'store']);
    Route::post('/transactions/transfer', [\App\Http\Controllers\Api\TransactionController::class, 'transfer']);

    Route::get('/categories', function() {
        return \App\Models\Category::where('is_default', true)
            ->orWhere('user_id', auth()->id())
            ->get();
    });
});
