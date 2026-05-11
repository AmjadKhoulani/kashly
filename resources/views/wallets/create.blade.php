<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('wallets.index') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors mb-4 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Vault
            </a>
            <h2 class="text-3xl font-bold text-white tracking-tight">Create New Wallet</h2>
            <p class="text-slate-400 mt-1">Set up a private wallet to track your personal savings or cash.</p>
        </div>

        <!-- Form -->
        <div class="glass p-8 rounded-[2rem] border border-white/5">
            <form action="{{ route('wallets.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-300 mb-2">Wallet Name</label>
                    <input type="text" name="name" id="name" required
                        class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-slate-600"
                        placeholder="e.g. My Savings Account">
                </div>

                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-300 mb-2">Wallet Type</label>
                    <select name="type" id="type" required
                        class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="savings">Savings</option>
                        <option value="current">Current (Daily Expenses)</option>
                        <option value="cash">Cash (Physical)</option>
                        <option value="investment">Private Investment</option>
                    </select>
                </div>

                <div>
                    <label for="balance" class="block text-sm font-semibold text-slate-300 mb-2">Initial Balance</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-bold">$</span>
                        <input type="number" step="0.01" name="balance" id="balance" required
                            class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl pl-8 pr-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-slate-600"
                            placeholder="0.00">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex items-center justify-center px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all shadow-xl shadow-indigo-500/20 text-lg">
                        Create Wallet
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
