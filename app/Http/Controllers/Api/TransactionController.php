<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', $request->user()->id);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $transactions = $query->with(['transactionable', 'paymentMethod', 'category'])
            ->latest('transaction_date')
            ->paginate(30);

        return response()->json($transactions);
    }

    public function categories(Request $request)
    {
        $categories = Transaction::where('user_id', $request->user()->id)
            ->distinct()
            ->pluck('category');
            
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:income,expense,capital',
            'category_id' => 'required|exists:categories,id',
            'transaction_date' => 'required|date',
            'transactionable_id' => 'required',
            'transactionable_type' => 'required|string',
            'description' => 'nullable|string',
            'payment_method_id' => 'nullable|exists:payment_methods,id'
        ]);

        $validated['user_id'] = $request->user()->id;

        $transaction = Transaction::create($validated);

        // Update current value/balance of the fund/wallet/business
        $modelClass = $transaction->transactionable_type;
        $model = $modelClass::find($transaction->transactionable_id);
        
        if ($model) {
            $valueField = ($modelClass === 'App\Models\Wallet') ? 'balance' : 
                         (($modelClass === 'App\Models\Business') ? 'total_value' : 'current_value');
            
            if ($transaction->type === 'income' || $transaction->type === 'capital') {
                $model->increment($valueField, $transaction->amount);
            } else {
                $model->decrement($valueField, $transaction->amount);
            }
        }

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction
        ]);
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

        $fromAccount = \App\Models\PaymentMethod::findOrFail($validated['from_payment_method_id']);
        $toAccount = \App\Models\PaymentMethod::findOrFail($validated['to_payment_method_id']);
        $amount = $validated['amount'];

        \DB::transaction(function () use ($validated, $fromAccount, $toAccount, $amount, $request) {
            // 1. Create Withdrawal Transaction
            Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $amount,
                'type' => 'expense',
                'category' => 'تحويل صادق',
                'description' => 'تحويل إلى ' . $toAccount->name . ($validated['description'] ? ' - ' . $validated['description'] : ''),
                'transaction_date' => $validated['transaction_date'],
                'payment_method_id' => $fromAccount->id,
                'transactionable_type' => "App\\Models\\" . $validated['source_type'],
                'transactionable_id' => $validated['source_id'],
                'currency' => $fromAccount->currency,
            ]);

            // 2. Create Deposit Transaction
            Transaction::create([
                'user_id' => $request->user()->id,
                'amount' => $amount,
                'type' => 'income',
                'category' => 'تحويل وارد',
                'description' => 'تحويل من ' . $fromAccount->name . ($validated['description'] ? ' - ' . $validated['description'] : ''),
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

        return response()->json(['status' => 'success']);
    }
}
