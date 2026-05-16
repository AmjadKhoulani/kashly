<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::post('/webhooks/madaaq', [WebhookController::class, 'madaaq']);

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

    Route::get('/wallets', [\App\Http\Controllers\Api\WalletController::class, 'index']);
    Route::get('/wallets/{id}', [\App\Http\Controllers\Api\WalletController::class, 'show']);

    Route::get('/businesses', [\App\Http\Controllers\Api\BusinessController::class, 'index']);
    Route::get('/businesses/{id}', [\App\Http\Controllers\Api\BusinessController::class, 'show']);

    Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class);
});
