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
}
