<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Peminjaman Buku</h2>
                <p class="text-sm text-slate-500 mt-1">Proses peminjaman buku untuk siswa</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <livewire:transaction.loan-form />
        </div>
    </div>
</x-app-layout>
