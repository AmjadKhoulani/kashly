<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Partner;
use App\Models\User;
use App\Mail\PartnerInvitationMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::where('user_id', auth()->id())
            ->where(function($query) {
                $query->whereNull('linked_user_id')
                      ->orWhere('linked_user_id', '!=', auth()->id());
            })
            ->with('equities.equitable', 'linkedUser')
            ->get();

        return view('partners.index', compact('partners'));
    }

    public function linkAccount(Request $request, Partner $partner)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $tempPassword = Str::random(10);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Create a new user as partner
            $user = User::create([
                'name' => $partner->name,
                'email' => $request->email,
                'password' => Hash::make($tempPassword),
                'role' => User::ROLE_PARTNER,
            ]);
            
            // Send invitation email with credentials
            try {
                Mail::to($request->email)->send(new PartnerInvitationMail(
                    $partner->name, 
                    auth()->user()->name, 
                    $request->email, 
                    $tempPassword
                ));
            } catch (\Exception $e) {
                // Log error but continue
                \Log::error("Failed to send partner invitation email: " . $e->getMessage());
            }

        } else {
            // User exists, just update role and link
            $user->update(['role' => User::ROLE_PARTNER]);
        }

        $partner->update(['linked_user_id' => $user->id]);

        return back()->with('status', 'تم ربط الحساب بنجاح وإرسال بيانات الدخول إلى بريد الشريك.');
    }
}
