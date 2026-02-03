<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Laporan Pengunjung') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Filter Section -->
        <div class="card">
            <div class="card-body">
                <form action="{{ route('reports.visitors') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-4">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-input w-full">
                    </div>
                    <div class="md:col-span-4">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-input w-full">
                    </div>
                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit" class="btn btn-primary flex-1 justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </button>
                        
                        <!-- Export Buttons (Placeholder for future) -->
                        <button type="button" class="btn btn-secondary flex-1 justify-center opacity-50 cursor-not-allowed" title="Segera Hadir">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="stat-card bg-emerald-50 border-emerald-200">
                <div class="stat-icon bg-emerald-100 text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value text-emerald-700">{{ number_format($totalVisitors) }}</div>
                    <div class="stat-label text-emerald-600">Total Pengunjung</div>
                </div>
            </div>
            <div class="stat-card bg-blue-50 border-blue-200">
                <div class="stat-icon bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value text-blue-700">{{ $avgVisitors }}</div>
                    <div class="stat-label text-blue-600">Rata-rata / Hari</div>
                </div>
            </div>
            <div class="stat-card bg-purple-50 border-purple-200">
                <div class="stat-icon bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="stat-value text-purple-700">{{ count($dailyTrend) }}</div>
                    <div class="stat-label text-purple-600">Hari Buka</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Line Chart: Tren Harian -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tren Kunjungan Harian</h3>
                </div>
                <div class="card-body">
                    <div id="dailyTrendChart" class="h-80"></div>
                </div>
            </div>

            <!-- Bar Chart: Top Kelas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Top 10 Kelas Teraktif</h3>
                </div>
                <div class="card-body">
                    <div id="classChart" class="h-80"></div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Kunjungan</h3>
            </div>
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="w-12 text-center">No</th>
                                <th>Tanggal & Jam</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentVisitors as $index => $visitor)
                                <tr class="hover:bg-slate-50">
                                    <td class="text-center">{{ $recentVisitors->firstItem() + $index }}</td>
                                    <td>{{ $visitor->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="font-medium text-slate-800">{{ $visitor->student->name }}</td>
                                    <td>{{ $visitor->student->class->name ?? '-' }}</td>
                                    <td>{{ $visitor->student->major->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-12">
                                        <div class="flex flex-col items-center justify-center text-slate-500">
                                            <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            <p>Tidak ada data kunjungan pada periode ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $recentVisitors->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Daily Trend Chart
        const dailyOptions = {
            series: [{
                name: "Pengunjung",
                data: @json($dailyTrend->pluck('total'))
            }],
            chart: {
                height: 320,
                type: 'area',
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Figtree, sans-serif'
            },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            xaxis: {
                categories: @json($dailyTrend->pluck('date')),
                type: 'datetime',
                labels: {
                    format: 'dd MMM'
                }
            },
            colors: ['#10b981'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
            }
        };
        new ApexCharts(document.querySelector("#dailyTrendChart"), dailyOptions).render();

        // Class Chart
        const classOptions = {
            series: [{
                name: "Total Kunjungan",
                data: @json($visitorsByClass->pluck('total'))
            }],
            chart: {
                height: 320,
                type: 'bar',
                toolbar: { show: false },
                fontFamily: 'Figtree, sans-serif'
            },
            plotOptions: {
                bar: { 
                    borderRadius: 4, 
                    horizontal: true,
                    barHeight: '60%'
                }
            },
            dataLabels: { enabled: true },
            xaxis: {
                categories: @json($visitorsByClass->pluck('class_name'))
            },
            colors: ['#3b82f6'],
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
            }
        };
        new ApexCharts(document.querySelector("#classChart"), classOptions).render();
    </script>
    @endpush
</x-app-layout>
