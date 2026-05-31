<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Integration;
use App\Models\Business;
use App\Models\Wallet;
use App\Models\InvestmentFund;
use App\Models\Transaction;

class IntegrationsController extends Controller
{
    public function index()
    {
        $integrations = Integration::where('user_id', auth()->id())->get();
        $businesses = Business::where('user_id', auth()->id())->get();
        $wallets = Wallet::where('user_id', auth()->id())->get();
        $funds = InvestmentFund::where('user_id', auth()->id())->get();

        return view('integrations.index', compact('integrations', 'businesses', 'wallets', 'funds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider' => 'required|string',
            'name' => 'required|string|max:255',
            'target_type' => 'required|string',
            'target_id' => 'required|integer',
            'webhook_secret' => 'nullable|string|max:255',
        ]);

        $integration = new Integration();
        $integration->user_id = auth()->id();
        $integration->provider = $validated['provider'];
        $integration->name = $validated['name'];
        $integration->target_type = $validated['target_type'];
        $integration->target_id = $validated['target_id'];
        $integration->webhook_secret = $validated['webhook_secret'] ?? bin2hex(random_bytes(16));
        $integration->is_active = true;
        $integration->save();

        return back()->with('success', 'تم إنشاء التكامل بنجاح');
    }

    public function destroy($id)
    {
        $integration = Integration::where('user_id', auth()->id())
            ->where('id', $id)
            ->firstOrFail();
            
        $integration->delete();

        return back()->with('success', 'تم إلغاء تفعيل التكامل بنجاح');
    }

    /**
     * Fetch all historical payments from MadaaQ and import them safely.
     */
    public function syncMadaaq($id)
    {
        $integration = Integration::where('user_id', auth()->id())
            ->where('provider', 'madaaq')
            ->where('id', $id)
            ->firstOrFail();

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json',
                'X-MadaaQ-Key' => $integration->webhook_secret
            ])->timeout(12)->get('https://www.madaaq.com/api/payments');

            if (!$response->successful()) {
                return back()->with('error', 'فشل الاتصال بمنصة MadaaQ: ' . ($response->json()['message'] ?? 'خطأ غير معروف'));
            }

            $data = $response->json();
            $payments = $data['payments'] ?? [];
            $importedCount = 0;
            $skippedCount = 0;

            foreach ($payments as $payment) {
                $description = "دفعة من المشترك: {$payment['subscriber']} ({$payment['type']})";
                
                // Determine transaction date
                $txDate = isset($payment['transaction_date']) 
                    ? \Carbon\Carbon::parse($payment['transaction_date']) 
                    : now();

                // Bulletproof duplicate check: check same target, same day, same absolute amount, and matching subscriber name
                $exists = Transaction::where('user_id', $integration->user_id)
                    ->where('transactionable_type', $integration->target_type)
                    ->where('transactionable_id', $integration->target_id)
                    ->whereDate('transaction_date', $txDate->toDateString())
                    ->where(function($q) use ($payment) {
                        $absAmount = abs($payment['amount']);
                        $q->where('amount', $absAmount)
                          ->orWhere('original_amount', $absAmount)
                          ->orWhere('amount', -$absAmount)
                          ->orWhere('original_amount', -$absAmount);
                    })
                    ->where('description', 'like', "%" . $payment['subscriber'] . "%")
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                // Call helper processing transaction
                $this->processWebhookTransaction(
                    $integration,
                    $payment['amount'],
                    $payment['currency'] ?? 'SYP',
                    'MadaaQ Payment',
                    $description,
                    $txDate
                );

                $importedCount++;
            }

            return back()->with('success', "تمت مزامنة العمليات بنجاح! تم استيراد {$importedCount} حركات جديدة، وتخطي {$skippedCount} حركات مكررة.");
            
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء الاتصال بمنصة MadaaQ: ' . $e->getMessage());
        }
    }

    /**
     * Helper to process transaction, convert currencies, and update balances.
     */
    private function processWebhookTransaction($integration, $amount, $currency, $category, $description, $transactionDate = null)
    {
        $target = $integration->target;
        if (!$target) {
            return;
        }

        $targetCurrency = $target->currency ?? 'USD';
        
        // Determine transaction flow/type and dynamically adjust texts
        $txType = 'income';
        if ($amount < 0) {
            $txType = 'expense';
            $amount = abs($amount);
            if (strpos($category, 'Payment') !== false) {
                $category = str_replace('Payment', 'Expense', $category);
            }
            if (strpos($description, 'دفعة من المشترك') !== false) {
                $description = str_replace('دفعة من المشترك', 'مصروف للمشترك', $description);
            }
        }

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
            'type' => $txType,
            'category' => $category,
            'description' => $description,
            'transactionable_type' => $integration->target_type,
            'transactionable_id' => $integration->target_id,
            'user_id' => $integration->user_id,
            'transaction_date' => $transactionDate ?: now(),
        ]);

        $isIncome = ($txType === 'income' || $txType === 'capital');

        // Increment or decrement the target balance/current value/total value based on transaction type
        if ($integration->target_type === 'App\Models\Wallet') {
            if ($isIncome) {
                $target->increment('balance', $targetAmount);
            } else {
                $target->decrement('balance', $targetAmount);
            }
        } elseif ($integration->target_type === 'App\Models\InvestmentFund') {
            if ($isIncome) {
                $target->increment('current_value', $targetAmount);
            } else {
                $target->decrement('current_value', $targetAmount);
            }
        } elseif ($integration->target_type === 'App\Models\Business') {
            if ($isIncome) {
                $target->increment('total_value', $targetAmount);
            } else {
                $target->decrement('total_value', $targetAmount);
            }
        }
    }
}

