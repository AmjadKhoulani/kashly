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

        $transactions = $query->with(['transactionable', 'paymentMethod', 'categoryRelation'])
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

    public function update(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense,capital',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'transaction_date' => 'required|date',
            'payment_method_id' => 'nullable|exists:payment_methods,id',
        ]);

        \DB::transaction(function () use ($validated, $transaction, $request) {
            // --- 1. REVERT OLD TRANSACTION EFFECTS ---
            $oldType = $transaction->type;
            $modelClass = $transaction->transactionable_type;
            $model = $modelClass::find($transaction->transactionable_id);

            if ($model) {
                $valueField = ($modelClass === 'App\Models\Wallet') ? 'balance' : 
                             (($modelClass === 'App\Models\Business') ? 'total_value' : 'current_value');

                $targetAmount = $transaction->original_amount ?? $transaction->amount;
                if ($transaction->currency !== $model->currency) {
                    $targetAmount = $targetAmount * ($transaction->exchange_rate ?? 1.0);
                }

                if ($oldType === 'income' || $oldType === 'capital') {
                    $model->decrement($valueField, $targetAmount);
                } else {
                    $model->increment($valueField, $targetAmount);
                }
            }

            if ($transaction->payment_method_id) {
                $pm = $transaction->paymentMethod;
                if ($pm) {
                    $targetAmountPm = $transaction->original_amount ?? $transaction->amount;
                    if ($transaction->currency !== $pm->currency) {
                        $targetAmountPm = $targetAmountPm * ($transaction->exchange_rate ?? 1.0);
                    }
                    if ($oldType === 'income' || $oldType === 'capital') {
                        $pm->decrement('balance', $targetAmountPm);
                    } else {
                        $pm->increment('balance', $targetAmountPm);
                    }
                }
            }

            // --- 2. CALCULATE NEW VALUES ---
            $newAmount = $validated['amount'];
            $newType = $validated['type'];
            $currency = $transaction->currency;
            $rate = $transaction->exchange_rate ?? 1.0;

            // Get Category Name from category_id
            $cat = \App\Models\Category::find($validated['category_id']);
            $categoryName = $cat ? $cat->name : 'بدون تصنيف';

            // Determine target currency
            $targetCurrency = 'USD';
            if ($model) {
                $targetCurrency = $model->currency;
            }

            // If new payment method is selected
            $newPm = null;
            if ($request->filled('payment_method_id')) {
                $newPm = \App\Models\PaymentMethod::find($request->input('payment_method_id'));
                if ($newPm) {
                    $targetCurrency = $newPm->currency;
                }
            }

            if ($currency === $targetCurrency) {
                $targetAmount = $newAmount;
            } else {
                $targetAmount = $newAmount * $rate;
            }

            if ($currency === 'USD') {
                $finalAmount = $newAmount;
            } elseif ($targetCurrency === 'USD') {
                $finalAmount = $targetAmount;
            } else {
                $finalAmount = $targetAmount;
            }

            // --- 3. APPLY NEW EFFECTS ---
            if ($model) {
                $valueField = ($modelClass === 'App\Models\Wallet') ? 'balance' : 
                             (($modelClass === 'App\Models\Business') ? 'total_value' : 'current_value');

                if ($newType === 'income' || $newType === 'capital') {
                    $model->increment($valueField, $targetAmount);
                } else {
                    $model->decrement($valueField, $targetAmount);
                }
            }

            if ($request->filled('payment_method_id') && $newPm) {
                if ($newType === 'income' || $newType === 'capital') {
                    $newPm->increment('balance', $targetAmount);
                } else {
                    $newPm->decrement('balance', $targetAmount);
                }
            }

            // --- 4. UPDATE TRANSACTION RECORD ---
            $transaction->update([
                'amount' => $finalAmount,
                'original_amount' => $newAmount !== $finalAmount ? $newAmount : null,
                'type' => $newType,
                'category' => $categoryName,
                'category_id' => $validated['category_id'],
                'description' => $validated['description'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'payment_method_id' => $request->input('payment_method_id'),
            ]);
        });

        return response()->json([
            'status' => 'success',
            'transaction' => $transaction
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);

        \DB::transaction(function () use ($transaction) {
            $type = $transaction->type;
            $modelClass = $transaction->transactionable_type;
            $model = $modelClass::find($transaction->transactionable_id);

            // 1. Revert Wallet or Fund or Business balance
            if ($model) {
                $valueField = ($modelClass === 'App\Models\Wallet') ? 'balance' : 
                             (($modelClass === 'App\Models\Business') ? 'total_value' : 'current_value');

                $targetAmount = $transaction->original_amount ?? $transaction->amount;
                if ($transaction->currency !== $model->currency) {
                    $targetAmount = $targetAmount * ($transaction->exchange_rate ?? 1.0);
                }

                if ($type === 'income' || $type === 'capital') {
                    $model->decrement($valueField, $targetAmount);
                } else {
                    $model->increment($valueField, $targetAmount);
                }
            }

            // 2. Revert Payment Method balance
            if ($transaction->payment_method_id) {
                $pm = $transaction->paymentMethod;
                if ($pm) {
                    $targetAmountPm = $transaction->original_amount ?? $transaction->amount;
                    if ($transaction->currency !== $pm->currency) {
                        $targetAmountPm = $targetAmountPm * ($transaction->exchange_rate ?? 1.0);
                    }
                    if ($type === 'income' || $type === 'capital') {
                        $pm->decrement('balance', $targetAmountPm);
                    } else {
                        $pm->increment('balance', $targetAmountPm);
                    }
                }
            }

            // 3. Delete the transaction
            $transaction->delete();
        });

        return response()->json(['status' => 'success']);
    }
}
