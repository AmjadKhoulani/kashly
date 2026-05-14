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

        // Commercial Stats
        $businesses = Business::where('user_id', $user->id)->get();
        $funds = InvestmentFund::where('user_id', $user->id)->get();
        $totalBusinessValue = $businesses->sum('total_value') + $funds->sum('current_value');

        // Personal Stats
        $wallets = Wallet::where('user_id', $user->id)->get();
        $totalPersonalCash = $wallets->sum('balance');

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
            'recentTransactions',
            'businesses',
            'funds',
            'wallets',
            'chartData'
        ));
    }
}
