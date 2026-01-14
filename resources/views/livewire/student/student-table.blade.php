<div>
    <!-- Search and Filters -->
    <div class="mb-4 space-y-4">
        <div class="flex flex-wrap items-center gap-4">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari NIS atau nama siswa..."
                       class="form-input pl-10 w-full">
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
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="filterClass" class="form-select">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterMajor" class="form-select">
                <option value="">Semua Jurusan</option>
                @foreach($majors as $major)
                    <option value="{{ $major->id }}">{{ $major->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterAcademicYear" class="form-select">
                <option value="">Semua Tahun Ajaran</option>
                @foreach($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus" class="form-select">
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>

            @if($search || $filterClass || $filterMajor || $filterAcademicYear || $filterStatus !== '')
                <button wire:click="resetFilters" class="btn btn-secondary text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-16">No</th>
                    <th wire:click="sortBy('nis')" class="cursor-pointer hover:bg-slate-100">
                        <div class="flex items-center gap-1">
                            NIS
                            @if($sortField === 'nis')
                                <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th wire:click="sortBy('name')" class="cursor-pointer hover:bg-slate-100">
                        <div class="flex items-center gap-1">
                            Nama
                            @if($sortField === 'name')
                                <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? '' : 'rotate-180' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            @endif
                        </div>
                    </th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                    <th>Pinjaman Aktif</th>
                    <th>Status</th>
                    <th class="w-32 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    <tr>
                        <td class="text-slate-500">{{ $students->firstItem() + $index }}</td>
                        <td class="font-mono text-sm">{{ $student->nis }}</td>
                        <td class="font-medium text-slate-800">{{ $student->name }}</td>
                        <td>{{ $student->class->name ?? '-' }}</td>
                        <td>{{ $student->major->name ?? '-' }}</td>
                        <td>
                            @if($student->active_loans_count > 0)
                                <span class="badge badge-info">{{ $student->active_loans_count }} buku</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td>
                            @if($student->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-warning">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('students.show', $student) }}" 
                                   class="p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('students.edit', $student) }}" 
                                   class="p-2 text-slate-600 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                   title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('students.destroy', $student) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
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
                        <td colspan="8" class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            @if($search || $filterClass || $filterMajor || $filterAcademicYear || $filterStatus !== '')
                                Tidak ada siswa yang cocok dengan filter
                            @else
                                Belum ada data siswa
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($students->hasPages())
        <div class="mt-4 pt-4 border-t border-slate-200">
            {{ $students->links() }}
        </div>
    @endif
</div>
