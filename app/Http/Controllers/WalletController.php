<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::where('user_id', Auth::id())
            ->withSum('transactions', 'amount')
            ->latest()
            ->get();

        return view('wallets.index', compact('wallets'));
    }

    public function create()
    {
        return view('wallets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:savings,current,cash,investment',
            'balance' => 'required|numeric',
        ]);

        $wallet = Wallet::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'balance' => $validated['balance'],
        ]);

        return redirect()->route('wallets.index')->with('success', 'Wallet created successfully.');
    }

    public function show(Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) {
            abort(403);
        }

        $wallet->load(['transactions' => function($q) {
            $q->latest('transaction_date');
        }]);
        
        return view('wallets.show', compact('wallet'));
    }
}
