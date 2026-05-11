<aside class="w-72 bg-slate-900 border-l border-slate-800 flex flex-col h-screen sticky top-0 transition-all duration-300">
    <div class="p-8">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 space-x-reverse group">
            <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-2xl font-black tracking-tight text-white mr-3">كاشلي</span>
        </a>
    </div>

    <nav class="flex-1 px-4 space-y-2">
        <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-4 mb-4 mt-4 opacity-50">القائمة الرئيسية</div>
        
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="flex items-center px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('dashboard') ? 'bg-indigo-600/10 text-indigo-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }}">
            <div class="p-2 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-500' }} rounded-xl ml-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            </div>
            <span class="font-bold text-sm">لوحة التحكم</span>
        </x-nav-link>

        <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-4 mb-4 mt-8 opacity-50">تجاري</div>
        
        <x-nav-link :href="route('businesses.index')" :active="request()->routeIs('businesses.*')" class="flex items-center px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('businesses.*') ? 'bg-indigo-600/10 text-indigo-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }}">
            <div class="p-2 {{ request()->routeIs('businesses.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'bg-slate-800 text-slate-500' }} rounded-xl ml-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <span class="font-bold text-sm">صناديق الاستثمار</span>
        </x-nav-link>

        <x-nav-link :href="route('partners.index')" :active="request()->routeIs('partners.*')" class="flex items-center px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('partners.*') ? 'bg-amber-600/10 text-amber-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }}">
            <div class="p-2 {{ request()->routeIs('partners.*') ? 'bg-amber-600 text-white shadow-lg shadow-amber-500/20' : 'bg-slate-800 text-slate-500' }} rounded-xl ml-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <span class="font-bold text-sm">الشركاء</span>
        </x-nav-link>

        <div class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-4 mb-4 mt-8 opacity-50">شخصي</div>

        <x-nav-link :href="route('wallets.index')" :active="request()->routeIs('wallets.*')" class="flex items-center px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('wallets.*') ? 'bg-emerald-600/10 text-emerald-400' : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }}">
            <div class="p-2 {{ request()->routeIs('wallets.*') ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-500/20' : 'bg-slate-800 text-slate-500' }} rounded-xl ml-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <span class="font-bold text-sm">الخزنة الشخصية</span>
        </x-nav-link>

        <a href="#" class="flex items-center px-4 py-3 rounded-2xl transition-all group text-slate-400 hover:bg-slate-800/50 hover:text-slate-200">
            <div class="p-2 bg-slate-800 text-slate-500 rounded-xl ml-3 group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="font-bold text-sm">الديون والمستحقات</span>
        </a>
    </nav>

    <div class="p-4 border-t border-slate-800">
        <div class="flex items-center p-3 rounded-2xl bg-slate-850">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400 font-black text-sm">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="mr-3 overflow-hidden">
                <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[10px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mr-auto">
                @csrf
                <button type="submit" class="p-2 text-slate-500 hover:text-rose-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>
</aside>
