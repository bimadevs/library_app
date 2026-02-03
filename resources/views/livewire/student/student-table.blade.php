<div class="space-y-6">
    <!-- Filters & Toolbar -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center mb-6">
            <!-- Search -->
            <div class="relative w-full lg:w-96">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Cari NIS atau nama siswa..."
                       class="block w-full py-2.5 pl-10 pr-3 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors duration-200">
            </div>

            <!-- Per Page -->
            <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                <label class="text-sm text-slate-600 font-medium">Tampilkan:</label>
                <select wire:model.live="perPage" class="block w-24 py-2 pl-3 pr-10 text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors duration-200">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 pt-5 border-t border-slate-100">
            <select wire:model.live="filterClass" class="block w-full py-2 pl-3 pr-10 text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors duration-200">
                <option value="">Semua Kelas</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterMajor" class="block w-full py-2 pl-3 pr-10 text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors duration-200">
                <option value="">Semua Jurusan</option>
                @foreach($majors as $major)
                    <option value="{{ $major->id }}">{{ $major->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterAcademicYear" class="block w-full py-2 pl-3 pr-10 text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors duration-200">
                <option value="">Semua Tahun Ajaran</option>
                @foreach($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus" class="block w-full py-2 pl-3 pr-10 text-slate-700 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-colors duration-200">
                <option value="">Semua Status</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
            </select>

            @if($search || $filterClass || $filterMajor || $filterAcademicYear || $filterStatus !== '')
                <button wire:click="resetFilters" class="col-span-full md:col-span-1 md:col-start-4 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-lg shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset Filter
                </button>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden relative min-h-[400px]">
        <div wire:loading.flex class="absolute inset-0 z-10 bg-white/50 backdrop-blur-sm flex items-center justify-center">
            <div class="flex items-center gap-3 px-4 py-3 bg-white rounded-lg shadow-lg border border-slate-100">
                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium text-slate-700">Memuat data...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Foto</th>
                        <th wire:click="sortBy('nis')" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                            <div class="flex items-center gap-1">
                                NIS
                                <span class="transition-colors duration-200 {{ $sortField === 'nis' ? 'text-indigo-600' : 'text-slate-300 group-hover:text-slate-400' }}">
                                    @if($sortField === 'nis' && $sortDirection === 'desc')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </span>
                            </div>
                        </th>
                        <th wire:click="sortBy('name')" class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider cursor-pointer hover:bg-slate-100 transition-colors group select-none">
                            <div class="flex items-center gap-1">
                                Nama
                                <span class="transition-colors duration-200 {{ $sortField === 'name' ? 'text-indigo-600' : 'text-slate-300 group-hover:text-slate-400' }}">
                                    @if($sortField === 'name' && $sortDirection === 'desc')
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    @else
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                    @endif
                                </span>
                            </div>
                        </th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Pinjaman</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($students as $index => $student)
                        <tr class="hover:bg-slate-50 transition-colors duration-200 group">
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $students->firstItem() + $index }}</td>
                            <td class="px-6 py-4">
                                <div class="w-10 h-10 bg-slate-100 rounded-full overflow-hidden shadow-sm border border-slate-200 ring-2 ring-transparent group-hover:ring-indigo-500/20 transition-all duration-200">
                                    <img src="{{ $student->photo_url }}" alt="Foto" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm text-slate-600 group-hover:text-indigo-600 transition-colors">{{ $student->nis }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-slate-900">{{ $student->name }}</div>
                                <div class="text-xs text-slate-500 lg:hidden">{{ $student->class->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $student->class->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $student->major->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($student->active_loans_count > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                        {{ $student->active_loans_count }} buku
                                    </span>
                                @else
                                    <span class="text-slate-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($student->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-50 text-slate-600 ring-1 ring-inset ring-slate-600/20">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <a href="{{ route('students.show', $student) }}" 
                                       class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200"
                                       title="Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" 
                                       class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all duration-200"
                                       title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <button type="button" 
                                            wire:click="delete({{ $student->id }})"
                                            wire:confirm="Apakah Anda yakin ingin menghapus data siswa ini?"
                                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all duration-200"
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
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="bg-slate-50 p-4 rounded-full w-20 h-20 mx-auto mb-4 flex items-center justify-center border border-slate-100">
                                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-900">Tidak ada data siswa</h3>
                                <p class="text-slate-500 mt-1 max-w-sm mx-auto">
                                    @if($search || $filterClass || $filterMajor || $filterAcademicYear || $filterStatus !== '')
                                        Tidak ada siswa yang cocok dengan filter pencarian Anda.
                                    @else
                                        Mulai dengan menambahkan siswa baru atau import data dari Excel.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $students->links() }}
    </div>
</div>
