<x-app-layout>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <livewire:dashboard.statistics-card 
            title="Total Buku" 
            :value="$statistics['total_books']" 
            icon="book" 
            color="emerald" 
        />
        
        <livewire:dashboard.statistics-card 
            title="Judul Buku" 
            :value="$statistics['total_titles']" 
            icon="collection" 
            color="blue" 
        />
        
        <livewire:dashboard.statistics-card 
            title="Siswa Aktif" 
            :value="$statistics['active_students']" 
            icon="users" 
            color="amber" 
        />
        
        <livewire:dashboard.statistics-card 
            title="Peminjaman Aktif" 
            :value="$statistics['active_loans']" 
            icon="exchange" 
            color="purple" 
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <livewire:dashboard.due-today-table />
        <livewire:dashboard.unpaid-fines-table />
    </div>
</x-app-layout>
