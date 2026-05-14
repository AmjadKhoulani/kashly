<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $methods = PaymentMethod::where('user_id', auth()->id())->get();
        return view('payment_methods.index', compact('methods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:bank,cash,credit_card,debit_card,other',
            'balance' => 'required|numeric',
            'currency' => 'required|string|size:3',
        ]);

        PaymentMethod::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'balance' => $validated['balance'],
            'currency' => $validated['currency'],
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
