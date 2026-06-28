<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->default('residential'); // residential, commercial
            $table->decimal('rent_amount', 15, 2)->default(0.00);
            $table->string('status')->default('vacant'); // vacant, occupied, maintenance
            $table->timestamps();
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('national_id')->nullable();
            $table->timestamps();
        });

        Schema::create('lease_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 15, 2);
            $table->string('billing_cycle')->default('monthly'); // monthly, quarterly, semi_annually, annually
            $table->string('status')->default('active'); // active, completed, terminated
            $table->timestamps();
        });

        Schema::create('lease_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_contract_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_due', 15, 2);
            $table->decimal('amount_paid', 15, 2)->default(0.00);
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->unsignedBigInteger('payment_method_id')->nullable(); // manually constrained to avoid strict table lock
            $table->unsignedBigInteger('transaction_id')->nullable(); // manually constrained to avoid strict table lock
            $table->string('status')->default('pending'); // pending, paid, partially_paid, overdue
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lease_payments');
        Schema::dropIfExists('lease_contracts');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('units');
        Schema::dropIfExists('properties');
    }
};
