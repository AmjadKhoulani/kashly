<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestmentFundController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\IntegrationsController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/funds', [InvestmentFundController::class, 'index'])->name('funds.index');
    Route::get('/funds/{id}', [InvestmentFundController::class, 'show'])->name('funds.show');
    Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/integrations', [IntegrationsController::class, 'index'])->name('integrations.index');
});

// Super Admin Routes
Route::middleware(['auth', 'super_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('admin.users');
});

// Integrations (Public Webhooks)
Route::post('/webhooks/shopify/{id}', [WebhookController::class, 'shopify'])->name('webhooks.shopify');
Route::post('/webhooks/whmcs/{id}', [WebhookController::class, 'whmcs'])->name('webhooks.whmcs');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
