<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = Wallet::where('user_id', $request->user()->id)->get();
        return response()->json($wallets);
    }

    public function show(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)
            ->with(['paymentMethods'])
            ->findOrFail($id);

        $transactions = $wallet->transactions()
            ->with(['categoryRelation', 'paymentMethod'])
            ->latest('transaction_date')
            ->take(50)
            ->get();

        $totalIncome = $wallet->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $wallet->transactions()->where('type', 'expense')->sum('amount');
        $transactionsCount = $wallet->transactions()->count();

        // Get the current SYP exchange rate from settings
        $sypRate = 0;
        $setting = \DB::table('settings')->where('key', 'syp_exchange_rate')->first();
        if ($setting) {
            $sypRate = floatval($setting->value);
        }

        // Set dynamic properties on the wallet object so they serialize at the root level of JSON
        $wallet->setAttribute('transactions', $transactions);
        $wallet->setAttribute('total_income', $totalIncome);
        $wallet->setAttribute('total_expense', $totalExpense);
        $wallet->setAttribute('transactions_count', $transactionsCount);
        $wallet->setAttribute('syp_rate', $sypRate);
            
        return response()->json($wallet);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'custodian_name' => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'balance' => $request->balance,
            'currency' => $request->currency,
            'custodian_name' => $request->custodian_name,
        ]);

        return response()->json($wallet);
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'required|numeric',
            'currency' => 'required|string|max:3',
            'custodian_name' => 'nullable|string|max:255',
        ]);

        $wallet->update($request->only(['name', 'balance', 'currency', 'custodian_name']));

        return response()->json($wallet);
    }

    public function destroy(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)->findOrFail($id);
        $wallet->delete();

        return response()->json(['status' => 'success']);
    }

    public function reconcile(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)->findOrFail($id);
        $request->validate(['actual_balance' => 'required|numeric|min:0']);

        $difference = $request->actual_balance - $wallet->balance;

        if ($difference != 0) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $difference, $request) {
                \App\Models\Transaction::create([
                    'user_id' => $request->user()->id,
                    'amount' => abs($difference),
                    'type' => $difference > 0 ? 'income' : 'expense',
                    'category' => 'تسوية رصيد',
                    'description' => 'مطابقة رصيد يدوية - الرصيد الحقيقي: ' . $request->actual_balance,
                    'transactionable_id' => $wallet->id,
                    'transactionable_type' => Wallet::class,
                    'transaction_date' => now(),
                    'currency' => $wallet->currency,
                ]);

                $wallet->update(['balance' => $request->actual_balance]);
            });
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تمت مطابقة الرصيد بنجاح وتسجيل عملية تسوية بالفرق.',
            'wallet' => $wallet
        ]);
    }
}
