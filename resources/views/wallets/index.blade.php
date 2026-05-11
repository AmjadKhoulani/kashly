<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Personal Vault</h2>
                <p class="text-slate-400 mt-1">Manage your personal savings, cash, and private wallets.</p>
            </div>
            <a href="{{ route('wallets.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-semibold transition-all shadow-lg shadow-indigo-500/20 group">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                New Wallet
            </a>
        </div>

        <!-- Wallets Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($wallets as $wallet)
                <div class="glass p-6 rounded-[2rem] border border-white/5 hover:border-indigo-500/30 transition-all group relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl group-hover:bg-emerald-500/10 transition-all"></div>
                    
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 bg-emerald-500/10 rounded-2xl text-emerald-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-400 text-[10px] font-bold uppercase tracking-wider">{{ $wallet->type }}</span>
                    </div>

                    <h3 class="text-xl font-bold text-white mb-1">{{ $wallet->name }}</h3>
                    <p class="text-slate-500 text-xs mb-6 uppercase tracking-widest font-bold">Balance</p>
                    
                    <div class="mb-8">
                        <span class="text-3xl font-bold text-white">${{ number_format($wallet->balance, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-white/5">
                        <div class="text-xs text-slate-500">
                            Recent activity: <span class="text-slate-300">2 days ago</span>
                        </div>
                        <a href="{{ route('wallets.show', $wallet) }}" class="p-2 text-indigo-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center glass rounded-[2rem] border-dashed border-2 border-slate-800">
                    <h3 class="text-xl font-bold text-white mb-2">No Wallets Created</h3>
                    <p class="text-slate-400 mb-8">Organize your money by creating separate wallets for savings, cash, etc.</p>
                    <a href="{{ route('wallets.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-semibold transition-all">
                        Create Your First Wallet
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
