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
            ->with(['transactions' => function($q) {
                $q->latest('transaction_date')->take(20);
            }])
            ->findOrFail($id);
            
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
}
