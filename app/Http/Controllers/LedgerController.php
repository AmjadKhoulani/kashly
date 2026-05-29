<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\LedgerPayment;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index()
    {
        $statusOrder = ['overdue' => 0, 'active' => 1, 'partial' => 2, 'settled' => 3];

        $entries = LedgerEntry::where('user_id', auth()->id())
            ->withCount('payments')
            ->orderBy('due_date')
            ->get()
            ->sortBy(fn($e) => $statusOrder[$e->status] ?? 99);

        $active = $entries->whereIn('status', ['active', 'partial', 'overdue']);

        $totalReceivable  = $active->where('type', 'receivable')->sum(fn($e) => $e->remaining_amount);
        $totalPayable     = $active->where('type', 'payable')->sum(fn($e) => $e->remaining_amount);
        $totalInstallment = $active->where('type', 'installment')->sum(fn($e) => $e->remaining_amount);
        $totalLoan        = $active->where('type', 'loan')->sum(fn($e) => $e->remaining_amount);

        return view('ledger.index', compact(
            'entries', 'totalReceivable', 'totalPayable', 'totalInstallment', 'totalLoan'
        ));
    }

    public function store(Request $request)
    {
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

        LedgerEntry::create(array_merge(
            $request->only([
                'type','party_name','party_phone','description',
                'total_amount','currency','due_date','start_date',
                'installment_count','installment_amount','notes'
            ]),
            ['user_id' => auth()->id(), 'paid_amount' => 0, 'status' => 'active']
        ));

        return back()->with('success', 'تمت إضافة القيد بنجاح ✅');
    }

    public function show($id)
    {
        $entry = LedgerEntry::where('user_id', auth()->id())
            ->with('payments')
            ->findOrFail($id);

        return view('ledger.show', compact('entry'));
    }

    public function update(Request $request, $id)
    {
        $entry = LedgerEntry::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'party_name'   => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0',
            'due_date'     => 'nullable|date',
            'notes'        => 'nullable|string',
        ]);

        $entry->update($request->only([
            'type','party_name','party_phone','description',
            'total_amount','currency','due_date','start_date',
            'installment_count','installment_amount','notes'
        ]));

        $entry->syncStatus();

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function destroy($id)
    {
        $entry = LedgerEntry::where('user_id', auth()->id())->findOrFail($id);
        $entry->delete();
        return redirect()->route('ledger.index')->with('success', 'تم الحذف');
    }

    // تسجيل دفعة / استلام جزئي
    public function addPayment(Request $request, $id)
    {
        $entry = LedgerEntry::where('user_id', auth()->id())->findOrFail($id);

        $rules = [
            'payment_date'      => 'required|date',
            'notes'             => 'nullable|string',
            'pay_in_alt'        => 'nullable|boolean',
        ];

        if ($request->boolean('pay_in_alt')) {
            $rules['original_amount']   = 'required|numeric|min:0.01';
            $rules['original_currency'] = 'required|string|size:3';
            $rules['exchange_rate']     = 'required|numeric|min:0.0001';
            $rules['amount']            = 'nullable';
        } else {
            $rules['amount']            = 'required|numeric|min:0.01';
        }

        $request->validate($rules);

        // حساب المبلغ بعملة القيد
        $amount           = (float) $request->amount;
        $originalAmount   = null;
        $originalCurrency = null;
        $exchangeRate     = null;

        if ($request->boolean('pay_in_alt') && $request->filled('original_amount') && $request->filled('exchange_rate')) {
            $originalAmount   = (float) $request->original_amount;
            $originalCurrency = $request->original_currency;
            $exchangeRate     = (float) $request->exchange_rate;
            // المبلغ المحوّل = المبلغ الأصلي ÷ سعر الصرف
            $amount = round($originalAmount / $exchangeRate, 2);
        }

        LedgerPayment::create([
            'ledger_entry_id'   => $entry->id,
            'user_id'           => auth()->id(),
            'amount'            => $amount,
            'currency'          => $entry->currency,
            'original_amount'   => $originalAmount,
            'original_currency' => $originalCurrency,
            'exchange_rate'     => $exchangeRate,
            'payment_date'      => $request->payment_date,
            'notes'             => $request->notes,
        ]);

        // تحديث المبلغ المدفوع
        $entry->paid_amount = $entry->payments()->sum('amount');
        $entry->syncStatus();

        return back()->with('success', 'تم تسجيل الدفعة بنجاح ✅');
    }

    // إضافة مبلغ للذمة (زيادة الدين)
    public function addCharge(Request $request, $id)
    {
        $entry = LedgerEntry::where('user_id', auth()->id())->findOrFail($id);

        $rules = [
            'charge_date'       => 'nullable|date',
            'notes'             => 'nullable|string',
            'pay_in_alt'        => 'nullable|boolean',
        ];

        if ($request->boolean('pay_in_alt')) {
            $rules['original_amount']   = 'required|numeric|min:0.01';
            $rules['original_currency'] = 'required|string|size:3';
            $rules['exchange_rate']     = 'required|numeric|min:0.0001';
            $rules['amount']            = 'nullable';
        } else {
            $rules['amount']            = 'required|numeric|min:0.01';
        }

        $request->validate($rules);

        $amount           = (float) $request->amount;
        $originalAmount   = null;
        $originalCurrency = null;
        $exchangeRate     = null;

        if ($request->boolean('pay_in_alt') && $request->filled('original_amount') && $request->filled('exchange_rate')) {
            $originalAmount   = (float) $request->original_amount;
            $originalCurrency = $request->original_currency;
            $exchangeRate     = (float) $request->exchange_rate;
            $amount = round($originalAmount / $exchangeRate, 2);
        }

        $chargeDate = $request->filled('charge_date') ? \Carbon\Carbon::parse($request->charge_date)->format('Y-m-d') : now()->format('Y-m-d');

        LedgerPayment::create([
            'ledger_entry_id'   => $entry->id,
            'type'              => 'charge',
            'user_id'           => auth()->id(),
            'amount'            => $amount,
            'currency'          => $entry->currency,
            'original_amount'   => $originalAmount,
            'original_currency' => $originalCurrency,
            'exchange_rate'     => $exchangeRate,
            'payment_date'      => $chargeDate,
            'notes'             => $request->notes,
        ]);

        $entry->total_amount += $amount;
        $entry->syncStatus();

        return back()->with('success', 'تم إضافة المبلغ للذمة ✅');
    }
}
