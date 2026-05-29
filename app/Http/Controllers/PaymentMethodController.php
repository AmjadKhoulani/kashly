<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\InvestmentFund;
use App\Models\Wallet;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::where('user_id', auth()->id())
            ->with(['fund', 'wallet'])
            ->get();
            
        $funds = InvestmentFund::where('user_id', auth()->id())->get();
        $wallets = Wallet::where('user_id', auth()->id())->get();
        
        return view('payment_methods.index', compact('methods', 'funds', 'wallets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'custodian_name' => 'nullable|string|max:255',
            'type' => 'required|string|in:bank,cash,credit_card,debit_card,other',
            'balance' => 'required|numeric',
            'currency' => 'required|string|size:3',
            'association_type' => 'required|string|in:fund,wallet',
            'fund_id' => 'nullable|required_if:association_type,fund|exists:investment_funds,id',
            'wallet_id' => 'nullable|required_if:association_type,wallet|exists:wallets,id',
            'parent_id' => 'nullable|exists:payment_methods,id',
        ]);

        PaymentMethod::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'custodian_name' => $validated['custodian_name'] ?? null,
            'type' => $validated['type'],
            'balance' => $validated['balance'],
            'currency' => $validated['currency'],
            'fund_id' => $validated['association_type'] === 'fund' ? $validated['fund_id'] : null,
            'wallet_id' => $validated['association_type'] === 'wallet' ? $validated['wallet_id'] : null,
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'تمت إضافة وسيلة الدفع بنجاح');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user_id !== auth()->id()) {
            abort(403);
        }
        $paymentMethod->delete();
        return back()->with('success', 'تم حذف وسيلة الدفع بنجاح');
    }
}
