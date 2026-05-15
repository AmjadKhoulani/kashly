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
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'fund' => $fund,
            'equities' => $equities,
            'recent_transactions' => $transactions
        ]);
    }
}
