<x-app-layout>
    <x-slot name="header">
        Klasifikasi DDC
    </x-slot>

    <div class="card">
        <div class="flex items-center justify-between mb-6">
            <h3 class="card-header mb-0">Daftar Klasifikasi DDC</h3>
            <a href="{{ route('master.classifications.create') }}" class="btn btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Klasifikasi
            </a>
        </div>

        <livewire:master.classification-table />
    </div>
</x-app-layout>
