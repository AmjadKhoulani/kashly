<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_modules', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('icon');
            $table->string('description_ar')->nullable();
            $table->string('description_en')->nullable();
            $table->boolean('is_free')->default(true);
            $table->string('status')->default('active'); // active, beta, coming_soon
            $table->timestamps();
        });

        Schema::create('user_modules', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('module_id');
            $table->timestamp('activated_at')->useCurrent();
            $table->primary(['user_id', 'module_id']);
            $table->foreign('module_id')->references('id')->on('system_modules')->onDelete('cascade');
        });

        // Insert initial system modules
        DB::table('system_modules')->insert([
            [
                'id' => 'ledger',
                'name_ar' => 'منظومة الديون والمديونيات',
                'name_en' => 'Debts Ledger',
                'icon' => '🤝',
                'description_ar' => 'تتبع الديون والقروض الممنوحة والمستلمة، إدارة دفعات السداد وحساب الأرصدة المستحقة.',
                'description_en' => 'Track lent and borrowed debts, manage repayment installments, and track outstanding balances.',
                'is_free' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'investments',
                'name_ar' => 'منظومة الشركاء والاستثمارات',
                'name_en' => 'Investment Funds',
                'icon' => '💼',
                'description_ar' => 'إدارة صناديق الاستثمار، رؤوس أموال الشركاء، نسب الملكية، وتوزيع الأرباح الدورية.',
                'description_en' => 'Manage investment funds, partners capital, ownership percentages, and periodic dividend distributions.',
                'is_free' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'rentals',
                'name_ar' => 'منظومة العقارات والإيجارات',
                'name_en' => 'Real Estate & Rentals',
                'icon' => '🏢',
                'description_ar' => 'إدارة الأملاك والمباني، تتبع الوحدات الشاغرة والمؤجرة، إدارة عقود المستأجرين، وجدولة الدفعات المستحقة.',
                'description_en' => 'Manage properties and buildings, track vacant/rented units, manage lease contracts, and schedule rental dues.',
                'is_free' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('user_modules');
        Schema::dropIfExists('system_modules');
    }
};
