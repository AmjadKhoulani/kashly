<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ledger_payments', function (Blueprint $table) {
            $table->decimal('original_amount', 14, 2)->nullable()->after('amount');
            $table->string('original_currency', 3)->nullable()->after('original_amount');
            $table->decimal('exchange_rate', 14, 4)->nullable()->after('original_currency');
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->decimal('original_amount', 14, 2)->nullable()->after('total_amount');
            $table->string('original_currency', 3)->nullable()->after('original_amount');
            $table->decimal('charge_exchange_rate', 14, 4)->nullable()->after('original_currency');
        });
    }

    public function down(): void
    {
        Schema::table('ledger_payments', function (Blueprint $table) {
            $table->dropColumn(['original_amount', 'original_currency', 'exchange_rate']);
        });
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropColumn(['original_amount', 'original_currency', 'charge_exchange_rate']);
        });
    }
};
