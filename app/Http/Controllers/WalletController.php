<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::where('user_id', auth()->id())->get();
        return view('wallets.index', compact('wallets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'custodian_name' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        Wallet::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'custodian_name' => $request->custodian_name,
            'balance' => $request->balance,
            'currency' => $request->currency,
        ]);

        return back()->with('success', 'تم إنشاء المحفظة الشخصية بنجاح');
    }

    public function show($id)
    {
        $wallet = Wallet::where('user_id', auth()->id())->findOrFail($id);
        $transactions = Transaction::where('transactionable_id', $wallet->id)
            ->where('transactionable_type', Wallet::class)
            ->latest()
            ->paginate(20);

        return view('wallets.show', compact('wallet', 'transactions'));
    }

    public function reconcile(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', auth()->id())->findOrFail($id);
        $request->validate(['actual_balance' => 'required|numeric|min:0']);

        $difference = $request->actual_balance - $wallet->balance;

        if ($difference == 0) {
            return back()->with('success', 'الرصيد مطابق تماماً، لا حاجة للتعديل.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $difference, $request) {
            // Create Adjustment Transaction
            Transaction::create([
                'user_id' => auth()->id(),
                'amount' => abs($difference),
                'type' => $difference > 0 ? 'income' : 'expense',
                'category' => 'تسوية رصيد',
                'description' => 'مطابقة رصيد يدوية - المبلغ الحقيقي: ' . $request->actual_balance,
                'transactionable_id' => $wallet->id,
                'transactionable_type' => Wallet::class,
                'transaction_date' => now(),
                'currency' => $wallet->currency,
            ]);

            // Update Wallet Balance
            $wallet->update(['balance' => $request->actual_balance]);
        });

        return back()->with('success', 'تمت مطابقة الرصيد بنجاح وتسجيل عملية تسوية بالفرق.');
    }

    public function destroy(Wallet $wallet)
    {
        if ($wallet->user_id !== auth()->id()) abort(403);
        $wallet->delete();
        return redirect()->route('wallets.index')->with('success', 'تم حذف المحفظة بنجاح');
    }
}
