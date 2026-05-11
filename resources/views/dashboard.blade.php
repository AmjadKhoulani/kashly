<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">
                {{ __('Financial Overview') }}
            </h2>
            <p class="text-slate-400 text-sm">Welcome back, {{ Auth::user()->name }}! Here's what's happening with your wealth today.</p>
        </div>
        <div class="flex space-x-3">
            <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-all shadow-lg shadow-indigo-500/20">
                + New Transaction
            </button>
            <button class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-sm font-semibold transition-all border border-slate-700">
                Generate Report
            </button>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Net Worth -->
            <div class="glass p-6 rounded-3xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-indigo-500/20 rounded-lg text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded-full">+12.5%</span>
                </div>
                <p class="text-slate-400 text-sm font-medium uppercase tracking-wider">Total Net Worth</p>
                <h3 class="text-3xl font-bold text-white mt-1">${{ number_format($netWorth, 2) }}</h3>
            </div>

            <!-- Monthly Profit -->
            <div class="glass p-6 rounded-3xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded-full">+0%</span>
                </div>
                <p class="text-slate-400 text-sm font-medium uppercase tracking-wider">Monthly Profit</p>
                <h3 class="text-3xl font-bold text-white mt-1">$0.00</h3>
            </div>

            <!-- Active Funds -->
            <div class="glass p-6 rounded-3xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-2xl group-hover:bg-amber-500/20 transition-all"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-amber-500/20 rounded-lg text-amber-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-slate-400 px-2 py-1">{{ $activeFunds }} Active</span>
                </div>
                <p class="text-slate-400 text-sm font-medium uppercase tracking-wider">Investment Funds</p>
                <h3 class="text-3xl font-bold text-white mt-1">$0.00</h3>
            </div>

            <!-- Debts/Claims -->
            <div class="glass p-6 rounded-3xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-500/10 rounded-full blur-2xl group-hover:bg-rose-500/20 transition-all"></div>
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-rose-500/20 rounded-lg text-rose-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-xs font-medium text-rose-400 bg-rose-400/10 px-2 py-1 rounded-full">0 Overdue</span>
                </div>
                <p class="text-slate-400 text-sm font-medium uppercase tracking-wider">Outstanding Debts</p>
                <h3 class="text-3xl font-bold text-white mt-1">${{ number_format($totalDebts, 2) }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Chart Section -->
            <div class="lg:col-span-2 glass p-6 rounded-3xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Net Worth Performance</h3>
                    <select class="bg-slate-800 border-none text-slate-300 text-xs rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-indigo-500">
                        <option>Last 6 Months</option>
                        <option>Last 12 Months</option>
                    </select>
                </div>
                <div id="netWorthChart" class="w-full h-80"></div>
            </div>

            <!-- Recent Transactions -->
            <div class="glass p-6 rounded-3xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Recent Activity</h3>
                    <a href="#" class="text-indigo-400 text-xs hover:underline">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentTransactions as $transaction)
                        <div class="flex items-center p-3 rounded-2xl bg-slate-800/50 border border-slate-700/50 group hover:border-indigo-500/30 transition-all">
                            <div class="w-10 h-10 rounded-xl {{ $transaction->type == 'income' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }} flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($transaction->type == 'income')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    @endif
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-sm font-semibold text-white">{{ $transaction->description }}</p>
                                <p class="text-[10px] text-slate-500">{{ ucfirst($transaction->category) }} • {{ $transaction->wallet->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold {{ $transaction->type == 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $transaction->type == 'income' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </p>
                                <p class="text-[10px] text-slate-500">{{ $transaction->transaction_date->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-slate-500">
                            <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <p class="text-sm">No recent transactions found.</p>
                        </div>
                    @endforelse
                    
                    <!-- Mock Data for demo -->
                    <div class="flex items-center p-3 rounded-2xl bg-slate-800/50 border border-slate-700/50 group hover:border-indigo-500/30 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-semibold text-white">WHMCS Profit Sync</p>
                            <p class="text-[10px] text-slate-500">Commercial • Investment Fund A</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-emerald-400">+$2,400.00</p>
                            <p class="text-[10px] text-slate-500">Today, 2:45 PM</p>
                        </div>
                    </div>

                    <div class="flex items-center p-3 rounded-2xl bg-slate-800/50 border border-slate-700/50 group hover:border-indigo-500/30 transition-all">
                        <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-semibold text-white">Server Maintenance</p>
                            <p class="text-[10px] text-slate-500">Personal • Expenses</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-rose-400">-$450.00</p>
                            <p class="text-[10px] text-slate-500">Yesterday</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var options = {
                series: [{
                    name: 'Net Worth',
                    data: [150000, 162000, 158000, 175000, 210000, 245890]
                }],
                chart: {
                    type: 'area',
                    height: 320,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'Outfit, sans-serif',
                },
                colors: ['#6366f1'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [20, 100, 100, 100]
                    }
                },
                xaxis: {
                    categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#64748b' } }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748b' },
                        formatter: (value) => '$' + value.toLocaleString()
                    }
                },
                grid: {
                    borderColor: '#1e293b',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: true } }
                },
                tooltip: {
                    theme: 'dark',
                    y: { formatter: (value) => '$' + value.toLocaleString() }
                }
            };

            var chart = new ApexCharts(document.querySelector("#netWorthChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>
