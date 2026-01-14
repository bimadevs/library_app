<x-app-layout>
    <x-slot name="header">
        {{ $title ?? 'Halaman' }}
    </x-slot>

    <div class="card">
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-lg font-medium text-slate-600 mb-2">{{ $title ?? 'Halaman' }}</h3>
            <p class="text-slate-400">Fitur ini akan segera tersedia.</p>
        </div>
    </div>
</x-app-layout>
