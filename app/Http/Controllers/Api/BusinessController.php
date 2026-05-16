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
        $business = Business::where('user_id', $request->user()->id)
            ->with(['transactions' => function($q) {
                $q->latest('transaction_date')->take(20);
            }])
            ->findOrFail($id);
            
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
}
