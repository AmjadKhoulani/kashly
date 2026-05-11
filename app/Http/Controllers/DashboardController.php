<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Business;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Debt;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $netWorth = Wallet::where('user_id', $user->id)->sum('balance');
        $activeFunds = Business::where('owner_id', $user->id)->count();
        $totalDebts = Debt::where('user_id', $user->id)->where('type', 'debt')->sum('remaining_amount');
        
        $recentTransactions = Transaction::whereHas('wallet', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->latest()->take(5)->get();

        return view('dashboard', compact('netWorth', 'activeFunds', 'totalDebts', 'recentTransactions'));
    }
}
