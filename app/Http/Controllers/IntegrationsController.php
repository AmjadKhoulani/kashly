<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Integration;
use App\Models\Business;
use App\Models\Wallet;

class IntegrationsController extends Controller
{
    public function index()
    {
        $integrations = Integration::where('user_id', auth()->id())->get();
        $businesses = Business::where('user_id', auth()->id())->get();
        $wallets = Wallet::where('user_id', auth()->id())->get();

        return view('integrations.index', compact('integrations', 'businesses', 'wallets'));
    }
}
