<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('businesses.index') }}" class="inline-flex items-center text-slate-400 hover:text-white transition-colors mb-4 text-sm font-medium">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Funds
            </a>
            <h2 class="text-3xl font-bold text-white tracking-tight">Create Investment Fund</h2>
            <p class="text-slate-400 mt-1">Set up a new commercial fund to manage assets and partner shares.</p>
        </div>

        <!-- Form -->
        <div class="glass p-8 rounded-[2rem] border border-white/5">
            <form action="{{ route('businesses.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-300 mb-2">Fund Name</label>
                    <input type="text" name="name" id="name" required
                        class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-slate-600"
                        placeholder="e.g. Real Estate Tech Fund">
                    @error('name') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-300 mb-2">Description (Optional)</label>
                    <textarea name="description" id="description" rows="4"
                        class="w-full bg-slate-900 border border-slate-800 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-slate-600"
                        placeholder="Briefly describe the purpose of this fund..."></textarea>
                    @error('description') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full flex items-center justify-center px-6 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-bold transition-all shadow-xl shadow-indigo-500/20 text-lg">
                        Initialize Fund
                    </button>
                </div>
            </form>
        </div>

        <!-- Tip -->
        <div class="mt-8 flex items-start p-4 bg-indigo-500/5 rounded-2xl border border-indigo-500/10">
            <div class="p-2 bg-indigo-500/20 rounded-lg text-indigo-400 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-sm text-slate-400 leading-relaxed">
                <strong class="text-slate-200">Pro Tip:</strong> After creating the fund, you can add partners and assign equity percentages to automate profit distribution later.
            </p>
        </div>
    </div>
</x-app-layout>
