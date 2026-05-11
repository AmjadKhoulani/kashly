<?php

namespace App\Http\Controllers;

use App\Models\InvestmentFund;
use App\Models\Partner;
use App\Models\Equity;
use App\Models\Transaction;
use Illuminate\Http\Request;

class InvestmentFundController extends Controller
{
    public function index()
    {
        $funds = InvestmentFund::where('user_id', auth()->id())->get();
        return view('funds.index', compact('funds'));
    }

    public function show($id)
    {
        $fund = InvestmentFund::where('user_id', auth()->id())->findOrFail($id);
        
        // Get Partners associated with this fund via Equities
        $equities = Equity::where('equitable_id', $fund->id)
            ->where('equitable_type', InvestmentFund::class)
            ->with('partner')
            ->get();

        $transactions = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->latest()
            ->get();

        return view('funds.show', compact('fund', 'equities', 'transactions'));
    }
}
