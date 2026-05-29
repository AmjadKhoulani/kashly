<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function show(Request $request, $id)
    {
        $method = PaymentMethod::where('user_id', $request->user()->id)
            ->with(['wallet', 'fund', 'parent'])
            ->findOrFail($id);

        // Fetch transactions associated with this payment method
        $transactions = Transaction::where('payment_method_id', $method->id)
            ->with(['categoryRelation', 'paymentMethod'])
            ->latest('transaction_date')
            ->take(50)
            ->get();

        $totalIncome = Transaction::where('payment_method_id', $method->id)->where('type', 'income')->sum('amount');
        $totalExpense = Transaction::where('payment_method_id', $method->id)->where('type', 'expense')->sum('amount');

        // Set dynamic properties
        $method->setAttribute('transactions', $transactions);
        $method->setAttribute('total_income', $totalIncome);
        $method->setAttribute('total_expense', $totalExpense);

        return response()->json($method);
    }

    public function destroy(Request $request, $id)
    {
        $method = PaymentMethod::where('user_id', $request->user()->id)->findOrFail($id);
        $method->delete();

        return response()->json(['status' => 'success']);
    }
}
