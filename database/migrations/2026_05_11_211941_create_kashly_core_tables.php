<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Partners (Shareholders)
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Businesses (Income Sources like Store, Internet Co)
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable(); // Store, Service, etc.
            $table->decimal('total_value', 15, 2)->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Investment Funds (Pooled Capital)
        Schema::create('investment_funds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('capital', 15, 2);
            $table->decimal('current_value', 15, 2);
            $table->string('status')->default('active'); // active, closed
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Equities (Links Partners to Funds/Businesses)
        Schema::create('equities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->morphs('equitable'); // business or fund
            $table->decimal('percentage', 5, 2); // e.g. 25.00
            $table->decimal('amount', 15, 2); // capital contribution
            $table->timestamps();
        });

        // Wallets (Personal Cash/Savings)
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Debts and Claims (Loans)
        Schema::create('debts_claims', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['debt', 'claim']); // debt: I owe, claim: someone owes me
            $table->date('due_date')->nullable();
            $table->string('person_name')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Transactions (Universal Ledger)
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense', 'transfer']);
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->morphs('transactionable'); // business, fund, or wallet
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('transaction_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('debts_claims');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('equities');
        Schema::dropIfExists('investment_funds');
        Schema::dropIfExists('businesses');
        Schema::dropIfExists('partners');
    }
};
