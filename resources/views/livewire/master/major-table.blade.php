<div>
    <!-- Search and Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="relative flex-1 max-w-md">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari jurusan..."
                   class="form-input pl-11 w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 transition-all shadow-sm">
            <svg class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium text-slate-600">Show:</label>
            <select wire:model.live="perPage" class="form-select rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 py-2 pl-3 pr-8 shadow-sm text-sm">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 shadow-sm bg-white">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-xs uppercase font-bold text-slate-500 tracking-wider">
                <tr>
                    <th class="px-6 py-4 w-16 text-center border-b border-slate-100">No</th>
                    <th wire:click="sortBy('name')" class="px-6 py-4 cursor-pointer hover:bg-slate-100 transition-colors group border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            Nama Jurusan
                            <span class="text-slate-400 group-hover:text-indigo-600 transition-colors">
                                @if($sortField === 'name')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                                    </svg>
                                @endif
                            </span>
                        </div>
                    </th>
                    <th class="px-6 py-4 border-b border-slate-100">Jumlah Siswa</th>
                    <th class="px-6 py-4 text-center w-32 border-b border-slate-100">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($majors as $index => $major)
                    <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                        <td class="px-6 py-4 text-center text-slate-400 font-mono text-xs">{{ $majors->firstItem() + $index }}</td>
                        <td class="px-6 py-4">
                            <span class="font-semibold text-slate-800">{{ $major->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $major->students_count }} siswa
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" 
                                        x-data
                                        @click="$dispatch('open-modal-edit', { 
                                            url: '{{ route('master.majors.update', $major) }}', 
                                            name: '{{ addslashes($major->name) }}'
                                        })"
                                        class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all active:scale-95"
                                        title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('master.majors.destroy', $major) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all active:scale-95"
                                            title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-slate-50 p-4 rounded-full mb-3 shadow-inner">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <p class="text-base font-medium text-slate-600">Tidak ada data ditemukan</p>
                                <p class="text-sm text-slate-400 mt-1">
                                    @if($search)
                                        Tidak ada jurusan yang cocok dengan "{{ $search }}"
                                    @else
                                        Belum ada jurusan yang ditambahkan
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($majors->hasPages())
        <div class="mt-6">
            {{ $majors->links() }}
        </div>
    @endif
</div>