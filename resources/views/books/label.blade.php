<x-app-layout>
    <x-slot name="header">
        Generate Label Buku
    </x-slot>

    <div class="card">
        <div class="flex items-center justify-between mb-6">
            <h3 class="card-header mb-0">Generate Label Buku</h3>
            <a href="{{ route('books.index') }}" class="btn btn-secondary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>

        <livewire:book.book-label-generator />
    </div>
</x-app-layout>
