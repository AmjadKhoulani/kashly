<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvestmentFundController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\IntegrationsController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PartnerDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/funds', [InvestmentFundController::class, 'index'])->name('funds.index');
    Route::post('/funds', [InvestmentFundController::class, 'store'])->name('funds.store');
    Route::get('/funds/{id}', [InvestmentFundController::class, 'show'])->name('funds.show');
    Route::get('/funds/{id}/transactions', [InvestmentFundController::class, 'fundTransactions'])->name('funds.transactions');
    Route::post('/funds/{id}/add-partner', [InvestmentFundController::class, 'addPartner'])->name('funds.addPartner');
    Route::put('/equities/{id}', [InvestmentFundController::class, 'updateEquity'])->name('funds.updateEquity');
    Route::delete('/equities/{id}', [InvestmentFundController::class, 'removePartner'])->name('funds.removePartner');
    Route::post('/funds/{id}/add-asset', [InvestmentFundController::class, 'addAsset'])->name('funds.addAsset');
    Route::post('/funds/{id}/add-payment-method', [InvestmentFundController::class, 'addPaymentMethod'])->name('funds.addPaymentMethod');
    Route::get('/funds/{id}/distributions', [InvestmentFundController::class, 'distributions'])->name('funds.distributions');
    Route::delete('/funds/{id}', [InvestmentFundController::class, 'destroy'])->name('funds.destroy');
    Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::delete('/partners/{partner}', [PartnerController::class, 'destroy'])->name('partners.destroy');
    Route::post('/partners/{partner}/link', [PartnerController::class, 'linkAccount'])->name('partners.link');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/integrations', [IntegrationsController::class, 'index'])->name('integrations.index');
    Route::resource('payment-methods', PaymentMethodController::class);
});

// Super Admin Routes
Route::middleware(['auth', 'super_admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('admin.users');
});

// Partner Routes
Route::middleware(['auth', 'partner'])->prefix('partner')->group(function () {
    Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('partner.dashboard');
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
