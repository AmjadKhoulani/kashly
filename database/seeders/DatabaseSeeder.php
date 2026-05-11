<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'أمجد خولاني',
            'email' => 'amjad@kashly.xyz',
            'password' => bcrypt('password'),
        ]);

        // Sample Partner
        $partner = \App\Models\Partner::create([
            'name' => 'ياسر القحطاني',
            'email' => 'yasser@example.com',
            'phone' => '0501234567',
            'notes' => 'شريك استراتيجي في القطاع العقاري',
        ]);

        // Sample Business
        $business = \App\Models\Business::create([
            'name' => 'متجر الإلكترونيات الحديثة',
            'type' => 'Retail',
            'total_value' => 50000.00,
            'user_id' => $user->id,
        ]);

        // Sample Investment Fund
        $fund = \App\Models\InvestmentFund::create([
            'name' => 'مجمع النور العقاري',
            'capital' => 1200000.00,
            'current_value' => 1250000.00,
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        // Sample Equity
        \App\Models\Equity::create([
            'partner_id' => $partner->id,
            'equitable_id' => $fund->id,
            'equitable_type' => \App\Models\InvestmentFund::class,
            'percentage' => 25.00,
            'amount' => 300000.00,
        ]);

        // Sample Wallet
        $wallet = \App\Models\Wallet::create([
            'name' => 'المحفظة الشخصية',
            'balance' => 12450.00,
            'user_id' => $user->id,
        ]);

        // Sample Transactions
        \App\Models\Transaction::create([
            'amount' => 1500.00,
            'type' => 'income',
            'category' => 'أرباح',
            'description' => 'أرباح مجمع النور - شهر مايو',
            'transactionable_id' => $fund->id,
            'transactionable_type' => \App\Models\InvestmentFund::class,
            'user_id' => $user->id,
            'transaction_date' => now(),
        ]);

        \App\Models\Transaction::create([
            'amount' => 45.00,
            'type' => 'expense',
            'category' => 'طعام',
            'description' => 'غداء عمل',
            'transactionable_id' => $wallet->id,
            'transactionable_type' => \App\Models\Wallet::class,
            'user_id' => $user->id,
            'transaction_date' => now(),
        ]);
    }
}
