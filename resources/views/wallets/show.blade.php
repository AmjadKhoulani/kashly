<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('wallets.index') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors mb-2 text-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Vault
                </a>
                <div class="flex items-center space-x-3">
                    <h2 class="text-3xl font-bold text-white tracking-tight">{{ $wallet->name }}</h2>
                    <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-400 text-[10px] font-bold uppercase tracking-wider">{{ $wallet->type }}</span>
                </div>
            </div>
            <div class="flex space-x-3">
                <button class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-sm font-semibold border border-slate-700 transition-all">
                    Settings
                </button>
                <button class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-emerald-500/20 transition-all">
                    Add Transaction
                </button>
            </div>
        </div>

        <!-- Balance Card -->
        <div class="glass p-8 rounded-[2rem] border border-white/5 relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <p class="text-sm text-slate-500 uppercase font-bold tracking-widest mb-2">Available Balance</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-5xl font-black text-white">${{ number_format($wallet->balance, 2) }}</span>
                    <span class="text-slate-500 font-medium">USD</span>
                </div>
            </div>
        </div>

        <!-- Transactions List -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">History</h3>
                <div class="flex space-x-2">
                    <button class="px-4 py-2 bg-slate-800 text-white text-xs font-bold rounded-lg border border-slate-700">All</button>
                    <button class="px-4 py-2 text-slate-400 text-xs font-bold rounded-lg">Income</button>
                    <button class="px-4 py-2 text-slate-400 text-xs font-bold rounded-lg">Expense</button>
                </div>
            </div>

            <div class="glass rounded-[2rem] border border-white/5 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-white/5">
                        @forelse($wallet->transactions as $transaction)
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-6">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-2xl {{ $transaction->type == 'income' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }} flex items-center justify-center mr-4">
                                            @if($transaction->type == 'income')
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                            @else
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-white font-bold">{{ $transaction->category }}</p>
                                            <p class="text-slate-500 text-xs">{{ $transaction->transaction_date->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-sm text-slate-400">
                                    {{ $transaction->description }}
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <span class="text-lg font-black {{ $transaction->type == 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format(abs($transaction->amount), 2) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-20 text-center">
                                    <p class="text-slate-500">No transactions recorded in this vault.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
