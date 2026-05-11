<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::where('owner_id', Auth::id())
            ->withCount('partners')
            ->withSum('transactions', 'amount')
            ->latest()
            ->get();

        return view('businesses.index', compact('businesses'));
    }

    public function create()
    {
        return view('businesses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $business = Business::create([
            'owner_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('businesses.index')->with('success', 'Investment Fund created successfully.');
    }

    public function show(Business $business)
    {
        if ($business->owner_id !== Auth::id()) {
            abort(403);
        }

        $business->load(['partners', 'transactions.wallet']);
        
        return view('businesses.show', compact('business'));
    }
}
