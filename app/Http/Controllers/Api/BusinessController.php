<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $businesses = Business::where('user_id', $request->user()->id)->get();
        return response()->json($businesses);
    }

    public function show(Request $request, $id)
    {
        $business = Business::where('user_id', $request->user()->id)->findOrFail($id);

        $transactions = $business->transactions()
            ->with(['categoryRelation', 'paymentMethod'])
            ->latest('transaction_date')
            ->take(50)
            ->get();

        $totalIncome = $business->transactions()->where('type', 'income')->sum('amount');
        $totalExpense = $business->transactions()->where('type', 'expense')->sum('amount');
        $transactionsCount = $business->transactions()->count();

        // Set dynamic properties on the business object so they serialize at the root level of JSON
        $business->setAttribute('transactions', $transactions);
        $business->setAttribute('total_income', $totalIncome);
        $business->setAttribute('total_expense', $totalExpense);
        $business->setAttribute('transactions_count', $transactionsCount);
            
        return response()->json($business);
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_value' => 'required|numeric',
            'currency' => 'required|string|max:3',
        ]);

        $business = Business::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'total_value' => $request->total_value,
            'currency' => $request->currency,
        ]);

        return response()->json($business);
    }

    public function update(Request $request, $id)
    {
        $business = Business::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'total_value' => 'required|numeric',
            'currency' => 'required|string|max:3',
        ]);

        $business->update($request->only(['name', 'total_value', 'currency']));

        return response()->json($business);
    }

    public function destroy(Request $request, $id)
    {
        $business = Business::where('user_id', $request->user()->id)->findOrFail($id);
        $business->delete();

        return response()->json(['status' => 'success']);
    }
}
