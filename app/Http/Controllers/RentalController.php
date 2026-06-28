<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\LeaseContract;
use App\Models\LeasePayment;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $properties = Property::where('user_id', $userId)->with('units.activeContract.tenant')->get();
        $tenants = Tenant::where('user_id', $userId)->get();
        $paymentMethods = PaymentMethod::where('user_id', $userId)->get();

        // Get all contracts with units and tenants
        $contracts = LeaseContract::whereHas('unit.property', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['unit.property', 'tenant'])->get();

        // Get all pending and paid payments
        $payments = LeasePayment::whereHas('leaseContract.unit.property', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['leaseContract.unit.property', 'leaseContract.tenant', 'paymentMethod'])
          ->orderBy('due_date', 'asc')
          ->get();

        return view('rentals.index', compact('properties', 'tenants', 'paymentMethods', 'contracts', 'payments'));
    }

    public function storeProperty(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Property::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'تم تسجيل العقار بنجاح.');
    }

    public function storeUnit(Request $request, $propertyId)
    {
        $property = Property::where('user_id', auth()->id())->findOrFail($propertyId);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:residential,commercial',
            'rent_amount' => 'required|numeric|min:0',
        ]);

        Unit::create([
            'property_id' => $property->id,
            'name' => $request->name,
            'type' => $request->type,
            'rent_amount' => $request->rent_amount,
            'status' => 'vacant',
        ]);

        return redirect()->back()->with('success', 'تمت إضافة الوحدة بنجاح.');
    }

    public function storeTenant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'national_id' => 'nullable|string|max:50',
        ]);

        Tenant::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'national_id' => $request->national_id,
        ]);

        return redirect()->back()->with('success', 'تم إضافة المستأجر بنجاح.');
    }

    public function storeContract(Request $request)
    {
        $request->validate([
            'unit_id' => 'required|exists:units,id',
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'rent_amount' => 'required|numeric|min:0',
            'billing_cycle' => 'required|in:monthly,quarterly,semi_annually,annually',
        ]);

        // Verify ownership
        $unit = Unit::whereHas('property', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($request->unit_id);

        $tenant = Tenant::where('user_id', auth()->id())->findOrFail($request->tenant_id);

        DB::transaction(function () use ($request, $unit) {
            // Create contract
            $contract = LeaseContract::create([
                'unit_id' => $request->unit_id,
                'tenant_id' => $request->tenant_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'rent_amount' => $request->rent_amount,
                'billing_cycle' => $request->billing_cycle,
                'status' => 'active',
            ]);

            // Update unit status to occupied
            $unit->update(['status' => 'occupied']);

            // Generate lease payments
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $endDate = \Carbon\Carbon::parse($request->end_date);
            $rent = $request->rent_amount;

            $monthsInterval = 1;
            if ($request->billing_cycle === 'quarterly') $monthsInterval = 3;
            elseif ($request->billing_cycle === 'semi_annually') $monthsInterval = 6;
            elseif ($request->billing_cycle === 'annually') $monthsInterval = 12;

            $currentDueDate = $startDate->copy();
            while ($currentDueDate->lt($endDate)) {
                LeasePayment::create([
                    'lease_contract_id' => $contract->id,
                    'amount_due' => $rent,
                    'due_date' => $currentDueDate->format('Y-m-d'),
                    'status' => 'pending',
                ]);

                $currentDueDate->addMonths($monthsInterval);
            }
        });

        return redirect()->back()->with('success', 'تم إنشاء عقد الإيجار وجدولة الدفعات بنجاح.');
    }

    public function collectPayment(Request $request, $paymentId)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $payment = LeasePayment::whereHas('leaseContract.unit.property', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($paymentId);

        $paymentMethod = PaymentMethod::where('user_id', auth()->id())->findOrFail($request->payment_method_id);

        DB::transaction(function () use ($payment, $paymentMethod) {
            // 1. Create financial transaction in core
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'amount' => $payment->amount_due,
                'type' => 'income',
                'category' => 'أرباح إيجارات',
                'description' => 'تحصيل إيجار: ' . $payment->leaseContract->unit->property->name . ' - ' . $payment->leaseContract->unit->name . ' (المستأجر: ' . $payment->leaseContract->tenant->name . ')',
                'transaction_date' => now(),
                'payment_method_id' => $paymentMethod->id,
                'transactionable_type' => Property::class,
                'transactionable_id' => $payment->leaseContract->unit->property_id,
                'currency' => 'USD',
                'exchange_rate' => 1.0,
            ]);

            // 2. Increment balance of payment method
            $paymentMethod->increment('balance', $payment->amount_due);

            // 3. Mark lease payment as paid
            $payment->update([
                'amount_paid' => $payment->amount_due,
                'paid_date' => now(),
                'payment_method_id' => $paymentMethod->id,
                'transaction_id' => $transaction->id,
                'status' => 'paid',
            ]);
        });

        return redirect()->back()->with('success', 'تم تحصيل الدفعة بنجاح وتحديث أرصدة الحساب.');
    }
}
