<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Business;
use App\Models\InvestmentFund;
use App\Models\Wallet;
use App\Models\PaymentMethod;
use App\Models\Category;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with(['transactionable', 'paymentMethod', 'categoryRelation']);

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
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
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
            'type' => 'required|in:income,expense,capital',
            'amount' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'source_type' => 'required|string',
            'source_id' => 'required|integer',
            'transaction_date' => 'required|date',
            'currency' => 'nullable|string',
            'exchange_rate' => 'nullable|numeric',
            'invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        $currency = $request->filled('currency') ? $request->input('currency') : 'USD';
        $rate = $request->filled('exchange_rate') ? (float)$request->input('exchange_rate') : 1.0;
        if ($rate <= 0) {
            $rate = 1.0;
        }

        // Determine the target currency and calculate amounts
        $targetCurrency = 'USD';
        if ($validated['source_type'] === 'Wallet') {
            $wallet = Wallet::find($validated['source_id']);
            $targetCurrency = $wallet ? $wallet->currency : 'USD';
        } elseif ($validated['source_type'] === 'InvestmentFund') {
            $fund = InvestmentFund::find($validated['source_id']);
            $targetCurrency = $fund ? $fund->currency : 'USD';
        }

        // If a payment method is selected, its currency overrides the target currency
        if ($request->filled('payment_method_id')) {
            $pm = PaymentMethod::find($request->input('payment_method_id'));
            if ($pm) {
                $targetCurrency = $pm->currency;
            }
        }

        // Calculate Target Amount (the amount deducted/added to the local Wallet/Account)
        // If currencies match, targetAmount is the exact transaction amount.
        // If they differ, targetAmount = transactionAmount * exchange_rate.
        $transactionAmount = $validated['amount'];
        if ($currency === $targetCurrency) {
            $targetAmount = $transactionAmount;
        } else {
            $targetAmount = $transactionAmount * $rate;
        }

        // Calculate the USD equivalent (finalAmount) for the global transaction reports
        if ($currency === 'USD') {
            $finalAmount = $transactionAmount;
        } elseif ($targetCurrency === 'USD') {
            $finalAmount = $targetAmount;
        } else {
            // Fallback: If neither is USD, we assume targetAmount can be treated as USD, or convert if possible.
            $finalAmount = $targetAmount;
        }

        $invoicePath = null;
        if ($request->hasFile('invoice')) {
            $invoicePath = $request->file('invoice')->store('invoices', 'public');
        }

        // Handle category name from category_id if not explicitly provided
        $categoryName = $validated['category'] ?? null;
        if ($request->filled('category_id') && !$categoryName) {
            $cat = Category::find($request->input('category_id'));
            if ($cat) {
                $categoryName = $cat->name;
            }
        }

        // Create the Transaction
        $transaction = Transaction::create([
            'amount' => $finalAmount,
            'original_amount' => $transactionAmount !== $finalAmount ? $transactionAmount : null,
            'currency' => $currency,
            'exchange_rate' => $rate,
            'type' => $validated['type'],
            'category' => $categoryName,
            'category_id' => $validated['category_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'invoice_path' => $invoicePath,
            'payment_method_id' => $request->input('payment_method_id'),
            'transactionable_type' => "App\\Models\\" . $validated['source_type'],
            'transactionable_id' => $validated['source_id'],
            'user_id' => auth()->id(),
            'transaction_date' => $validated['transaction_date'] ?? now(),
        ]);

        // Update Wallet Balance if source is Wallet
        if ($validated['source_type'] === 'Wallet' && isset($wallet)) {
            if ($validated['type'] === 'income' || $validated['type'] === 'capital') {
                $wallet->increment('balance', $targetAmount);
            } else {
                $wallet->decrement('balance', $targetAmount);
            }
        }

        // Update Fund Balance if source is InvestmentFund
        if ($validated['source_type'] === 'InvestmentFund' && isset($fund)) {
            if ($validated['type'] === 'income' || $validated['type'] === 'capital') {
                $fund->increment('current_value', $targetAmount);
            } else {
                $fund->decrement('current_value', $targetAmount);
            }
        }

        // Update Payment Method Balance if selected
        if ($request->filled('payment_method_id') && isset($pm)) {
            if ($validated['type'] === 'income' || $validated['type'] === 'capital') {
                $pm->increment('balance', $targetAmount);
            } else {
                $pm->decrement('balance', $targetAmount);
            }
        }

        return back()->with('success', 'تم تسجيل العملية وتحديث الأرصدة بنجاح');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);
        
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required|in:income,expense,capital',
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

    public function transfer(Request $request)
    {
        $validated = $request->validate([
            'from_payment_method_id' => 'required|exists:payment_methods,id',
            'to_payment_method_id' => 'required|exists:payment_methods,id|different:from_payment_method_id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string',
            'source_type' => 'required|string',
            'source_id' => 'required|integer',
        ]);

        $fromAccount = PaymentMethod::findOrFail($validated['from_payment_method_id']);
        $toAccount = PaymentMethod::findOrFail($validated['to_payment_method_id']);
        $amount = $validated['amount'];

        \DB::transaction(function () use ($validated, $fromAccount, $toAccount, $amount) {
            $descSuffix = isset($validated['description']) && $validated['description'] ? ' - ' . $validated['description'] : '';
            
            // 1. Create Withdrawal Transaction
            Transaction::create([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'type' => 'expense',
                'category' => 'تحويل صادق',
                'description' => 'تحويل إلى ' . $toAccount->name . $descSuffix,
                'transaction_date' => $validated['transaction_date'],
                'payment_method_id' => $fromAccount->id,
                'transactionable_type' => "App\\Models\\" . $validated['source_type'],
                'transactionable_id' => $validated['source_id'],
                'currency' => $fromAccount->currency,
            ]);

            // 2. Create Deposit Transaction
            Transaction::create([
                'user_id' => auth()->id(),
                'amount' => $amount,
                'type' => 'income',
                'category' => 'تحويل وارد',
                'description' => 'تحويل من ' . $fromAccount->name . $descSuffix,
                'transaction_date' => $validated['transaction_date'],
                'payment_method_id' => $toAccount->id,
                'transactionable_type' => "App\\Models\\" . $validated['source_type'],
                'transactionable_id' => $validated['source_id'],
                'currency' => $toAccount->currency,
            ]);

            // 3. Update Balances
            $fromAccount->decrement('balance', $amount);
            $toAccount->increment('balance', $amount);
        });

        return back()->with('success', 'تم التحويل بين الحسابات بنجاح');
    }
}
