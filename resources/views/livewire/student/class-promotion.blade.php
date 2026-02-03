<div class="space-y-6">
    @if(!$showResults)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Header -->
            <div class="p-6 md:p-8 border-b border-slate-100">
                <h2 class="text-xl font-bold text-slate-900">Proses Kenaikan Kelas</h2>
                <p class="text-slate-500 mt-1">Pindahkan siswa dari satu kelas ke kelas lain secara massal.</p>
            </div>
            
            <div class="p-6 md:p-8 space-y-8">
                <!-- Step 1: Configuration -->
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-slate-200 ml-3"></div>
                    <div class="relative pl-10">
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold flex items-center justify-center ring-4 ring-white">1</div>
                        
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Konfigurasi Kelas</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-slate-50/50 rounded-2xl border border-slate-200">
                            <div>
                                <label for="sourceClassId" class="block text-sm font-semibold text-slate-700 mb-2">Kelas Asal <span class="text-rose-500">*</span></label>
                                <select wire:model.live="sourceClassId" 
                                        id="sourceClassId" 
                                        class="block w-full py-2.5 px-3 text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200">
                                    <option value="">Pilih Kelas Asal</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('sourceClassId') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="targetClassId" class="block text-sm font-semibold text-slate-700 mb-2">Kelas Tujuan <span class="text-rose-500">*</span></label>
                                <select wire:model="targetClassId" 
                                        id="targetClassId" 
                                        class="block w-full py-2.5 px-3 text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200">
                                    <option value="">Pilih Kelas Tujuan</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('targetClassId') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="targetAcademicYearId" class="block text-sm font-semibold text-slate-700 mb-2">Tahun Ajaran Tujuan <span class="text-rose-500">*</span></label>
                                <select wire:model="targetAcademicYearId" 
                                        id="targetAcademicYearId" 
                                        class="block w-full py-2.5 px-3 text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200">
                                    <option value="">Pilih Tahun Ajaran</option>
                                    @foreach($academicYears as $academicYear)
                                        <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                                    @endforeach
                                </select>
                                @error('targetAcademicYearId') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Select Students -->
                <div class="relative">
                    <div class="absolute left-0 top-0 bottom-0 w-px bg-slate-200 ml-3"></div>
                    <div class="relative pl-10">
                        <div class="absolute left-0 top-1 w-6 h-6 rounded-full {{ $sourceClassId ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-500' }} text-xs font-bold flex items-center justify-center ring-4 ring-white">2</div>
                        
                        <h3 class="text-lg font-bold text-slate-900 mb-4">Pilih Siswa</h3>
                        
                        @if($sourceClassId)
                            <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-white hover:shadow-sm transition-all cursor-pointer">
                                            <input type="checkbox" 
                                                   wire:model.live="selectAll"
                                                   id="selectAll"
                                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5">
                                            <span class="font-medium text-slate-700 select-none">
                                                Pilih Semua <span class="text-slate-500 font-normal">({{ $students->count() }} siswa)</span>
                                            </span>
                                        </label>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $selectedCount }} siswa dipilih
                                    </span>
                                </div>

                                @if($students->count() > 0)
                                    <div class="max-h-[500px] overflow-y-auto">
                                        <table class="w-full text-left text-sm">
                                            <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                                                <tr>
                                                    <th class="w-16 px-6 py-3 bg-slate-50"></th>
                                                    <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider bg-slate-50">NIS</th>
                                                    <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider bg-slate-50">Nama Lengkap</th>
                                                    <th class="px-6 py-3 font-bold text-slate-500 uppercase tracking-wider bg-slate-50">Jurusan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100 bg-white">
                                                @foreach($students as $student)
                                                    <tr class="hover:bg-slate-50 transition-colors cursor-pointer group {{ in_array($student->id, $selectedStudents) ? 'bg-indigo-50/30' : '' }}" 
                                                        wire:click="$dispatch('toggle-student', { id: {{ $student->id }} })">
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center justify-center" wire:click.stop>
                                                                <input type="checkbox" 
                                                                       wire:model.live="selectedStudents"
                                                                       value="{{ $student->id }}"
                                                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-5 h-5 cursor-pointer">
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 font-mono text-slate-600 group-hover:text-indigo-600 transition-colors">{{ $student->nis }}</td>
                                                        <td class="px-6 py-4 font-medium text-slate-900">{{ $student->name }}</td>
                                                        <td class="px-6 py-4 text-slate-500">{{ $student->major->name ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center py-16 text-slate-500 bg-slate-50/30">
                                        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 shadow-sm border border-slate-100">
                                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                        </div>
                                        <p class="font-medium text-slate-900">Tidak ada siswa aktif</p>
                                        <p class="text-sm">Kelas ini belum memiliki siswa aktif.</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="border-2 border-dashed border-slate-300 rounded-2xl p-12 text-center text-slate-500 bg-slate-50/50">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-medium text-slate-900">Pilih Kelas Asal Terlebih Dahulu</p>
                                <p class="text-slate-500">Daftar siswa akan muncul setelah Anda memilih kelas asal.</p>
                            </div>
                        @endif
                        
                        @error('selectedStudents')
                            <div class="mt-4 p-4 rounded-xl bg-rose-50 border border-rose-100 flex items-start gap-3">
                                <svg class="w-5 h-5 text-rose-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-rose-700 font-medium">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="bg-slate-50 px-8 py-5 border-t border-slate-200 flex items-center justify-end">
                <button wire:click="confirmPromotion" 
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-xl shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-indigo-200 hover:shadow-indigo-300 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                        {{ $selectedCount == 0 ? 'disabled' : '' }}>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    Proses Kenaikan Kelas ({{ $selectedCount }})
                </button>
            </div>
        </div>

        <!-- Confirmation Modal -->
        @if($showConfirmation)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="cancelPromotion"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    
                    <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start gap-5">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-xl leading-6 font-bold text-slate-900" id="modal-title">
                                        Konfirmasi Kenaikan Kelas
                                    </h3>
                                    <div class="mt-4">
                                        <p class="text-sm text-slate-600 mb-6 leading-relaxed">
                                            Anda akan memproses kenaikan kelas untuk <strong class="text-slate-900">{{ $selectedCount }} siswa</strong>.
                                            Pastikan konfigurasi di bawah ini sudah benar.
                                        </p>
                                        
                                        <div class="bg-slate-50 rounded-xl p-4 space-y-3 text-sm border border-slate-200">
                                            <div class="flex justify-between items-center pb-2 border-b border-slate-200/60">
                                                <span class="text-slate-500 font-medium">Kelas Tujuan</span>
                                                <span class="font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded">{{ $classes->find($targetClassId)?->name ?? '-' }}</span>
                                            </div>
                                            <div class="flex justify-between items-center pt-1">
                                                <span class="text-slate-500 font-medium">Tahun Ajaran</span>
                                                <span class="font-bold text-slate-900">{{ $academicYears->find($targetAcademicYearId)?->name ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse gap-3 border-t border-slate-100">
                            <button type="button" 
                                    wire:click="executePromotion"
                                    class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm shadow-indigo-200 hover:shadow-indigo-300 transition-all duration-200">
                                Ya, Lanjutkan
                            </button>
                            <button type="button" 
                                    wire:click="cancelPromotion"
                                    class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2.5 bg-white text-base font-bold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm hover:border-slate-400 transition-all duration-200">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @else
        <!-- Results View -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-8">
                <div class="text-center mb-10">
                    <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm ring-8 ring-emerald-50">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Proses Selesai!</h2>
                    <p class="text-slate-500">Kenaikan kelas telah berhasil diproses.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 text-center shadow-sm">
                        <p class="text-4xl font-bold text-emerald-600 mb-2">{{ count($promotionResults['promoted']) }}</p>
                        <p class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Berhasil</p>
                    </div>
                    <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 text-center shadow-sm">
                        <p class="text-4xl font-bold text-amber-600 mb-2">{{ count($promotionResults['skipped']) }}</p>
                        <p class="text-xs font-bold text-amber-700 uppercase tracking-wider">Dilewati</p>
                    </div>
                    <div class="bg-indigo-50 rounded-2xl p-6 border border-indigo-100 text-center shadow-sm">
                        <p class="text-xl font-bold text-indigo-700 mb-1 line-clamp-1">{{ $promotionResults['target_class'] }}</p>
                        <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">{{ $promotionResults['target_academic_year'] }}</p>
                    </div>
                </div>

                <div class="space-y-8">
                    @if(count($promotionResults['promoted']) > 0)
                        <div class="border border-emerald-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-emerald-50 px-6 py-4 border-b border-emerald-200 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <h4 class="font-bold text-emerald-900">Daftar Siswa Berhasil</h4>
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-white sticky top-0 shadow-sm z-10">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider bg-emerald-50/50 backdrop-blur-sm">NIS</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider bg-emerald-50/50 backdrop-blur-sm">Nama</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider bg-emerald-50/50 backdrop-blur-sm">Dari</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-emerald-700 uppercase tracking-wider bg-emerald-50/50 backdrop-blur-sm">Ke</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-emerald-100 bg-white">
                                        @foreach($promotionResults['promoted'] as $item)
                                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                                <td class="px-6 py-3 font-mono text-slate-600">{{ $item['student']->nis }}</td>
                                                <td class="px-6 py-3 font-medium text-slate-900">{{ $item['student']->name }}</td>
                                                <td class="px-6 py-3 text-slate-500">{{ $item['from_class'] }}</td>
                                                <td class="px-6 py-3 text-emerald-700 font-bold bg-emerald-50/30">{{ $item['to_class'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(count($promotionResults['skipped']) > 0)
                        <div class="border border-amber-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-amber-50 px-6 py-4 border-b border-amber-200 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                                <h4 class="font-bold text-amber-900">Daftar Siswa Dilewati</h4>
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-white sticky top-0 shadow-sm z-10">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-amber-700 uppercase tracking-wider bg-amber-50/50 backdrop-blur-sm">NIS</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-amber-700 uppercase tracking-wider bg-amber-50/50 backdrop-blur-sm">Nama</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-amber-700 uppercase tracking-wider bg-amber-50/50 backdrop-blur-sm">Alasan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-amber-100 bg-white">
                                        @foreach($promotionResults['skipped'] as $item)
                                            <tr class="hover:bg-amber-50/30 transition-colors">
                                                <td class="px-6 py-3 font-mono text-slate-600">{{ $item['student']->nis }}</td>
                                                <td class="px-6 py-3 font-medium text-slate-900">{{ $item['student']->name }}</td>
                                                <td class="px-6 py-3 text-amber-700 font-medium italic">{{ $item['reason'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4 border-t border-slate-100 pt-8">
                    <button wire:click="resetPromotion" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 hover:shadow-md">
                        <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Proses Kelas Lain
                    </button>
                    <a href="{{ route('students.index') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-xl shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-indigo-200 hover:shadow-indigo-300 active:scale-95">
                        Selesai & Kembali
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
