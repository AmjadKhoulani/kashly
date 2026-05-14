<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\InvestmentFund;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Multi-currency stats
        $wallets = Wallet::where('user_id', $user->id)->get();
        $funds = InvestmentFund::where('user_id', $user->id)->get();
        $businesses = Business::where('user_id', $user->id)->get();

        $totalByCurrency = [];
        
        // Sum Wallets
        foreach($wallets as $wallet) {
            $totalByCurrency[$wallet->currency] = ($totalByCurrency[$wallet->currency] ?? 0) + $wallet->balance;
        }

        // Sum Funds
        foreach($funds as $fund) {
            $totalByCurrency[$fund->currency] = ($totalByCurrency[$fund->currency] ?? 0) + $fund->current_value;
        }

        // Calculate Estimated Total in USD
        $estimatedTotalUSD = 0;
        foreach($totalByCurrency as $curr => $val) {
            if ($curr === 'USD') {
                $estimatedTotalUSD += $val;
            } else {
                // Find last transaction with this currency to get rate, fallback to 1 if not found
                $lastRate = Transaction::where('user_id', $user->id)
                    ->where('currency', $curr)
                    ->where('exchange_rate', '>', 1) // Only real rates
                    ->latest()
                    ->value('exchange_rate') ?? 1;

                $estimatedTotalUSD += ($lastRate > 0) ? $val / $lastRate : $val;
            }
        }

        $totalPersonalCash = $wallets->sum('balance'); // Keep for legacy if needed, but we'll use breakdown
        $totalBusinessValue = $businesses->sum('total_value') + $funds->sum('current_value');

        // Recent Transactions
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('transactionable')
            ->latest()
            ->take(5)
            ->get();

        // Chart Data (Last 7 Days)
        $chartData = [
            'days' => [],
            'commercial' => [],
            'personal' => []
        ];

        $dayNames = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData['days'][] = $dayNames[$date->dayOfWeek];

            $commercial = Transaction::where('user_id', $user->id)
                ->whereIn('transactionable_type', ['App\Models\Business', 'App\Models\InvestmentFund'])
                ->whereDate('transaction_date', $date)
                ->sum('amount');
            
            $personal = Transaction::where('user_id', $user->id)
                ->where('transactionable_type', 'App\Models\Wallet')
                ->whereDate('transaction_date', $date)
                ->sum('amount');

            $chartData['commercial'][] = (float)$commercial;
            $chartData['personal'][] = (float)$personal;
        }

        return view('dashboard', compact(
            'totalBusinessValue',
            'totalPersonalCash',
            'estimatedTotalUSD',
            'totalByCurrency',
            'recentTransactions',
            'businesses',
            'funds',
            'wallets',
            'chartData'
        ));
    }
}
