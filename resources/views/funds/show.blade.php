<x-app-layout>
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-10">
            
            <!-- Breadcrumbs & Header -->
            <div class="px-4">
                <nav class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">
                    <a href="{{ route('funds.index') }}" class="hover:text-indigo-600">صناديق الاستثمار</a> / 
                    <span class="text-gray-900">{{ $fund->name }}</span>
                </nav>
                <div class="flex justify-between items-center">
                    <h2 class="text-4xl font-black text-gray-900">{{ $fund->name }}</h2>
                    <div class="flex space-x-3 space-x-reverse">
                        <button class="bg-white border border-gray-200 text-gray-900 px-5 py-2.5 rounded-xl text-sm font-black shadow-sm">تعديل</button>
                        <button class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-black shadow-lg shadow-indigo-500/20">توزيع أرباح</button>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="spendee-card p-8">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-1">رأس المال الكلي</p>
                    <p class="text-3xl font-black text-gray-900">${{ number_format($fund->capital, 2) }}</p>
                </div>
                <div class="spendee-card p-8">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-1">القيمة الحالية</p>
                    <p class="text-3xl font-black text-indigo-600">${{ number_format($fund->current_value, 2) }}</p>
                </div>
                <div class="spendee-card p-8">
                    <p class="text-[10px] text-gray-400 font-black uppercase mb-1">نسبة النمو</p>
                    @php 
                        $growth = (($fund->current_value - $fund->capital) / $fund->capital) * 100;
                    @endphp
                    <p class="text-3xl font-black text-emerald-600">{{ number_format($growth, 1) }}%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Partners Section -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex justify-between items-center px-2">
                        <h3 class="text-xl font-black text-gray-900">الشركاء والمساهمات</h3>
                        <button class="text-indigo-600 text-xs font-black hover:underline">+ إضافة شريك</button>
                    </div>
                    <div class="spendee-card overflow-hidden">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">الشريك</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">المبلغ المستثمر</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">الحصة (%)</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase">الحصة الحالية</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($equities as $equity)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-xs font-black text-indigo-600 ml-3">
                                                    {{ mb_substr($equity->partner->name, 0, 1) }}
                                                </div>
                                                <span class="font-bold text-gray-900">{{ $equity->partner->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 font-bold text-gray-600">${{ number_format($equity->amount, 0) }}</td>
                                        <td class="px-6 py-5">
                                            <span class="px-2 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-lg">
                                                {{ number_format($equity->percentage, 1) }}%
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 font-black text-gray-900">
                                            ${{ number_format(($equity->percentage / 100) * $fund->current_value, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Fund Activity -->
                <div class="space-y-6">
                    <h3 class="text-xl font-black text-gray-900 px-2">آخر التحركات</h3>
                    <div class="spendee-card p-6 space-y-6">
                        @forelse($transactions as $transaction)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 {{ $transaction->type == 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} rounded-xl flex items-center justify-center text-lg">
                                        {{ $transaction->type == 'income' ? '📈' : '📉' }}
                                    </div>
                                    <div class="mr-3">
                                        <p class="text-sm font-black text-gray-900">{{ $transaction->description }}</p>
                                        <p class="text-[10px] text-gray-400 uppercase font-black">{{ $transaction->transaction_date->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-black {{ $transaction->type == 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 0) }}
                                </span>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 text-xs font-bold py-4">لا يوجد عمليات مؤخراً</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
