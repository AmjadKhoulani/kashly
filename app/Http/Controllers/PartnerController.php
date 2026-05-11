<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::whereHas('business', function($q) {
            $q->where('owner_id', Auth::id());
        })->with('business')->get();

        return view('partners.index', compact('partners'));
    }

    public function create()
    {
        $businesses = Business::where('owner_id', Auth::id())->get();
        return view('partners.create', compact('businesses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'name' => 'required|string|max:255',
            'equity_percentage' => 'required|numeric|min:0|max:100',
        ]);

        Partner::create($validated);

        return redirect()->route('partners.index')->with('success', 'Partner added successfully.');
    }
}
