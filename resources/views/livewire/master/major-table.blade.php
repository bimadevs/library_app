<div>
    <!-- Search and Filter -->
    <div class="flex items-center justify-between mb-4">
        <div class="relative">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari jurusan..."
                   class="form-input pl-10 w-64">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-slate-600">Tampilkan:</label>
            <select wire:model.live="perPage" class="form-select w-20">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-16">No</th>
                    <th wire:click="sortBy('code')" class="cursor-pointer hover:bg-slate-100">
                        <div class="flex items-center gap-1">
                            Kode
                            @if($sortField === 'code')
                                <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th wire:click="sortBy('name')" class="cursor-pointer hover:bg-slate-100">
                        <div class="flex items-center gap-1">
                            Nama Jurusan
                            @if($sortField === 'name')
                                <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th>Jumlah Siswa</th>
                    <th class="w-32 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($majors as $index => $major)
                    <tr>
                        <td class="text-slate-500">{{ $majors->firstItem() + $index }}</td>
                        <td><span class="badge badge-info">{{ $major->code }}</span></td>
                        <td class="font-medium text-slate-800">{{ $major->name }}</td>
                        <td>{{ $major->students_count }} siswa</td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('master.majors.edit', $major) }}" 
                                   class="p-2 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('master.majors.destroy', $major) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
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
                        <td colspan="5" class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            @if($search)
                                Tidak ada jurusan yang cocok dengan pencarian "{{ $search }}"
                            @else
                                Belum ada data jurusan
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($majors->hasPages())
        <div class="mt-4 pt-4 border-t border-slate-200">
            {{ $majors->links() }}
        </div>
    @endif
</div>
