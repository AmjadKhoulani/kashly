<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentFund;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Business;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $wallets = Wallet::where('user_id', $user->id)->get();
        $funds = InvestmentFund::where('user_id', $user->id)->get();
        $businesses = Business::where('user_id', $user->id)->get();

        $totalByCurrency = [];
        foreach($wallets as $wallet) {
            $totalByCurrency[$wallet->currency] = ($totalByCurrency[$wallet->currency] ?? 0) + $wallet->balance;
        }
        foreach($funds as $fund) {
            $totalByCurrency[$fund->currency] = ($totalByCurrency[$fund->currency] ?? 0) + $fund->current_value;
        }

        // Calculate Estimated Total in USD (simplified for API for now)
        $estimatedTotalUSD = 0;
        foreach($totalByCurrency as $curr => $val) {
            if ($curr === 'USD') {
                $estimatedTotalUSD += $val;
            } else {
                $lastRate = Transaction::where('user_id', $user->id)
                    ->where('currency', $curr)
                    ->where('exchange_rate', '>', 1)
                    ->latest()
                    ->value('exchange_rate') ?? 1;
                $estimatedTotalUSD += ($lastRate > 0) ? $val / $lastRate : $val;
            }
        }

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('transactionable')
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'estimated_total_usd' => $estimatedTotalUSD,
            'total_by_currency' => $totalByCurrency,
            'wallets' => $wallets,
            'funds' => $funds,
            'recent_transactions' => $recentTransactions
        ]);
    }
}
