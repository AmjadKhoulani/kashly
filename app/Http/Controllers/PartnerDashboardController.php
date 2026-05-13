<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\Equity;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        $partner = Partner::where('linked_user_id', Auth::id())->first();

        if (!$partner) {
            return view('partner.no_link');
        }

        $equities = Equity::where('partner_id', $partner->id)->with('equitable')->get();
        
        $stats = [
            'total_equity_value' => $equities->sum('amount'),
            'fund_count' => $equities->count(),
            'recent_transactions' => Transaction::whereIn('transactionable_id', $equities->pluck('equitable_id'))
                ->whereIn('transactionable_type', $equities->pluck('equitable_type'))
                ->latest()
                ->take(10)
                ->get(),
        ];

        return view('partner.dashboard', compact('partner', 'equities', 'stats'));
    }
}
