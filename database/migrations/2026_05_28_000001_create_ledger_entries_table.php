<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // نوع الديون: مديني (receivable) / متدين أنا (payable) / تقسيط (installment) / قرض (loan)
            $table->enum('type', ['receivable', 'payable', 'installment', 'loan']);

            $table->string('party_name');           // اسم الشخص أو الجهة
            $table->string('party_phone')->nullable(); // رقم هاتف
            $table->text('description')->nullable(); // وصف / سبب الدين

            $table->decimal('total_amount', 14, 2); // المبلغ الإجمالي
            $table->decimal('paid_amount', 14, 2)->default(0); // المدفوع حتى الآن
            $table->string('currency', 3)->default('USD');

            // للتقسيط والقروض
            $table->integer('installment_count')->nullable();   // عدد الأقساط
            $table->decimal('installment_amount', 14, 2)->nullable(); // قيمة القسط الواحد
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();               // تاريخ استحقاق أو انتهاء

            $table->enum('status', ['active', 'settled', 'overdue', 'partial'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ledger_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_entry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('currency', 3)->default('USD');
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_payments');
        Schema::dropIfExists('ledger_entries');
    }
};
