<x-app-layout>
    <div class="py-12 px-6" x-data="{ distAmount: {{ $netProfit > 0 ? $netProfit : 0 }} }">
        <div class="max-w-7xl mx-auto space-y-12">
            
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">
                        <a href="{{ route('funds.index') }}" class="hover:text-indigo-600">صناديق الاستثمار</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <a href="{{ route('funds.show', $fund->id) }}" class="hover:text-indigo-600">{{ $fund->name }}</a>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                        <span class="text-gray-900">توزيعات الأرباح</span>
                    </nav>
                    <h2 class="text-4xl font-black text-gray-900 tracking-tight">إدارة وتوزيع الأرباح</h2>
                    <p class="text-gray-500 font-bold mt-2">احتساب الأرباح الصافية وتوزيعها على الشركاء حسب الحصص المعتمدة.</p>
                </div>
                <a href="{{ route('funds.show', $fund->id) }}" class="bg-white border border-gray-100 text-gray-900 px-8 py-4 rounded-[2rem] text-sm font-black shadow-sm hover:bg-gray-50 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l7-7-7-7"></path></svg>
                    العودة للصندوق
                </a>
            </div>

            @if(session('status'))
                <div class="bg-emerald-50 border border-emerald-100 text-emerald-700 p-6 rounded-[2rem] font-black text-sm flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center text-xl">✓</div>
                    {{ session('status') }}
                </div>
            @endif

            <!-- Current Period Summary & Execution Form -->
            <div class="bg-gray-900 p-12 rounded-[4rem] relative overflow-hidden shadow-2xl">
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl"></div>
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
                    
                    <!-- Stats Section -->
                    <div class="space-y-12">
                        <div>
                            <p class="text-[10px] text-indigo-300 font-black uppercase tracking-widest mb-4">الأرباح الصافية المتوفرة حالياً</p>
                            <p class="text-7xl font-black text-white tracking-tighter">${{ number_format($netProfit, 0) }}</p>
                            <div class="mt-6 flex items-center gap-3">
                                <span class="text-xs font-bold text-emerald-400 bg-emerald-400/10 px-4 py-2 rounded-full">جاهز للتوزيع</span>
                                <span class="text-xs font-bold text-gray-400 border border-white/10 px-4 py-2 rounded-full">إجمالي الإيرادات: ${{ number_format($income, 0) }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div class="bg-white/5 p-6 rounded-3xl">
                                <p class="text-[10px] text-gray-400 font-black uppercase mb-2">إجمالي التوزيعات السابقة</p>
                                <p class="text-2xl font-black text-white">${{ number_format($fund->distributions->sum('net_amount'), 0) }}</p>
                            </div>
                            <div class="bg-white/5 p-6 rounded-3xl">
                                <p class="text-[10px] text-gray-400 font-black uppercase mb-2">عدد الشركاء</p>
                                <p class="text-2xl font-black text-white">{{ $equities->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Execution Form -->
                    <div class="bg-white/5 p-10 rounded-[3rem] border border-white/10">
                        <h3 class="text-xl font-black text-white mb-8">بدء عملية توزيع أرباح</h3>
                        <form action="{{ route('funds.executeDistribution', $fund->id) }}" method="POST" class="space-y-6">
                            @csrf
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2">المبلغ المراد توزيعه حالياً ($)</label>
                                <input type="number" name="amount" x-model="distAmount" class="w-full bg-white/5 border-white/10 text-white rounded-[1.5rem] p-6 font-black text-xl focus:ring-emerald-500 focus:border-emerald-500">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2 tracking-widest">تاريخ التوزيع</label>
                                    <input type="date" name="distribution_date" value="{{ date('Y-m-d') }}" class="w-full bg-white/5 border-white/10 text-white rounded-[1.5rem] p-5 font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase mb-3 mr-2 tracking-widest text-right">وسيلة الدفع</label>
                                    <select name="payment_method_id" class="w-full bg-white/5 border-white/10 text-white rounded-[1.5rem] p-5 font-bold">
                                        @foreach($paymentMethods as $pm)
                                            <option value="{{ $pm->id }}" class="text-gray-900">{{ $pm->name }} (${{ number_format($pm->balance, 0) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-6 rounded-[2rem] font-black text-xl shadow-xl shadow-emerald-500/20 transition-all">تأكيد وتوثيق التوزيع</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Partner Distribution Preview -->
                <div class="lg:col-span-2 space-y-8">
                    <h3 class="text-2xl font-black text-gray-900 px-4">تفاصيل حصص الشركاء من التوزيع الحالي</h3>
                    <div class="bg-white rounded-[4rem] border border-gray-50 shadow-sm overflow-hidden">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">الشريك</th>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">النسبة</th>
                                    <th class="px-10 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-left">المبلغ المستحق</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($equities as $equity)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-10 py-8">
                                            <div class="flex items-center gap-4">
                                                <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center text-lg font-black text-indigo-600">
                                                    {{ mb_substr($equity->partner->name, 0, 1) }}
                                                </div>
                                                <p class="font-black text-gray-900">{{ $equity->partner->name }}</p>
                                            </div>
                                        </td>
                                        <td class="px-10 py-8 text-center">
                                            <span class="text-xs font-black text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg">{{ number_format($equity->percentage, 1) }}%</span>
                                        </td>
                                        <td class="px-10 py-8 text-left">
                                            <p class="font-black text-xl text-gray-900" x-text="'$' + (({{ $equity->percentage / 100 }} * distAmount) || 0).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0})"></p>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Historical Distributions -->
                <div class="space-y-8">
                    <h3 class="text-2xl font-black text-gray-900 px-4">سجل التوزيعات الموثقة</h3>
                    <div class="bg-white p-10 rounded-[4rem] border border-gray-50 shadow-sm space-y-8">
                        @forelse($fund->distributions as $dist)
                            <div class="flex items-center justify-between group">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shadow-sm">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900">توزيع أرباح</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $dist->distribution_date->format('Y/m/d') }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <p class="font-black text-gray-900">${{ number_format($dist->net_amount, 0) }}</p>
                                    <p class="text-[8px] text-emerald-500 font-black uppercase tracking-widest">تم الدفع</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">⏳</div>
                                <p class="text-gray-400 font-bold">لا توجد توزيعات موثقة حالياً</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
