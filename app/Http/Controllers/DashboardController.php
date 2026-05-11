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
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBusinessValue',
            'totalPersonalCash',
            'recentTransactions',
            'businesses',
            'funds',
            'wallets'
        ));
    }
}
