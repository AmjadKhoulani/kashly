<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ledger_payments', function (Blueprint $table) {
            $table->string('type', 20)->default('payment')->after('ledger_entry_id'); // 'payment' or 'charge'
        });
    }

    public function down(): void
    {
        Schema::table('ledger_payments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
