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

        $wallets = Wallet::where('user_id', $user->id)->with('paymentMethods')->get();
        $funds = InvestmentFund::where('user_id', $user->id)->get();
        $businesses = Business::where('user_id', $user->id)->get();

        $totalByCurrency = [];
        foreach($wallets as $wallet) {
            $totalByCurrency[$wallet->currency] = ($totalByCurrency[$wallet->currency] ?? 0) + $wallet->balance;
        }
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

        // Calculate Estimated Personal Cash in USD (converted)
        $estimatedPersonalCashUSD = 0;
        $personalCashByCurrency = [];
        foreach($wallets as $wallet) {
            $estimatedPersonalCashUSD += $convertToUsd($wallet->balance, $wallet->currency);
            $personalCashByCurrency[$wallet->currency] = ($personalCashByCurrency[$wallet->currency] ?? 0) + $wallet->balance;
        }

        // Calculate Estimated Business Value in USD (converted)
        $estimatedBusinessValueUSD = 0;
        $estimatedBusinessOnlyUSD = 0;
        foreach($businesses as $business) {
            $valUsd = $convertToUsd($business->total_value, $business->currency ?? 'USD');
            $estimatedBusinessValueUSD += $valUsd;
            $estimatedBusinessOnlyUSD += $valUsd;
        }
        $estimatedFundsOnlyUSD = 0;
        foreach($funds as $fund) {
            $valUsd = $convertToUsd($fund->current_value, $fund->currency ?? 'USD');
            $estimatedBusinessValueUSD += $valUsd;
            $estimatedFundsOnlyUSD += $valUsd;
        }

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

        $upcomingDebtsMapped = $upcomingDebts->map(function ($entry) {
            return [
                'id' => $entry->id,
                'party_name' => $entry->party_name,
                'type' => $entry->type,
                'type_label' => $entry->type_label,
                'type_color' => $entry->type_color,
                'total_amount' => (float)$entry->total_amount,
                'paid_amount' => (float)$entry->paid_amount,
                'remaining_amount' => (float)$entry->remaining_amount,
                'currency' => $entry->currency,
                'due_date' => $entry->due_date ? $entry->due_date->format('Y-m-d') : null,
                'days_left' => $entry->due_date ? (int)now()->startOfDay()->diffInDays($entry->due_date->startOfDay(), false) : null,
                'description' => $entry->description,
                'status' => $entry->status,
                'status_label' => $entry->status_label,
            ];
        });

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with(['transactionable', 'categoryRelation', 'paymentMethod'])
            ->latest()
            ->take(10)
            ->get();

        // Chart Data (Last 7 Days)
        $chartData = [
            'days' => [],
            'commercial' => [],
            'personal' => []
        ];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartData['days'][] = $date->format('D'); // Mon, Tue, etc.

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

        return response()->json([
            'estimated_total_usd' => (float)$estimatedTotalUSD,
            'syp_rate' => (float)$sypRate,
            'total_by_currency' => (object)$totalByCurrency,
            'wallets' => $wallets,
            'businesses' => $businesses,
            'funds' => $funds,
            'recent_transactions' => $recentTransactions,
            'chart_data' => (object)$chartData,
            'estimated_personal_cash_usd' => (float)$estimatedPersonalCashUSD,
            'personal_cash_by_currency' => (object)$personalCashByCurrency,
            'estimated_business_value_usd' => (float)$estimatedBusinessValueUSD,
            'estimated_business_only_usd' => (float)$estimatedBusinessOnlyUSD,
            'estimated_funds_only_usd' => (float)$estimatedFundsOnlyUSD,
            'total_receivables_usd' => (float)$totalReceivablesUSD,
            'total_payables_usd' => (float)$totalPayablesUSD,
            'net_debts_usd' => (float)$netDebtsUSD,
            'upcoming_debts' => $upcomingDebtsMapped
        ]);
    }
}
