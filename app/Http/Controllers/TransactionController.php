<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Business;
use App\Models\InvestmentFund;
use App\Models\Wallet;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with('transactionable')
            ->latest()
            ->paginate(20);

        $businesses = Business::where('user_id', auth()->id())->get();
        $funds = InvestmentFund::where('user_id', auth()->id())->get();
        $wallets = Wallet::where('user_id', auth()->id())->get();

        return view('transactions.index', compact('transactions', 'businesses', 'funds', 'wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'source_type' => 'required|string', // Business, Fund, or Wallet
            'source_id' => 'required|integer',
            'transaction_date' => 'required|date',
        ]);

        $transaction = new Transaction();
        $transaction->amount = $validated['amount'];
        $transaction->type = $validated['type'];
        $transaction->category = $validated['category'];
        $transaction->description = $validated['description'];
        $transaction->transactionable_type = "App\\Models\\" . $validated['source_type'];
        $transaction->transactionable_id = $validated['source_id'];
        $transaction->user_id = auth()->id();
        $transaction->transaction_date = $validated['transaction_date'];
        $transaction->save();

        return back()->with('success', 'تم تسجيل العملية بنجاح');
    }
}
