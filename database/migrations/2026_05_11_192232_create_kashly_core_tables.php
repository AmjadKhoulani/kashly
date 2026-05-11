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
        // Add fields to users table (via separate schema call if needed, but here we do new tables)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('owner'); // admin, owner, partner
            $table->string('plan')->default('free'); // free, pro
        });

        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->decimal('equity_percentage', 5, 2);
            $table->decimal('initial_capital', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('cash'); // cash, bank, crypto
            $table->decimal('balance', 15, 2)->default(0);
            $table->boolean('is_personal')->default(true);
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('partner_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense', 'capital', 'transfer', 'distribution']);
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('transaction_date')->useCurrent();
            $table->timestamps();
        });

        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['debt', 'claim']); // debt (owed by user), claim (owed to user)
            $table->string('contact_name');
            $table->decimal('amount', 15, 2);
            $table->decimal('remaining_amount', 15, 2);
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending'); // pending, partial, paid
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('businesses');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'plan']);
        });
    }
};
