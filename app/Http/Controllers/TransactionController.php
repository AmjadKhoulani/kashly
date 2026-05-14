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
    public function index()
    {
        $transactions = Transaction::where('user_id', auth()->id())
            ->with(['transactionable', 'paymentMethod'])
            ->latest()
            ->paginate(20);

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
        $currency = $request->input('currency', 'USD');
        $rate = $request->input('exchange_rate', 1);

        if ($currency !== 'USD') {
            $originalAmount = $validated['amount'];
            $finalAmount = $originalAmount / $rate;
        }

        $invoicePath = null;
        if ($request->hasFile('invoice')) {
            $invoicePath = $request->file('invoice')->store('invoices', 'public');
        }

        Transaction::create([
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

        return back()->with('success', 'تم تسجيل العملية بنجاح');
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
