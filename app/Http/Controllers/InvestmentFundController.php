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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capital' => 'nullable|numeric|min:0',
            'distribution_frequency' => 'required|string',
            'currency' => 'required|string|size:3',
            'icon' => 'nullable|string',
        ]);

        $fund = InvestmentFund::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'capital' => $validated['capital'] ?? 0,
            'current_value' => $validated['capital'] ?? 0,
            'distribution_frequency' => $validated['distribution_frequency'],
            'currency' => $validated['currency'],
            'icon' => $validated['icon'] ?? '🏘️',
            'status' => 'active',
        ]);

        return redirect()->route('funds.show', $fund->id)->with('success', 'تم إنشاء الكيان الاستثماري بنجاح');
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
            ->get();

        $paymentMethods = PaymentMethod::where('user_id', auth()->id())->get();

        return view('funds.show', compact('fund', 'equities', 'transactions', 'paymentMethods'));
    }

    public function addPartner(Request $request, $id)
    {
        $fund = InvestmentFund::findOrFail($id);
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
                // Create User account first
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
                
                // Send Notification
                $user->notify(new \App\Notifications\PartnerInvitedNotification($user->email, $password));

                // Store password in session for one-time display
                session()->flash('new_partner_password', $password);
                session()->flash('new_partner_email', $user->email);
            }

            if ($request->equity_type === 'contribution') {
                // Add to capital
                $fund->increment('capital', $request->amount);
                $fund->increment('current_value', $request->amount);

                // Create or update equity for this partner
                Equity::updateOrCreate(
                    ['partner_id' => $partnerId, 'equitable_id' => $fund->id, 'equitable_type' => InvestmentFund::class],
                    ['amount' => $request->amount, 'equity_type' => 'contribution']
                );

                // Recalculate all contribution-based percentages
                $allContributionEquities = Equity::where('equitable_id', $fund->id)
                    ->where('equitable_type', InvestmentFund::class)
                    ->where('equity_type', 'contribution')
                    ->get();

                foreach ($allContributionEquities as $eq) {
                    $newPercent = ($eq->amount / $fund->capital) * 100;
                    $eq->update(['percentage' => $newPercent]);
                }
            } else {
                // Fixed percentage
                Equity::updateOrCreate(
                    ['partner_id' => $partnerId, 'equitable_id' => $fund->id, 'equitable_type' => InvestmentFund::class],
                    ['percentage' => $request->percentage, 'equity_type' => 'fixed', 'amount' => 0]
                );
            }
        });

        return back()->with('status', 'تم إضافة الشريك وتحديث الحصص بنجاح.');
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
        $fund = InvestmentFund::with('distributions')->findOrFail($id);
        $equities = Equity::where('equitable_id', $fund->id)
            ->where('equitable_type', InvestmentFund::class)
            ->with('partner')
            ->get();

        // Calculate current period profit
        $lastDistribution = Distribution::where('investment_fund_id', $id)->latest('distribution_date')->first();
        $query = Transaction::where('transactionable_id', $id)->where('transactionable_type', InvestmentFund::class);
        
        if ($lastDistribution) {
            $query->where('created_at', '>', $lastDistribution->created_at);
        }

        $income = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');
        $netProfit = $income - $expense;

        return view('funds.distributions', compact('fund', 'equities', 'netProfit', 'income', 'expense'));
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
}
