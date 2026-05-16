<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Integration;
use App\Models\Transaction;

class WebhookController extends Controller
{
    public function shopify(Request $request, $integrationId)
    {
        $integration = Integration::findOrFail($integrationId);
        
        // HMAC verification would go here in a production app
        
        $data = $request->all();
        $amount = $data['total_price'] ?? 0;
        
        if ($amount > 0) {
            Transaction::create([
                'amount' => $amount,
                'type' => 'income',
                'category' => 'Shopify Order',
                'description' => 'طلب رقم #' . ($data['order_number'] ?? 'N/A'),
                'transactionable_type' => $integration->target_type,
                'transactionable_id' => $integration->target_id,
                'user_id' => $integration->user_id,
                'transaction_date' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function whmcs(Request $request, $integrationId)
    {
        $integration = Integration::findOrFail($integrationId);
        
        $amount = $request->input('amount') ?? 0;
        
        if ($amount > 0) {
            Transaction::create([
                'amount' => $amount,
                'type' => 'income',
                'category' => 'WHMCS Payment',
                'description' => 'فاتورة رقم #' . ($request->input('invoiceid') ?? 'N/A'),
                'transactionable_type' => $integration->target_type,
                'transactionable_id' => $integration->target_id,
                'user_id' => $integration->user_id,
                'transaction_date' => now(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function madaaq(Request $request)
    {
        $key = $request->header('X-MadaaQ-Key');
        
        if (!$key) {
            return response()->json(['error' => 'Missing security key'], 401);
        }

        // Find the integration that matches this key
        $integration = Integration::where('provider', 'madaaq')
            ->where('webhook_secret', $key)
            ->first();

        if (!$integration) {
            return response()->json(['error' => 'Invalid security key'], 403);
        }

        // Validate incoming data
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            'subscriber' => 'required|string',
            'type' => 'required|string'
        ]);

        // Create the transaction
        Transaction::create([
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'type' => 'income',
            'category' => 'MadaaQ Payment',
            'description' => "دفعة من المشترك: {$validated['subscriber']} ({$validated['type']})",
            'transactionable_type' => $integration->target_type,
            'transactionable_id' => $integration->target_id,
            'user_id' => $integration->user_id,
            'transaction_date' => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Transaction recorded']);
    }
}
