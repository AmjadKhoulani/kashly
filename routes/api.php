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
    Route::post('/funds', [\App\Http\Controllers\Api\FundController::class, 'store']);
    Route::get('/funds/{id}', [\App\Http\Controllers\Api\FundController::class, 'show']);
    Route::put('/funds/{id}', [\App\Http\Controllers\Api\FundController::class, 'update']);
    Route::delete('/funds/{id}', [\App\Http\Controllers\Api\FundController::class, 'destroy']);

    Route::get('/transactions', [\App\Http\Controllers\Api\TransactionController::class, 'index']);
    Route::get('/transactions/categories', [\App\Http\Controllers\Api\TransactionController::class, 'categories']);
    Route::post('/transactions', [\App\Http\Controllers\Api\TransactionController::class, 'store']);
    Route::post('/transactions/transfer', [\App\Http\Controllers\Api\TransactionController::class, 'transfer']);
    Route::put('/transactions/{id}', [\App\Http\Controllers\Api\TransactionController::class, 'update']);
    Route::delete('/transactions/{id}', [\App\Http\Controllers\Api\TransactionController::class, 'destroy']);

    Route::get('/wallets', [\App\Http\Controllers\Api\WalletController::class, 'index']);
    Route::get('/wallets/{id}', [\App\Http\Controllers\Api\WalletController::class, 'show']);
    Route::delete('/wallets/{id}', [\App\Http\Controllers\Api\WalletController::class, 'destroy']);

    Route::get('/businesses', [\App\Http\Controllers\Api\BusinessController::class, 'index']);
    Route::get('/businesses/{id}', [\App\Http\Controllers\Api\BusinessController::class, 'show']);
    Route::delete('/businesses/{id}', [\App\Http\Controllers\Api\BusinessController::class, 'destroy']);

    Route::apiResource('categories', \App\Http\Controllers\Api\CategoryController::class);

    Route::get('/ledger', [\App\Http\Controllers\Api\LedgerController::class, 'index']);
    Route::post('/ledger', [\App\Http\Controllers\Api\LedgerController::class, 'store']);
    Route::get('/ledger/{id}', [\App\Http\Controllers\Api\LedgerController::class, 'show']);
    Route::put('/ledger/{id}', [\App\Http\Controllers\Api\LedgerController::class, 'update']);
    Route::delete('/ledger/{id}', [\App\Http\Controllers\Api\LedgerController::class, 'destroy']);
    Route::post('/ledger/{id}/payment', [\App\Http\Controllers\Api\LedgerController::class, 'addPayment']);
});
