<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::where('user_id', auth()->id())
            ->with('equities.equitable', 'linkedUser')
            ->get();

        return view('partners.index', compact('partners'));
    }

    public function linkAccount(Request $request, Partner $partner)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Check if user already exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Create a temporary user
            $user = User::create([
                'name' => $partner->name,
                'email' => $request->email,
                'password' => Hash::make(Str::random(12)),
                'role' => User::ROLE_PARTNER,
            ]);
        } else {
            // Update role if already exists
            $user->update(['role' => User::ROLE_PARTNER]);
        }

        $partner->update(['linked_user_id' => $user->id]);

        return back()->with('status', 'تم ربط الحساب بنجاح. يمكن للشريك الآن الدخول باستخدام بريده الإلكتروني.');
    }
}
