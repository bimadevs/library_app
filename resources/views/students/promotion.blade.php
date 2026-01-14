<x-app-layout>
    <x-slot name="header">
        Kenaikan Kelas
    </x-slot>

    <div class="card max-w-5xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="card-header mb-0">Kenaikan Kelas Siswa</h3>
                <p class="text-sm text-slate-500 mt-1">Naikkan siswa dari satu kelas ke kelas berikutnya</p>
            </div>
            <a href="{{ route('students.index') }}" class="btn btn-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <livewire:student.class-promotion />
    </div>
</x-app-layout>
