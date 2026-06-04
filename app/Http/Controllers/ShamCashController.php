<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class ShamCashController extends Controller
{
    private $baseUrl = 'https://shamcash-api.com/api/v1/shamcash';

    /**
     * Get a configured HTTP client with proxy options if set in .env
     */
    private function client()
    {
        $proxy = env('SHAMCASH_PROXY');
        if ($proxy) {
            return Http::withOptions(['proxy' => $proxy]);
        }
        return Http::asJson();
    }

    public function initiateLinking()
    {
        // This endpoint creates a new linking session on shamcash-api.com
        $response = $this->client()->post($this->baseUrl . '/link-sessions');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Failed to initiate linking session'], 500);
    }

    public function checkLinkStatus($sessionId)
    {
        $response = $this->client()->get($this->baseUrl . '/link-sessions/' . $sessionId);

        if ($response->successful()) {
            $data = $response->json();
            
            // If completed, save the token to the user
            if (isset($data['status']) && $data['status'] === 'completed' && isset($data['token'])) {
                auth()->user()->update([
                    'shamcash_token' => $data['token']
                ]);
            }

            return response()->json($data);
        }

        return response()->json(['error' => 'Failed to check linking status'], 500);
    }

    public function saveManualToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        auth()->user()->update([
            'shamcash_token' => $request->token
        ]);

        return back()->with('success', 'تم حفظ توكن ShamCash بنجاح');
    }

    public function getAccounts()
    {
        $token = auth()->user()->shamcash_token;
        if (!$token) {
            return response()->json(['error' => 'No ShamCash token found'], 401);
        }

        $response = $this->client()->withToken($token)
            ->get('https://api.shamcash-api.com/v1/accounts');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Failed to fetch accounts'], 500);
    }
}
