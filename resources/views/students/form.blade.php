<x-app-layout>
    <x-slot name="header">
        {{ $student->exists ? 'Edit Siswa' : 'Tambah Siswa' }}
    </x-slot>

    <div class="card max-w-4xl">
        <h3 class="card-header">{{ $student->exists ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h3>

        <form action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}" method="POST">
            @csrf
            @if($student->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NIS -->
                <div>
                    <label for="nis" class="form-label">NIS <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" 
                               name="nis" 
                               id="nis" 
                               class="form-input pr-10 @error('nis') border-red-500 @enderror" 
                               value="{{ old('nis', $student->nis) }}"
                               placeholder="Scan atau ketik NIS"
                               autofocus
                               required>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Dapat menggunakan barcode scanner</p>
                    @error('nis')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-input @error('name') border-red-500 @enderror" 
                           value="{{ old('name', $student->name) }}"
                           placeholder="Nama lengkap siswa"
                           required>
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Birth Place -->
                <div>
                    <label for="birth_place" class="form-label">Tempat Lahir <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="birth_place" 
                           id="birth_place" 
                           class="form-input @error('birth_place') border-red-500 @enderror" 
                           value="{{ old('birth_place', $student->birth_place) }}"
                           placeholder="Tempat lahir"
                           required>
                    @error('birth_place')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Birth Date -->
                <div>
                    <label for="birth_date" class="form-label">Tanggal Lahir <span class="text-red-500">*</span></label>
                    <input type="date" 
                           name="birth_date" 
                           id="birth_date" 
                           class="form-input @error('birth_date') border-red-500 @enderror" 
                           value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}"
                           required>
                    @error('birth_date')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label for="gender" class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" 
                            id="gender" 
                            class="form-select @error('gender') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="form-label">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="text" 
                           name="phone" 
                           id="phone" 
                           class="form-input @error('phone') border-red-500 @enderror" 
                           value="{{ old('phone', $student->phone) }}"
                           placeholder="Nomor telepon"
                           required>
                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Class -->
                <div>
                    <label for="class_id" class="form-label">Kelas <span class="text-red-500">*</span></label>
                    <select name="class_id" 
                            id="class_id" 
                            class="form-select @error('class_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Major -->
                <div>
                    <label for="major_id" class="form-label">Jurusan <span class="text-red-500">*</span></label>
                    <select name="major_id" 
                            id="major_id" 
                            class="form-select @error('major_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Jurusan</option>
                        @foreach($majors as $major)
                            <option value="{{ $major->id }}" {{ old('major_id', $student->major_id) == $major->id ? 'selected' : '' }}>
                                {{ $major->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('major_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Year -->
                <div>
                    <label for="academic_year_id" class="form-label">Tahun Ajaran <span class="text-red-500">*</span></label>
                    <select name="academic_year_id" 
                            id="academic_year_id" 
                            class="form-select @error('academic_year_id') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" {{ old('academic_year_id', $student->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                {{ $academicYear->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year_id')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Loan -->
                <div>
                    <label for="max_loan" class="form-label">Maks. Peminjaman <span class="text-red-500">*</span></label>
                    <input type="number" 
                           name="max_loan" 
                           id="max_loan" 
                           class="form-input @error('max_loan') border-red-500 @enderror" 
                           value="{{ old('max_loan', $student->max_loan ?? 3) }}"
                           min="1"
                           max="10"
                           required>
                    @error('max_loan')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-500 mt-1">Jumlah maksimal buku yang dapat dipinjam</p>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="form-label">Alamat <span class="text-red-500">*</span></label>
                    <textarea name="address" 
                              id="address" 
                              rows="3"
                              class="form-input @error('address') border-red-500 @enderror" 
                              placeholder="Alamat lengkap"
                              required>{{ old('address', $student->address) }}</textarea>
                    @error('address')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active -->
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" 
                               name="is_active" 
                               id="is_active" 
                               value="1"
                               class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                               {{ old('is_active', $student->is_active ?? true) ? 'checked' : '' }}>
                        <label for="is_active" class="text-sm text-slate-700">Siswa aktif</label>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-slate-200">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ $student->exists ? 'Simpan Perubahan' : 'Simpan' }}
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
