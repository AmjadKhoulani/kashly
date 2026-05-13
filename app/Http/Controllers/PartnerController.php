<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Partner;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::where('user_id', auth()->id())
            ->orWhereNull('user_id') // For global partners if any
            ->with('equities.equitable')
            ->get();

        return view('partners.index', compact('partners'));
    }
}
