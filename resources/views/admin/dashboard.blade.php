<x-app-layout>
    <div class="py-12 px-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-center justify-between mb-10">
                <h1 class="text-3xl font-black text-gray-900">لوحة تحكم النظام (SaaS)</h1>
                <div class="flex items-center gap-4">
                    <span class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-black text-sm border border-indigo-100">Super Admin Mode</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">إجمالي المستخدمين</div>
                    <div class="text-4xl font-black text-gray-900">{{ $stats['total_users'] }}</div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">إجمالي الصناديق</div>
                    <div class="text-4xl font-black text-gray-900">{{ $stats['total_funds'] }}</div>
                </div>
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">إجمالي العمليات</div>
                    <div class="text-4xl font-black text-gray-900">{{ $stats['total_transactions'] }}</div>
                </div>
            </div>

            <!-- Recent Users Table -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-xl font-black text-gray-900">آخر المستخدمين المسجلين</h3>
                    <a href="{{ route('admin.users') }}" class="text-sm font-black text-indigo-600 hover:text-indigo-700">عرض الكل</a>
                </div>
                <table class="w-full text-right">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">الاسم</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">البريد الإلكتروني</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">الدور</th>
                            <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($stats['recent_users'] as $user)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-6 font-bold text-gray-900">{{ $user->name }}</td>
                                <td class="px-8 py-6 font-bold text-gray-500">{{ $user->email }}</td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                                        {{ $user->role === 'super_admin' ? 'bg-indigo-50 text-indigo-600' : 'bg-gray-50 text-gray-500' }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-sm font-bold text-gray-400">{{ $user->created_at->format('Y/m/d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
