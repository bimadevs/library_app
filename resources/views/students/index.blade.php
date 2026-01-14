<x-app-layout>
    <x-slot name="header">
        Daftar Siswa
    </x-slot>

    <div class="card">
        <div class="flex items-center justify-between mb-6">
            <h3 class="card-header mb-0">Daftar Siswa</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('students.import') }}" class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Import Excel
                </a>
                <a href="{{ route('students.create') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Siswa
                </a>
            </div>
        </div>

        <livewire:student.student-table />
    </div>
</x-app-layout>
