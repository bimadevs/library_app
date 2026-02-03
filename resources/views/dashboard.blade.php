<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold text-slate-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <p class="text-slate-500 text-sm">Selamat datang kembali di sistem manajemen perpustakaan.</p>
        </div>
    </x-slot>

    <div class="space-y-6 pb-12">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <livewire:dashboard.statistics-card 
                title="Total Buku" 
                :value="$statistics['total_books']" 
                icon="book" 
                color="indigo" 
            />
            
            <livewire:dashboard.statistics-card 
                title="Judul Buku" 
                :value="$statistics['total_titles']" 
                icon="collection" 
                color="indigo" 
            />
            
            <livewire:dashboard.statistics-card 
                title="Siswa Aktif" 
                :value="$statistics['active_students']" 
                icon="users" 
                color="indigo" 
            />
            
            <livewire:dashboard.statistics-card 
                title="Peminjaman Aktif" 
                :value="$statistics['active_loans']" 
                icon="exchange" 
                color="indigo" 
            />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Monthly Loans Chart -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Tren Peminjaman ({{ date('Y') }})</h3>
                <div class="relative h-64 w-full">
                    <canvas id="monthlyLoansChart"></canvas>
                </div>
            </div>

            <!-- Top Categories Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Kategori Terpopuler</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="topCategoriesChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- Top Classes Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Kelas Paling Aktif</h3>
                <div class="relative h-60 w-full">
                    <canvas id="topClassesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <livewire:dashboard.due-today-table />
            <livewire:dashboard.unpaid-fines-table />
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Loans
            const monthlyCtx = document.getElementById('monthlyLoansChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Jumlah Peminjaman',
                        data: @json($statistics['monthly_loans']),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#6366f1',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { borderDash: [2, 4] }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });

            // Top Categories
            const categoriesCtx = document.getElementById('topCategoriesChart').getContext('2d');
            new Chart(categoriesCtx, {
                type: 'doughnut',
                data: {
                    labels: @json($statistics['popular_categories']->pluck('name')),
                    datasets: [{
                        data: @json($statistics['popular_categories']->pluck('loans_count')),
                        backgroundColor: [
                            '#6366f1', '#ec4899', '#10b981', '#f59e0b', '#8b5cf6',
                            '#3b82f6', '#ef4444', '#a855f7', '#14b8a6', '#f97316'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                usePointStyle: true,
                            }
                        }
                    },
                    cutout: '70%'
                }
            });

             // Top Classes
             const classesCtx = document.getElementById('topClassesChart').getContext('2d');
             new Chart(classesCtx, {
                type: 'bar',
                data: {
                    labels: @json($statistics['top_classes']->pluck('name')),
                    datasets: [{
                        label: 'Total Peminjaman',
                        data: @json($statistics['top_classes']->pluck('loans_count')),
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 },
                            grid: { borderDash: [2, 4] }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
