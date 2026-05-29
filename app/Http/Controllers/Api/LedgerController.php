<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
use App\Models\LedgerPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LedgerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $statusOrder = ['overdue' => 0, 'active' => 1, 'partial' => 2, 'settled' => 3];

        $entries = LedgerEntry::where('user_id', $user->id)
            ->withCount('payments')
            ->orderBy('due_date')
            ->get()
            ->sortBy(fn($e) => $statusOrder[$e->status] ?? 99)
            ->values();

        $active = $entries->whereIn('status', ['active', 'partial', 'overdue']);

        // We can reuse the exchange rate service
        $sypRate = \App\Services\ExchangeRateService::getSypRate();
        $convertToUsd = function ($amount, $currency) use ($user, $sypRate) {
            if ($currency === 'USD') {
                return $amount;
            } elseif ($currency === 'SYP') {
                return ($sypRate > 0) ? $amount / $sypRate : $amount;
            } else {
                $lastRate = \App\Models\Transaction::where('user_id', $user->id)
                    ->where('currency', $currency)
                    ->where('exchange_rate', '>', 1)
                    ->latest()
                    ->value('exchange_rate') ?? 1;
                return ($lastRate > 0) ? $amount / $lastRate : $amount;
            }
        };

        $totalReceivableUSD = 0;
        $totalPayableUSD = 0;

        foreach ($active as $entry) {
            $rem = $entry->remaining_amount;
            $remUsd = $convertToUsd($rem, $entry->currency);
            if ($entry->type === 'receivable') {
                $totalReceivableUSD += $remUsd;
            } else {
                $totalPayableUSD += $remUsd;
            }
        }

        $mapped = $entries->map(function ($entry) {
            return [
                'id' => $entry->id,
                'party_name' => $entry->party_name,
                'party_phone' => $entry->party_phone,
                'type' => $entry->type,
                'type_label' => $entry->type_label,
                'type_color' => $entry->type_color,
                'total_amount' => (float)$entry->total_amount,
                'paid_amount' => (float)$entry->paid_amount,
                'remaining_amount' => (float)$entry->remaining_amount,
                'currency' => $entry->currency,
                'due_date' => $entry->due_date ? $entry->due_date->format('Y-m-d') : null,
                'start_date' => $entry->start_date ? $entry->start_date->format('Y-m-d') : null,
                'installment_count' => $entry->installment_count,
                'installment_amount' => (float)$entry->installment_amount,
                'notes' => $entry->notes,
                'description' => $entry->description,
                'status' => $entry->status,
                'status_label' => $entry->status_label,
                'payments_count' => $entry->payments_count,
            ];
        });

        return response()->json([
            'entries' => $mapped,
            'total_receivables_usd' => round($totalReceivableUSD, 2),
            'total_payables_usd' => round($totalPayableUSD, 2),
            'net_debts_usd' => round($totalReceivableUSD - $totalPayableUSD, 2),
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'type'          => 'required|in:receivable,payable,installment,loan',
            'party_name'    => 'required|string|max:255',
            'party_phone'   => 'nullable|string|max:30',
            'description'   => 'nullable|string',
            'total_amount'  => 'required|numeric|min:0.01',
            'currency'      => 'required|string|size:3',
            'due_date'      => 'nullable|date',
            'start_date'    => 'nullable|date',
            'installment_count'  => 'nullable|integer|min:1',
            'installment_amount' => 'nullable|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);

        $entry = LedgerEntry::create(array_merge(
            $request->only([
                'type','party_name','party_phone','description',
                'total_amount','currency','due_date','start_date',
                'installment_count','installment_amount','notes'
            ]),
            ['user_id' => $user->id, 'paid_amount' => 0, 'status' => 'active']
        ));

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدين بنجاح',
            'entry' => $entry
        ]);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();
        $entry = LedgerEntry::where('user_id', $user->id)
            ->with('payments')
            ->findOrFail($id);

        $mappedPayments = $entry->payments->map(function ($p) {
            return [
                'id' => $p->id,
                'type' => $p->type,
                'amount' => (float)$p->amount,
                'currency' => $p->currency,
                'original_amount' => $p->original_amount ? (float)$p->original_amount : null,
                'original_currency' => $p->original_currency,
                'exchange_rate' => $p->exchange_rate ? (float)$p->exchange_rate : null,
                'payment_date' => $p->payment_date ? $p->payment_date->format('Y-m-d') : null,
                'notes' => $p->notes,
            ];
        });

        return response()->json([
            'entry' => [
                'id' => $entry->id,
                'party_name' => $entry->party_name,
                'party_phone' => $entry->party_phone,
                'type' => $entry->type,
                'type_label' => $entry->type_label,
                'type_color' => $entry->type_color,
                'total_amount' => (float)$entry->total_amount,
                'paid_amount' => (float)$entry->paid_amount,
                'remaining_amount' => (float)$entry->remaining_amount,
                'currency' => $entry->currency,
                'due_date' => $entry->due_date ? $entry->due_date->format('Y-m-d') : null,
                'start_date' => $entry->start_date ? $entry->start_date->format('Y-m-d') : null,
                'installment_count' => $entry->installment_count,
                'installment_amount' => (float)$entry->installment_amount,
                'notes' => $entry->notes,
                'description' => $entry->description,
                'status' => $entry->status,
                'status_label' => $entry->status_label,
            ],
            'payments' => $mappedPayments
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->user();
        $entry = LedgerEntry::where('user_id', $user->id)->findOrFail($id);

        $request->validate([
            'party_name'   => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'due_date'     => 'nullable|date',
            'notes'        => 'nullable|string',
        ]);

        $entry->update($request->only([
            'type','party_name','party_phone','description',
            'total_amount','currency','due_date','start_date',
            'installment_count','installment_amount','notes'
        ]));

        $entry->syncStatus();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الدين بنجاح',
            'entry' => $entry
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $entry = LedgerEntry::where('user_id', $user->id)->findOrFail($id);
        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الدين بنجاح'
        ]);
    }

    public function addPayment(Request $request, $id)
    {
        $user = $request->user();
        $entry = LedgerEntry::where('user_id', $user->id)->findOrFail($id);

        $rules = [
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string',
            'pay_in_alt'   => 'nullable|boolean',
        ];

        if ($request->boolean('pay_in_alt')) {
            $rules['original_amount']   = 'required|numeric|min:0.01';
            $rules['original_currency'] = 'required|string|size:3';
            $rules['exchange_rate']     = 'required|numeric|min:0.0001';
        } else {
            $rules['amount']            = 'required|numeric|min:0.01';
        }

        $request->validate($rules);

        $amount = (float) $request->amount;
        $originalAmount = null;
        $originalCurrency = null;
        $exchangeRate = null;

        if ($request->boolean('pay_in_alt')) {
            $originalAmount   = (float) $request->original_amount;
            $originalCurrency = $request->original_currency;
            $exchangeRate     = (float) $request->exchange_rate;
            $amount = round($originalAmount / $exchangeRate, 2);
        }

        $payment = LedgerPayment::create([
            'ledger_entry_id'   => $entry->id,
            'user_id'           => $user->id,
            'amount'            => $amount,
            'currency'          => $entry->currency,
            'original_amount'   => $originalAmount,
            'original_currency' => $originalCurrency,
            'exchange_rate'     => $exchangeRate,
            'payment_date'      => $request->payment_date,
            'notes'             => $request->notes,
        ]);

        $entry->paid_amount = $entry->payments()->sum('amount');
        $entry->syncStatus();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل دفعة السداد بنجاح',
            'payment' => $payment,
            'remaining_amount' => (float)$entry->remaining_amount,
            'status' => $entry->status
        ]);
    }
}
