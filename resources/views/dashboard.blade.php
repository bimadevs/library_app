<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-2xl font-bold text-slate-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <p class="text-slate-500 text-sm">Welcome back to the library management system.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
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

        <!-- Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <livewire:dashboard.due-today-table />
            <livewire:dashboard.unpaid-fines-table />
        </div>
    </div>
</x-app-layout>
