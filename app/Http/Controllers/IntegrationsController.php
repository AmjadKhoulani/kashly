<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Integration;
use App\Models\Business;
use App\Models\Wallet;
use App\Models\InvestmentFund;

class IntegrationsController extends Controller
{
    public function index()
    {
        $integrations = Integration::where('user_id', auth()->id())->get();
        $businesses = Business::where('user_id', auth()->id())->get();
        $wallets = Wallet::where('user_id', auth()->id())->get();
        $funds = InvestmentFund::where('user_id', auth()->id())->get();

        return view('integrations.index', compact('integrations', 'businesses', 'wallets', 'funds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'name' => 'required|string|max:255',
            'target_type' => 'required|string',
            'target_id' => 'required|integer',
        ]);

        $integration = new Integration();
        $integration->user_id = auth()->id();
        $integration->provider = $validated['provider'];
        $integration->name = $validated['name'];
        $integration->target_type = $validated['target_type'];
        $integration->target_id = $validated['target_id'];
        $integration->webhook_secret = bin2hex(random_bytes(16));
        $integration->is_active = true;
        $integration->save();

        return back()->with('success', 'تم إنشاء التكامل بنجاح');
    }
}
