<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integration;
use App\Models\Transaction;

class WebhookController extends Controller
{
    /**
     * Helper to process transaction, convert currencies, and update balances.
     */
    private function processTransaction($integration, $amount, $currency, $category, $description)
    {
        $target = $integration->target;
        if (!$target) {
            return;
        }

        $targetCurrency = $target->currency ?? 'USD';
        
        // Calculate exchange rate and target amount
        $rate = 1.0;
        $targetAmount = $amount;

        if ($currency !== $targetCurrency) {
            if ($currency === 'SYP' && $targetCurrency === 'USD') {
                $sypRate = \App\Services\ExchangeRateService::getSypRate();
                $rate = $sypRate > 0 ? 1 / $sypRate : 1.0;
                $targetAmount = $amount * $rate;
            } elseif ($currency === 'USD' && $targetCurrency === 'SYP') {
                $sypRate = \App\Services\ExchangeRateService::getSypRate();
                $rate = $sypRate;
                $targetAmount = $amount * $rate;
            } else {
                // Fallback to query last transaction rate or 1.0
                $lastRate = Transaction::where('user_id', $integration->user_id)
                    ->where('currency', $currency)
                    ->where('exchange_rate', '>', 0)
                    ->latest()
                    ->value('exchange_rate') ?? 1.0;
                $rate = $lastRate;
                $targetAmount = $amount * $rate;
            }
        }

        // Determine final amount stored in Transaction (consistent with TransactionController)
        $finalAmount = $targetAmount;
        $originalAmount = null;
        if ($currency !== $targetCurrency) {
            $originalAmount = $amount;
        }

        // Create the transaction
        Transaction::create([
            'amount' => $finalAmount,
            'original_amount' => $originalAmount,
            'currency' => $currency,
            'exchange_rate' => $rate,
            'type' => 'income',
            'category' => $category,
            'description' => $description,
            'transactionable_type' => $integration->target_type,
            'transactionable_id' => $integration->target_id,
            'user_id' => $integration->user_id,
            'transaction_date' => now(),
        ]);

        // Increment the target balance/current value/total value
        if ($integration->target_type === 'App\Models\Wallet') {
            $target->increment('balance', $targetAmount);
        } elseif ($integration->target_type === 'App\Models\InvestmentFund') {
            $target->increment('current_value', $targetAmount);
        } elseif ($integration->target_type === 'App\Models\Business') {
            $target->increment('total_value', $targetAmount);
        }
    }

    public function shopify(Request $request, $integrationId)
    {
        $integration = Integration::findOrFail($integrationId);
        
        // HMAC verification would go here in a production app
        
        $data = $request->all();
        $amount = $data['total_price'] ?? 0;
        
        if ($amount > 0) {
            $currency = $data['currency'] ?? 'USD';
            $orderNumber = $data['order_number'] ?? 'N/A';
            $this->processTransaction(
                $integration,
                $amount,
                $currency,
                'Shopify Order',
                'طلب رقم #' . $orderNumber
            );
        }

        return response()->json(['status' => 'success']);
    }

    public function whmcs(Request $request, $integrationId)
    {
        $integration = Integration::findOrFail($integrationId);
        
        $amount = $request->input('amount') ?? 0;
        
        if ($amount > 0) {
            $currency = $request->input('currency') ?? 'USD';
            $invoiceId = $request->input('invoiceid') ?? 'N/A';
            $this->processTransaction(
                $integration,
                $amount,
                $currency,
                'WHMCS Payment',
                'فاتورة رقم #' . $invoiceId
            );
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
            'type' => 'required|string',
            'action' => 'nullable|string'
        ]);

        $action = strtolower($validated['action'] ?? 'create');

        // Check if this is a deletion/cancellation request
        if ($action === 'delete' || strtolower($validated['type']) === 'delete' || strtolower($validated['type']) === 'cancelled') {
            // Find the transaction to delete
            $query = Transaction::where('user_id', $integration->user_id)
                ->where('transactionable_type', $integration->target_type)
                ->where('transactionable_id', $integration->target_id);

            if (strtolower($validated['type']) === 'delete' || strtolower($validated['type']) === 'cancelled') {
                $query->where('description', 'like', "دفعة من المشترك: {$validated['subscriber']}%");
            } else {
                $description = "دفعة من المشترك: {$validated['subscriber']} ({$validated['type']})";
                $query->where('description', $description);
            }

            $transaction = $query->latest()->first();

            if ($transaction) {
                \DB::transaction(function () use ($transaction, $integration) {
                    // Revert the balance of the target
                    $target = $integration->target;
                    if ($target) {
                        $targetAmount = $transaction->amount; // Converted amount stored in DB
                        if ($integration->target_type === 'App\Models\Wallet') {
                            $target->decrement('balance', $targetAmount);
                        } elseif ($integration->target_type === 'App\Models\InvestmentFund') {
                            $target->decrement('current_value', $targetAmount);
                        } elseif ($integration->target_type === 'App\Models\Business') {
                            $target->decrement('total_value', $targetAmount);
                        }
                    }
                    $transaction->delete();
                });

                return response()->json(['status' => 'success', 'message' => 'Transaction deleted and balances reverted']);
            }

            return response()->json(['error' => 'Transaction not found for deletion'], 404);
        }

        // Process the transaction and update the target's balance with currency rate support
        $this->processTransaction(
            $integration,
            $validated['amount'],
            $validated['currency'],
            'MadaaQ Payment',
            "دفعة من المشترك: {$validated['subscriber']} ({$validated['type']})"
        );

        return response()->json(['status' => 'success', 'message' => 'Transaction recorded']);
    }
}

