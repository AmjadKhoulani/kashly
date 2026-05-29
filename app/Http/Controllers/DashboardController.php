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
        $sypRate = \App\Services\ExchangeRateService::getSypRate();
        
        $convertToUsd = function ($amount, $currency) use ($user, $sypRate) {
            if ($currency === 'USD') {
                return $amount;
            } elseif ($currency === 'SYP') {
                // Real-time exchange rate from sp-today.com!
                return ($sypRate > 0) ? $amount / $sypRate : $amount;
            } else {
                // Find last transaction with this currency to get rate, fallback to 1 if not found
                $lastRate = Transaction::where('user_id', $user->id)
                    ->where('currency', $currency)
                    ->where('exchange_rate', '>', 1) // Only real rates
                    ->latest()
                    ->value('exchange_rate') ?? 1;

                return ($lastRate > 0) ? $amount / $lastRate : $amount;
            }
        };

        $estimatedTotalUSD = 0;
        foreach($totalByCurrency as $curr => $val) {
            $estimatedTotalUSD += $convertToUsd($val, $curr);
        }

        $totalPersonalCash = $wallets->sum('balance'); // Keep for legacy if needed, but we'll use breakdown
        $totalBusinessValue = $businesses->sum('total_value') + $funds->sum('current_value');

        // Fetch active ledger entries
        $ledgerEntries = \App\Models\LedgerEntry::where('user_id', $user->id)
            ->where('status', '!=', 'settled')
            ->get();

        $totalReceivablesUSD = 0;
        $totalPayablesUSD = 0;

        foreach ($ledgerEntries as $entry) {
            $rem = $entry->remaining_amount; // dynamic attribute: max(0, total_amount - paid_amount)
            $remUsd = $convertToUsd($rem, $entry->currency);
            if ($entry->type === 'receivable') {
                $totalReceivablesUSD += $remUsd;
            } else {
                // payable, installment, loan
                $totalPayablesUSD += $remUsd;
            }
        }

        $netDebtsUSD = $totalReceivablesUSD - $totalPayablesUSD;

        // Fetch top 3 upcoming unpaid/outstanding debts with due dates
        $upcomingDebts = \App\Models\LedgerEntry::where('user_id', $user->id)
            ->where('status', '!=', 'settled')
            ->whereNotNull('due_date')
            ->orderBy('due_date', 'asc')
            ->take(3)
            ->get();

        // Recent Transactions
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with(['transactionable', 'categoryRelation'])
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
            'chartData',
            'sypRate',
            'totalReceivablesUSD',
            'totalPayablesUSD',
            'netDebtsUSD',
            'upcomingDebts'
        ));
    }
}
