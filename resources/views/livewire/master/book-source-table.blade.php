<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search -->
    <div class="mb-4">
        <div class="relative max-w-md">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari sumber buku..."
                   class="form-input pl-10 w-full">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200">
                    <th class="px-4 py-3 text-left">
                        <button wire:click="sortBy('name')" class="flex items-center gap-1 font-semibold text-slate-600 hover:text-slate-800">
                            Nama
                            @if($sortField === 'name')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($sortDirection === 'asc')
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    @endif
                                </svg>
                            @endif
                        </button>
                    </th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600">Deskripsi</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Jumlah Buku</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($bookSources as $bookSource)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium">{{ $bookSource->name }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $bookSource->description ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="badge badge-info">{{ $bookSource->books_count }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('master.book-sources.edit', $bookSource) }}" 
                                   class="text-blue-600 hover:text-blue-800" 
                                   title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button wire:click="delete({{ $bookSource->id }})" 
                                        wire:confirm="Apakah Anda yakin ingin menghapus sumber buku ini?"
                                        class="text-red-600 hover:text-red-800" 
                                        title="Hapus">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                            @if($search)
                                Tidak ada sumber buku yang cocok dengan pencarian "{{ $search }}"
                            @else
                                Belum ada data sumber buku
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($bookSources->hasPages())
        <div class="mt-4">
            {{ $bookSources->links() }}
        </div>
    @endif
</div>
