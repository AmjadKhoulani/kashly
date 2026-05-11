<x-app-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Investment Funds</h2>
                <p class="text-slate-400 mt-1">Manage your commercial portfolios and partners.</p>
            </div>
            <a href="{{ route('businesses.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-semibold transition-all shadow-lg shadow-indigo-500/20 group">
                <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create New Fund
            </a>
        </div>

        <!-- Funds Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($businesses as $business)
                <div class="bg-slate-900 p-6 rounded-[2.5rem] border border-white/5 hover:border-indigo-500/30 transition-all group relative overflow-hidden shadow-xl shadow-black/20">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-all"></div>
                    
                    <div class="flex justify-between items-start mb-8">
                        <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-[10px] font-black uppercase tracking-widest">Commercial</span>
                    </div>

                    <h3 class="text-xl font-bold text-white mb-2">{{ $business->name }}</h3>
                    <p class="text-slate-500 text-sm line-clamp-1 mb-8">{{ $business->description ?? 'Business Portfolio' }}</p>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-white/5 p-4 rounded-2xl text-center">
                            <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Partners</p>
                            <p class="text-xl font-black text-white">{{ $business->partners_count }}</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl text-center">
                            <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest mb-1">Assets</p>
                            <p class="text-xl font-black text-emerald-400">${{ number_format($business->transactions_sum_amount ?? 0, 2) }}</p>
                        </div>
                    </div>

                    <a href="{{ route('businesses.show', $business) }}" class="w-full flex items-center justify-center py-4 bg-slate-800 hover:bg-slate-700 text-white rounded-2xl text-sm font-bold transition-all border border-slate-700">
                        Manage Portfolio
                    </a>
                </div>
            @empty
                <div class="col-span-full py-20 text-center glass rounded-[2rem] border-dashed border-2 border-slate-800">
                    <div class="w-20 h-20 bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-600">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">No Investment Funds Found</h3>
                    <p class="text-slate-400 mb-8">Start by creating your first commercial fund to track partners and profits.</p>
                    <a href="{{ route('businesses.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-semibold transition-all">
                        Create Your First Fund
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
