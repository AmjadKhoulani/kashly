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
        Schema::table('investment_funds', function (Blueprint $table) {
            $table->string('distribution_frequency')->default('monthly')->after('status');
            $table->string('currency')->default('USD')->after('distribution_frequency');
        });

        Schema::table('equities', function (Blueprint $table) {
            $table->string('equity_type')->default('contribution')->after('percentage');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('currency')->default('USD')->after('amount');
            $table->decimal('exchange_rate', 15, 6)->default(1.0)->after('currency');
            $table->decimal('original_amount', 15, 2)->nullable()->after('exchange_rate');
        });

        Schema::create('fund_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_fund_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); 
            $table->decimal('value', 15, 2);
            $table->date('purchase_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_fund_id')->constrained()->onDelete('cascade');
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('net_amount', 15, 2);
            $table->date('distribution_date');
            $table->string('status')->default('projected');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributions');
        Schema::dropIfExists('fund_assets');
        
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate', 'original_amount']);
        });

        Schema::table('equities', function (Blueprint $table) {
            $table->dropColumn('equity_type');
        });

        Schema::table('investment_funds', function (Blueprint $table) {
            $table->dropColumn(['distribution_frequency', 'currency']);
        });
    }
};
