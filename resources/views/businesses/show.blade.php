<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('businesses.index') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors mb-2 text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Funds
                </a>
                <h2 class="text-3xl font-bold text-white tracking-tight">{{ $business->name }}</h2>
                <p class="text-slate-400 mt-1">{{ $business->description }}</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-sm font-semibold border border-slate-700 transition-all">
                    Edit Fund
                </button>
                <button class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-500/20 transition-all">
                    Add Transaction
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass p-6 rounded-[2rem] border border-white/5">
                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-2">Total Capital</p>
                <p class="text-2xl font-bold text-white">${{ number_format($business->transactions->sum('amount'), 2) }}</p>
            </div>
            <div class="glass p-6 rounded-[2rem] border border-white/5">
                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-2">Total Partners</p>
                <p class="text-2xl font-bold text-white">{{ $business->partners->count() }}</p>
            </div>
            <div class="glass p-6 rounded-[2rem] border border-white/5">
                <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-2">Profit/Loss</p>
                <p class="text-2xl font-bold text-emerald-400">+$0.00</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Partners List -->
            <div class="lg:col-span-1 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Partners & Equity</h3>
                    <button class="text-indigo-400 hover:text-indigo-300 text-sm font-semibold">+ Add</button>
                </div>
                <div class="space-y-3">
                    @forelse($business->partners as $partner)
                        <div class="glass p-4 rounded-2xl border border-white/5 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-xs font-bold text-indigo-400 mr-3">
                                    {{ substr($partner->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-slate-200">{{ $partner->name }}</span>
                            </div>
                            <span class="text-sm font-bold text-white">{{ $partner->equity_percentage }}%</span>
                        </div>
                    @empty
                        <p class="text-slate-500 text-sm py-4">No partners assigned to this fund.</p>
                    @endforelse
                </div>
            </div>

            <!-- Transactions List -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Recent Transactions</h3>
                    <button class="text-slate-400 hover:text-white text-sm font-semibold">View All</button>
                </div>
                <div class="glass rounded-[2rem] border border-white/5 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white/5 border-b border-white/5">
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Category</th>
                                <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @forelse($business->transactions as $transaction)
                                <tr class="hover:bg-white/[0.02] transition-colors group">
                                    <td class="px-6 py-4 text-sm text-slate-400">
                                        {{ $transaction->transaction_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 rounded-full {{ $transaction->type == 'income' ? 'bg-emerald-500' : 'bg-rose-500' }} mr-3"></div>
                                            <span class="text-white text-sm font-medium">{{ $transaction->category }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-bold {{ $transaction->type == 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format(abs($transaction->amount), 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                        No transactions recorded for this fund.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
