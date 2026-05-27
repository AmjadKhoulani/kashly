<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\LedgerPayment;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index()
    {
        $entries = LedgerEntry::where('user_id', auth()->id())
            ->withCount('payments')
            ->orderByRaw("FIELD(status, 'overdue', 'active', 'partial', 'settled')")
            ->orderBy('due_date')
            ->get();

        $totalReceivable = $entries->where('type', 'receivable')->whereIn('status', ['active','partial','overdue'])->sum('remaining_amount');
        $totalPayable    = $entries->where('type', 'payable')->whereIn('status', ['active','partial','overdue'])->sum('remaining_amount');
        $totalInstallment = $entries->where('type', 'installment')->whereIn('status', ['active','partial','overdue'])->sum('remaining_amount');
        $totalLoan       = $entries->where('type', 'loan')->whereIn('status', ['active','partial','overdue'])->sum('remaining_amount');

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

        $request->validate([
            'amount'       => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'notes'        => 'nullable|string',
        ]);

        LedgerPayment::create([
            'ledger_entry_id' => $entry->id,
            'user_id'         => auth()->id(),
            'amount'          => $request->amount,
            'currency'        => $entry->currency,
            'payment_date'    => $request->payment_date,
            'notes'           => $request->notes,
        ]);

        // تحديث المبلغ المدفوع
        $entry->paid_amount = $entry->payments()->sum('amount');
        $entry->syncStatus();

        return back()->with('success', 'تم تسجيل الدفعة بنجاح ✅');
    }
}
