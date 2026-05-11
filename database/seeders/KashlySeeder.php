<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Business;
use App\Models\Wallet;
use App\Models\Partner;
use App\Models\Transaction;
use App\Models\Debt;
use Illuminate\Support\Facades\Hash;

class KashlySeeder extends Seeder
{
    public function run(): void
    {
        // Create Test User
        $user = User::updateOrCreate(
            ['email' => 'test@kashly.com'],
            [
                'name' => 'Kashly Master',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'plan' => 'pro'
            ]
        );

        // Create Wallets
        $cashWallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Personal Cash',
            'type' => 'cash',
            'balance' => 5400.00,
            'is_personal' => true
        ]);

        $bankWallet = Wallet::create([
            'user_id' => $user->id,
            'name' => 'Main Bank Account',
            'type' => 'bank',
            'balance' => 125000.50,
            'is_personal' => true
        ]);

        // Create Business
        $business = Business::create([
            'owner_id' => $user->id,
            'name' => 'Nexus Investment Fund',
            'description' => 'Real estate and tech equity fund'
        ]);

        // Create Partners
        Partner::create([
            'business_id' => $business->id,
            'name' => 'Ahmed Ali',
            'equity_percentage' => 25.00,
            'initial_capital' => 50000.00
        ]);

        Partner::create([
            'business_id' => $business->id,
            'name' => 'Sarah Johnson',
            'equity_percentage' => 15.00,
            'initial_capital' => 30000.00
        ]);

        // Create Transactions
        Transaction::create([
            'wallet_id' => $bankWallet->id,
            'business_id' => $business->id,
            'amount' => 2400.00,
            'type' => 'income',
            'category' => 'Profit Sync',
            'description' => 'WHMCS Monthly Profit Sync',
            'transaction_date' => now()
        ]);

        Transaction::create([
            'wallet_id' => $cashWallet->id,
            'amount' => 15.50,
            'type' => 'expense',
            'category' => 'Coffee',
            'description' => 'Morning Starbucks',
            'transaction_date' => now()->subHours(2)
        ]);

        // Create Debts
        Debt::create([
            'user_id' => $user->id,
            'type' => 'debt',
            'contact_name' => 'Creative Agency',
            'amount' => 4120.00,
            'remaining_amount' => 4120.00,
            'due_date' => now()->addDays(15),
            'status' => 'pending'
        ]);
    }
}
