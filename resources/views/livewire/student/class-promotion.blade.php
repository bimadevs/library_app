<div>
    @if(!$showResults)
        <div class="space-y-6">
            <!-- Selection Form -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="sourceClassId" class="form-label">Kelas Asal <span class="text-red-500">*</span></label>
                    <select wire:model.live="sourceClassId" 
                            id="sourceClassId" 
                            class="form-select @error('sourceClassId') border-red-500 @enderror">
                        <option value="">Pilih Kelas Asal</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('sourceClassId')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="targetClassId" class="form-label">Kelas Tujuan <span class="text-red-500">*</span></label>
                    <select wire:model="targetClassId" 
                            id="targetClassId" 
                            class="form-select @error('targetClassId') border-red-500 @enderror">
                        <option value="">Pilih Kelas Tujuan</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('targetClassId')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="targetAcademicYearId" class="form-label">Tahun Ajaran Tujuan <span class="text-red-500">*</span></label>
                    <select wire:model="targetAcademicYearId" 
                            id="targetAcademicYearId" 
                            class="form-select @error('targetAcademicYearId') border-red-500 @enderror">
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}">{{ $academicYear->name }}</option>
                        @endforeach
                    </select>
                    @error('targetAcademicYearId')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Student List -->
            @if($sourceClassId)
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <input type="checkbox" 
                                   wire:model.live="selectAll"
                                   id="selectAll"
                                   class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <label for="selectAll" class="font-medium text-slate-700">
                                Pilih Semua ({{ $students->count() }} siswa)
                            </label>
                        </div>
                        <span class="text-sm text-slate-500">
                            {{ $selectedCount }} siswa dipilih
                        </span>
                    </div>

                    @if($students->count() > 0)
                        <div class="max-h-96 overflow-y-auto">
                            <table class="w-full">
                                <thead class="bg-slate-50 sticky top-0">
                                    <tr>
                                        <th class="w-12 px-4 py-2"></th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-slate-600">NIS</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Nama</th>
                                        <th class="px-4 py-2 text-left text-sm font-medium text-slate-600">Jurusan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($students as $student)
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-2">
                                                <input type="checkbox" 
                                                       wire:model.live="selectedStudents"
                                                       value="{{ $student->id }}"
                                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                            </td>
                                            <td class="px-4 py-2 font-mono text-sm">{{ $student->nis }}</td>
                                            <td class="px-4 py-2 font-medium">{{ $student->name }}</td>
                                            <td class="px-4 py-2 text-slate-600">{{ $student->major->name ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Tidak ada siswa aktif di kelas ini
                        </div>
                    @endif
                </div>

                @error('selectedStudents')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror

                @if($selectedCount > 0)
                    <div class="flex items-center gap-3">
                        <button wire:click="confirmPromotion" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            Naikkan {{ $selectedCount }} Siswa
                        </button>
                    </div>
                @endif
            @else
                <div class="bg-slate-50 rounded-lg p-8 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                    Pilih kelas asal untuk melihat daftar siswa
                </div>
            @endif
        </div>

        <!-- Confirmation Modal -->
        @if($showConfirmation)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-800">Konfirmasi Kenaikan Kelas</h3>
                        </div>
                        
                        <p class="text-slate-600 mb-4">
                            Anda akan menaikkan <strong>{{ $selectedCount }} siswa</strong> ke kelas baru. 
                            Tindakan ini akan mengubah data kelas dan tahun ajaran siswa.
                        </p>

                        <div class="bg-slate-50 rounded-lg p-3 mb-4 text-sm">
                            <p><strong>Kelas Tujuan:</strong> {{ $classes->find($targetClassId)?->name ?? '-' }}</p>
                            <p><strong>Tahun Ajaran:</strong> {{ $academicYears->find($targetAcademicYearId)?->name ?? '-' }}</p>
                        </div>

                        <div class="flex items-center gap-3 justify-end">
                            <button wire:click="cancelPromotion" class="btn btn-secondary">Batal</button>
                            <button wire:click="executePromotion" class="btn btn-primary">
                                Ya, Naikkan Kelas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- Results -->
        <div class="space-y-6">
            <!-- Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-emerald-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ count($promotionResults['promoted']) }}</p>
                    <p class="text-sm text-emerald-700">Siswa Berhasil Dinaikkan</p>
                </div>
                <div class="bg-amber-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ count($promotionResults['skipped']) }}</p>
                    <p class="text-sm text-amber-700">Siswa Dilewati</p>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-blue-800">
                    <strong>Kelas Tujuan:</strong> {{ $promotionResults['target_class'] }} |
                    <strong>Tahun Ajaran:</strong> {{ $promotionResults['target_academic_year'] }}
                </p>
            </div>

            <!-- Promoted Students -->
            @if(count($promotionResults['promoted']) > 0)
                <div class="border border-emerald-200 rounded-lg overflow-hidden">
                    <div class="bg-emerald-50 px-4 py-3 border-b border-emerald-200">
                        <h4 class="font-medium text-emerald-800">Siswa Berhasil Dinaikkan ({{ count($promotionResults['promoted']) }})</h4>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-emerald-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">No</th>
                                    <th class="px-4 py-2 text-left">NIS</th>
                                    <th class="px-4 py-2 text-left">Nama</th>
                                    <th class="px-4 py-2 text-left">Dari Kelas</th>
                                    <th class="px-4 py-2 text-left">Ke Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-100">
                                @foreach($promotionResults['promoted'] as $index => $item)
                                    <tr>
                                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 font-mono">{{ $item['student']->nis }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $item['student']->name }}</td>
                                        <td class="px-4 py-2">{{ $item['from_class'] }}</td>
                                        <td class="px-4 py-2 text-emerald-600 font-medium">{{ $item['to_class'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Skipped Students -->
            @if(count($promotionResults['skipped']) > 0)
                <div class="border border-amber-200 rounded-lg overflow-hidden">
                    <div class="bg-amber-50 px-4 py-3 border-b border-amber-200">
                        <h4 class="font-medium text-amber-800">Siswa Dilewati ({{ count($promotionResults['skipped']) }})</h4>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-amber-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-2 text-left">No</th>
                                    <th class="px-4 py-2 text-left">NIS</th>
                                    <th class="px-4 py-2 text-left">Nama</th>
                                    <th class="px-4 py-2 text-left">Alasan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-100">
                                @foreach($promotionResults['skipped'] as $index => $item)
                                    <tr>
                                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 font-mono">{{ $item['student']->nis }}</td>
                                        <td class="px-4 py-2 font-medium">{{ $item['student']->name }}</td>
                                        <td class="px-4 py-2 text-amber-700">{{ $item['reason'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                <button wire:click="resetPromotion" class="btn btn-secondary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                    </svg>
                    Kenaikan Kelas Lagi
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Lihat Daftar Siswa
                </a>
            </div>
        </div>
    @endif
</div>
