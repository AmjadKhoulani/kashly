<?php

namespace App\Http\Controllers;

use App\Models\InvestmentFund;
use App\Models\Partner;
use App\Models\Equity;
use App\Models\Transaction;
use App\Models\FundAsset;
use App\Models\Distribution;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentFundController extends Controller
{
    public function index()
    {
        $funds = InvestmentFund::where('user_id', auth()->id())->get();
        return view('funds.index', compact('funds'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'capital' => 'nullable|numeric|min:0',
                'distribution_frequency' => 'required|string',
                'currency' => 'required|string|size:3',
            ]);

            return DB::transaction(function () use ($validated) {
                $fund = InvestmentFund::create([
                    'user_id' => auth()->id(),
                    'name' => $validated['name'],
                    'capital' => $validated['capital'] ?? 0,
                    'current_value' => $validated['capital'] ?? 0,
                    'distribution_frequency' => $validated['distribution_frequency'],
                    'currency' => $validated['currency'],
                    'icon' => '🏢',
                    'status' => 'active',
                ]);

                $partner = Partner::updateOrCreate(
                    ['email' => auth()->user()->email],
                    [
                        'user_id' => auth()->id(),
                        'name' => auth()->user()->name,
                        'linked_user_id' => auth()->id()
                    ]
                );

                Equity::create([
                    'partner_id' => $partner->id,
                    'equitable_id' => $fund->id,
                    'equitable_type' => 'App\Models\InvestmentFund',
                    'amount' => $fund->capital,
                    'percentage' => $fund->capital > 0 ? 100 : 0,
                    'equity_type' => 'contribution',
                ]);

                return redirect()->route('funds.show', $fund->id)->with('success', 'تم إنشاء الكيان الاستثماري بنجاح');
            });

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Fund Store Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $fund = InvestmentFund::where('user_id', auth()->id())
            ->with(['assets', 'distributions'])
            ->findOrFail($id);
        
        $equities = Equity::where('equitable_id', $fund->id)
            ->where('equitable_type', InvestmentFund::class)
            ->with('partner')
            ->get();

        $transactions = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->with('paymentMethod')
            ->latest()
            ->limit(5)
            ->get();

        $paymentMethods = PaymentMethod::where('fund_id', $fund->id)->get();

        // Automatic fix for existing wrong calculations
        if ($equities->count() > 0 && abs($equities->sum('percentage') - 100) > 0.1) {
            $this->recalculateEquities($fund->id);
            $equities = Equity::where('equitable_id', $fund->id)
                ->where('equitable_type', InvestmentFund::class)
                ->with('partner')
                ->get();
        }

        return view('funds.show', compact('fund', 'equities', 'transactions', 'paymentMethods'));
    }

    public function fundTransactions($id)
    {
        $fund = InvestmentFund::where('user_id', auth()->id())->findOrFail($id);
        
        $transactions = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->with('paymentMethod')
            ->latest()
            ->paginate(20);

        $income = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->where('type', 'income')->sum('amount');
            
        $expense = Transaction::where('transactionable_id', $fund->id)
            ->where('transactionable_type', InvestmentFund::class)
            ->where('type', 'expense')->sum('amount');

        return view('funds.transactions', compact('fund', 'transactions', 'income', 'expense'));
    }

    public function addPartner(Request $request, $id)
    {
        $fund = InvestmentFund::findOrFail($id);
        if ($fund->user_id !== auth()->id()) abort(403);

        $request->validate([
            'partner_id' => 'required_without:new_partner_name|nullable|exists:partners,id',
            'new_partner_name' => 'required_without:partner_id|nullable|string|max:255',
            'new_partner_email' => 'required_if:is_new,true|nullable|email|unique:users,email',
            'equity_type' => 'required|in:contribution,fixed',
            'amount' => 'required_if:equity_type,contribution|nullable|numeric|min:0',
            'percentage' => 'required_if:equity_type,fixed|nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($fund, $request) {
            $partnerId = $request->partner_id;

            if ($request->filled('new_partner_name')) {
                $password = \Illuminate\Support\Str::random(10);
                $user = \App\Models\User::create([
                    'name' => $request->new_partner_name,
                    'email' => $request->new_partner_email,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'role' => 'partner',
                ]);

                $partner = \App\Models\Partner::create([
                    'user_id' => auth()->id(),
                    'name' => $request->new_partner_name,
                    'email' => $request->new_partner_email,
                    'linked_user_id' => $user->id,
                ]);
                $partnerId = $partner->id;
                
                try {
                    $user->notify(new \App\Notifications\PartnerInvitedNotification($user->email, $password));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Mail Error: ' . $e->getMessage());
                    session()->flash('mail_error', 'فشل إرسال البريد الإلكتروني، لكن تم إنشاء الحساب بنجاح.');
                }

                session()->flash('new_partner_password', $password);
                session()->flash('new_partner_email', $user->email);
            }

            if ($request->equity_type === 'contribution') {
                $fund->increment('capital', $request->amount);
                $fund->increment('current_value', $request->amount);

                Equity::updateOrCreate(
                    ['partner_id' => $partnerId, 'equitable_id' => $fund->id, 'equitable_type' => InvestmentFund::class],
                    ['amount' => $request->amount, 'equity_type' => 'contribution']
                );
            } else {
                Equity::updateOrCreate(
                    ['partner_id' => $partnerId, 'equitable_id' => $fund->id, 'equitable_type' => InvestmentFund::class],
                    ['percentage' => $request->percentage, 'equity_type' => 'fixed', 'amount' => 0]
                );
            }

            $this->recalculateEquities($fund->id);
        });

        return back()->with('status', 'تم إضافة الشريك وتحديث الحصص بنجاح.');
    }

    public function updateEquity(Request $request, $id)
    {
        $equity = Equity::findOrFail($id);
        $fund = InvestmentFund::findOrFail($equity->equitable_id);
        
        if ($fund->user_id !== auth()->id()) abort(403);

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'percentage' => 'required|numeric|min:0|max:100',
        ]);

        $equity->update([
            'amount' => $request->amount,
            'percentage' => $request->percentage,
        ]);

        $this->recalculateEquities($fund->id);

        return back()->with('status', 'تم تحديث بيانات الحصة بنجاح.');
    }

    private function recalculateEquities($fundId)
    {
        $fund = InvestmentFund::findOrFail($fundId);
        $allEquities = Equity::where('equitable_id', $fundId)
            ->where('equitable_type', InvestmentFund::class)
            ->get();

        // 1. Calculate Total Contribution Amount
        $totalContributionAmount = $allEquities->where('equity_type', 'contribution')->sum('amount');
        
        // 2. Calculate Total Fixed Percentage
        $totalFixedPercentage = $allEquities->where('equity_type', 'fixed')->sum('percentage');
        
        // 3. Remaining percentage to be distributed among contribution partners
        $remainingPercentage = max(0, 100 - $totalFixedPercentage);

        foreach ($allEquities as $eq) {
            if ($eq->equity_type === 'contribution') {
                $myPercentage = $totalContributionAmount > 0 
                    ? ($eq->amount / $totalContributionAmount) * $remainingPercentage 
                    : 0;
                $eq->update(['percentage' => $myPercentage]);
            }
        }

        // Update fund capital and current_value to reflect the sum of all contributions
        $fund->update([
            'capital' => $totalContributionAmount,
            'current_value' => $totalContributionAmount // Sync current value with total capital for accuracy
        ]);
    }

    public function removePartner($id)
    {
        $equity = Equity::findOrFail($id);
        $fundId = $equity->equitable_id;
        $fund = InvestmentFund::findOrFail($fundId);
        
        if ($fund->user_id !== auth()->id()) abort(403);

        $equity->delete();
        $this->recalculateEquities($fundId);

        return back()->with('status', 'تم حذف الشريك من الصندوق بنجاح.');
    }

    public function addAsset(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'value' => 'required|numeric',
            'purchase_date' => 'required|date',
        ]);

        FundAsset::create([
            'investment_fund_id' => $id,
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'purchase_date' => $request->purchase_date,
            'notes' => $request->notes,
        ]);

        return back()->with('status', 'تم إضافة الأصل بنجاح.');
    }

    public function distributions($id)
    {
        $fund = InvestmentFund::with(['distributions' => function($q) {
            $q->latest();
        }])->findOrFail($id);
        
        $equities = Equity::where('equitable_id', $fund->id)
            ->where('equitable_type', InvestmentFund::class)
            ->with('partner')
            ->get();

        // Calculate current period profit (since last distribution)
        $lastDistribution = Distribution::where('investment_fund_id', $id)->latest('distribution_date')->first();
        $query = Transaction::where('transactionable_id', $id)->where('transactionable_type', InvestmentFund::class);
        
        if ($lastDistribution) {
            $query->where('created_at', '>', $lastDistribution->created_at);
        }

        $income = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');
        $netProfit = $income - $expense;

        $paymentMethods = PaymentMethod::where('fund_id', $id)->get();

        return view('funds.distributions', compact('fund', 'equities', 'netProfit', 'income', 'expense', 'paymentMethods'));
    }

    public function executeDistribution(Request $request, $id)
    {
        $fund = InvestmentFund::findOrFail($id);
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'distribution_date' => 'required|date',
        ]);

        return DB::transaction(function () use ($fund, $request) {
            // 1. Create Distribution Master Record
            $distribution = Distribution::create([
                'investment_fund_id' => $fund->id,
                'gross_amount' => $request->amount,
                'net_amount' => $request->amount,
                'distribution_date' => $request->distribution_date,
                'status' => 'completed',
            ]);

            // 2. Create Partner Distribution Records
            $equities = Equity::where('equitable_id', $fund->id)
                ->where('equitable_type', InvestmentFund::class)
                ->get();

            foreach ($equities as $equity) {
                if ($equity->percentage > 0) {
                    $partnerAmount = ($equity->percentage / 100) * $request->amount;
                    
                    DB::table('partner_distributions')->insert([
                        'distribution_id' => $distribution->id,
                        'partner_id' => $equity->partner_id,
                        'amount' => $partnerAmount,
                        'percentage' => $equity->percentage,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 3. Create Transaction Record (Expense)
            Transaction::create([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'type' => 'expense',
                'category' => 'توزيع أرباح',
                'description' => 'توزيع أرباح للفترة المنتهية في ' . $request->distribution_date,
                'transactionable_id' => $fund->id,
                'transactionable_type' => InvestmentFund::class,
                'payment_method_id' => $request->payment_method_id,
                'transaction_date' => $request->distribution_date,
                'currency' => $fund->currency,
            ]);

            // 4. Update Payment Method Balance
            $paymentMethod = PaymentMethod::find($request->payment_method_id);
            $paymentMethod->decrement('balance', $request->amount);

            return redirect()->route('funds.distributions', $fund->id)->with('status', 'تم تنفيذ وتوثيق توزيع الأرباح بنجاح.');
        });
    }
    public function reconcileAccount(Request $request, $fundId, $accountId)
    {
        $fund = InvestmentFund::findOrFail($fundId);
        $paymentMethod = PaymentMethod::where('fund_id', $fundId)->findOrFail($accountId);
        $request->validate(['actual_balance' => 'required|numeric|min:0']);

        $difference = $request->actual_balance - $paymentMethod->balance;

        if ($difference == 0) {
            return back()->with('status', 'الرصيد مطابق تماماً.');
        }

        return DB::transaction(function () use ($fund, $paymentMethod, $difference, $request) {
            // 1. Create Adjustment Transaction
            Transaction::create([
                'user_id' => auth()->id(),
                'amount' => abs($difference),
                'type' => $difference > 0 ? 'income' : 'expense',
                'category' => 'تسوية رصيد',
                'description' => 'مطابقة رصيد حساب: ' . $paymentMethod->name . ' - المبلغ الحقيقي: ' . $request->actual_balance,
                'transactionable_id' => $fund->id,
                'transactionable_type' => InvestmentFund::class,
                'payment_method_id' => $paymentMethod->id,
                'transaction_date' => now(),
                'currency' => $fund->currency,
            ]);

            // 2. Update Payment Method Balance
            $paymentMethod->update(['balance' => $request->actual_balance]);

            // 3. Update Fund current_value
            if ($difference > 0) {
                $fund->increment('current_value', abs($difference));
            } else {
                $fund->decrement('current_value', abs($difference));
            }

            return back()->with('status', 'تمت مطابقة رصيد الحساب وتحديث القيمة الحالية للصندوق.');
        });
    }

    public function addPaymentMethod(Request $request, $id)
    {
        $fund = InvestmentFund::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:bank,cash,credit_card,debit_card,other',
            'balance' => 'required|numeric',
            'currency' => 'required|string|size:3',
        ]);

        PaymentMethod::create([
            'user_id' => auth()->id(),
            'fund_id' => $fund->id,
            'name' => $request->name,
            'type' => $request->type,
            'balance' => $request->balance,
            'currency' => $request->currency,
        ]);

        return back()->with('success', 'تمت إضافة الحساب للصندوق بنجاح');
    }

    public function destroy($id)
    {
        $fund = InvestmentFund::where('user_id', auth()->id())->findOrFail($id);
        $fund->delete();

        return redirect()->route('funds.index')->with('success', 'تم حذف الكيان الاستثماري بنجاح');
    }
}
