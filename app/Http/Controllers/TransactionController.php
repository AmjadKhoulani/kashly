<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Business;
use App\Models\InvestmentFund;
use App\Models\Wallet;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with(['transactionable', 'paymentMethod']);

        // Advanced Filters
        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('source_type')) {
            $query->where('transactionable_type', 'App\\Models\\' . $request->source_type);
        }
        if ($request->filled('source_id')) {
            $query->where('transactionable_id', $request->source_id);
        }
        if ($request->filled('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        $transactions = $query->latest('transaction_date')->paginate(50)->withQueryString();

        $businesses = Business::where('user_id', auth()->id())->get();
        $funds = InvestmentFund::where('user_id', auth()->id())->get();
        $wallets = Wallet::where('user_id', auth()->id())->get();
        $paymentMethods = PaymentMethod::where('user_id', auth()->id())->get();

        return view('transactions.index', compact('transactions', 'businesses', 'funds', 'wallets', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'source_type' => 'required|string',
            'source_id' => 'required|integer',
            'transaction_date' => 'required|date',
            'currency' => 'nullable|string',
            'exchange_rate' => 'nullable|numeric',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        $finalAmount = $validated['amount'];
        $originalAmount = null;
        $currency = $request->filled('currency') ? $request->input('currency') : 'USD';
        $rate = $request->filled('exchange_rate') ? $request->input('exchange_rate') : 1;

        if ($currency !== 'USD') {
            $originalAmount = $validated['amount'];
            // Safeguard against division by zero
            $finalAmount = $rate > 0 ? $originalAmount / $rate : $originalAmount;
        }

        $invoicePath = null;
        if ($request->hasFile('invoice')) {
            $invoicePath = $request->file('invoice')->store('invoices', 'public');
        }

        $transaction = Transaction::create([
            'amount' => $finalAmount,
            'original_amount' => $originalAmount,
            'currency' => $currency,
            'exchange_rate' => $rate,
            'type' => $validated['type'],
            'category' => $validated['category'],
            'description' => $validated['description'],
            'invoice_path' => $invoicePath,
            'payment_method_id' => $request->input('payment_method_id'),
            'transactionable_type' => "App\\Models\\" . $validated['source_type'],
            'transactionable_id' => $validated['source_id'],
            'user_id' => auth()->id(),
            'transaction_date' => $validated['transaction_date'],
        ]);

        // 1. Update InvestmentFund current_value if applicable
        if ($validated['source_type'] === 'InvestmentFund') {
            $fund = InvestmentFund::find($validated['source_id']);
            if ($fund) {
                if ($validated['type'] === 'income') {
                    $fund->increment('current_value', $finalAmount);
                } else {
                    $fund->decrement('current_value', $finalAmount);
                }
            }
        }

        // 2. Update Wallet balance if applicable
        if ($validated['source_type'] === 'Wallet') {
            $wallet = Wallet::find($validated['source_id']);
            if ($wallet) {
                if ($validated['type'] === 'income') {
                    $wallet->increment('balance', $finalAmount);
                } else {
                    $wallet->decrement('balance', $finalAmount);
                }
            }
        }

        // 3. Update Payment Method balance if selected
        if ($request->filled('payment_method_id')) {
            $pm = PaymentMethod::find($request->input('payment_method_id'));
            if ($pm) {
                if ($validated['type'] === 'income') {
                    $pm->increment('balance', $finalAmount);
                } else {
                    $pm->decrement('balance', $finalAmount);
                }
            }
        }

        return back()->with('success', 'تم تسجيل العملية وتحديث الأرصدة بنجاح');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense',
            'category' => 'required|string',
            'transaction_date' => 'required|date',
        ]);

        $transaction->update($validated);

        return back()->with('success', 'تم تحديث العملية بنجاح');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();
        return back()->with('success', 'تم حذف العملية');
    }
}
