<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentFund;
use App\Models\Equity;
use App\Models\Transaction;
use Illuminate\Http\Request;

class FundController extends Controller
{
    public function index(Request $request)
    {
        $funds = InvestmentFund::where('user_id', $request->user()->id)->get();
        return response()->json($funds);
    }

    public function show(Request $request, $id)
    {
        $fund = InvestmentFund::where('user_id', $request->user()->id)
            ->with(['assets', 'distributions'])
            ->findOrFail($id);

        $equities = Equity::where('equitable_id', $fund->id)
            ->where('equitable_type', InvestmentFund::class)
            ->with('partner')
            ->get();

        $transactions = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->with(['categoryRelation', 'paymentMethod'])
            ->latest('transaction_date')
            ->take(50)
            ->get();

        // Get root payment methods (those without a parent) with their children (currencies)
        $paymentMethods = \App\Models\PaymentMethod::where('fund_id', $fund->id)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $income = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->where('type', 'income')->sum('amount');
            
        $expense = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->where('type', 'expense')->sum('amount');

        $capitalSum = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->where('type', 'capital')->sum('amount');

        // Dynamic properties injected into the fund object so they are serialized nicely at root
        $fund->total_invested_capital = $fund->total_invested_capital;
        $fund->total_asset_value = $fund->assets->sum('value');
        $fund->capital_movements = $capitalSum;
        $fund->net_profit = $income - $expense;

        return response()->json([
            'fund' => $fund,
            'equities' => $equities,
            'recent_transactions' => $transactions,
            'payment_methods' => $paymentMethods
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capital' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'distribution_frequency' => 'required|string',
            'icon' => 'nullable|string',
            'status' => 'required|in:active,completed',
        ]);

        $fund = InvestmentFund::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'capital' => $validated['capital'],
            'current_value' => $validated['current_value'],
            'currency' => $validated['currency'],
            'distribution_frequency' => $validated['distribution_frequency'],
            'icon' => $validated['icon'] ?? '🏘️',
            'status' => $validated['status'],
        ]);

        return response()->json($fund);
    }

    public function update(Request $request, $id)
    {
        $fund = InvestmentFund::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capital' => 'required|numeric|min:0',
            'current_value' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'distribution_frequency' => 'required|string',
            'icon' => 'nullable|string',
            'status' => 'required|in:active,completed',
        ]);

        $fund->update($validated);

        return response()->json($fund);
    }

    public function destroy(Request $request, $id)
    {
        $fund = InvestmentFund::where('user_id', $request->user()->id)->findOrFail($id);
        $fund->delete();

        return response()->json(['status' => 'success']);
    }
}
