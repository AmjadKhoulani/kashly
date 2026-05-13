<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\InvestmentFund;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_funds' => InvestmentFund::count(),
            'total_transactions' => Transaction::count(),
            'recent_users' => User::latest()->take(5)->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users', compact('users'));
    }
}
