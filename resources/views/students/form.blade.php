<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm text-slate-500">
            <a href="{{ route('students.index') }}" class="hover:text-indigo-600 transition-colors">Daftar Siswa</a>
            <svg class="w-4 h-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-slate-800 font-medium">{{ $student->exists ? 'Edit Siswa' : 'Tambah Siswa Baru' }}</span>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto pb-12 px-4 sm:px-6 lg:px-8 py-8">
        <form action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if($student->exists)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Photo (Sticky) -->
                <div class="lg:col-span-4 xl:col-span-3 lg:sticky lg:top-8 space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <label class="block text-sm font-bold text-slate-900 mb-4">Foto Siswa</label>
                        
                        <div x-data="{ photoName: null, photoPreview: null }" class="space-y-4">
                            <!-- Photo Preview Area -->
                            <div class="relative aspect-square w-full rounded-2xl overflow-hidden bg-slate-50 border-2 border-dashed border-slate-300 hover:border-indigo-400 hover:bg-slate-100 transition-all duration-200 group cursor-pointer"
                                 @click="document.getElementById('photo').click()">
                                
                                <!-- Image Preview -->
                                <div class="absolute inset-0 z-0">
                                    <div class="w-full h-full" x-show="!photoPreview">
                                        @if($student->exists && $student->photo)
                                            <img src="{{ Storage::url($student->photo) }}" alt="Foto" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                                <svg class="w-12 h-12 mb-2 group-hover:text-indigo-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-xs font-semibold uppercase tracking-wider group-hover:text-indigo-600 transition-colors">Upload Foto</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- New Photo Preview -->
                                    <div class="w-full h-full bg-cover bg-no-repeat bg-center" x-show="photoPreview" style="display: none;"
                                         :style="'background-image: url(\'' + photoPreview + '\');'">
                                    </div>
                                </div>

                                <!-- Overlay on Hover -->
                                <div class="absolute inset-0 z-10 bg-black/0 group-hover:bg-black/10 transition-colors duration-200 flex items-center justify-center">
                                    <div class="bg-white/90 backdrop-blur-sm rounded-full p-2 opacity-0 group-hover:opacity-100 transform scale-90 group-hover:scale-100 transition-all duration-200 shadow-sm">
                                        <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </div>
                                </div>

                                <input type="file" name="photo" id="photo" class="hidden"
                                       accept="image/*"
                                       @change="
                                           const file = $event.target.files[0];
                                           if (file) {
                                               photoName = file.name;
                                               const reader = new FileReader();
                                               reader.onload = (e) => { photoPreview = e.target.result; };
                                               reader.readAsDataURL(file);
                                           }
                                       ">
                            </div>
                            
                            <p class="text-xs text-slate-500 text-center leading-relaxed">
                                Format: JPG, PNG. Max: 2MB.<br>
                                Disarankan rasio 1:1.
                            </p>
                            @error('photo')
                                <p class="text-xs text-rose-600 text-center font-medium bg-rose-50 py-1 px-2 rounded-md border border-rose-100">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3">
                        <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-xl shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 hover:shadow-indigo-200 hover:shadow-lg active:scale-95">
                            <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ $student->exists ? 'Simpan Perubahan' : 'Simpan Siswa Baru' }}
                        </button>
                        <a href="{{ route('students.index') }}" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-medium text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500">
                            Batal & Kembali
                        </a>
                    </div>
                </div>

                <!-- Right Column: Form Fields -->
                <div class="lg:col-span-8 xl:col-span-9 space-y-6">
                    
                    <!-- Section 1: Biodata -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Biodata Siswa</h3>
                                <p class="text-sm text-slate-500">Informasi pribadi siswa.</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nis" class="block text-sm font-medium text-slate-700 mb-1">NIS <span class="text-rose-500">*</span></label>
                                <input type="text" name="nis" id="nis" 
                                       class="block w-full py-2.5 px-3 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm font-mono transition-all duration-200"
                                       value="{{ old('nis', $student->nis) }}" placeholder="Nomor Induk Siswa" required autofocus>
                                @error('nis') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" id="name" 
                                       class="block w-full py-2.5 px-3 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                                       value="{{ old('name', $student->name) }}" placeholder="Nama Lengkap" required>
                                @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="birth_place" class="block text-sm font-medium text-slate-700 mb-1">Tempat Lahir <span class="text-rose-500">*</span></label>
                                <input type="text" name="birth_place" id="birth_place" 
                                       class="block w-full py-2.5 px-3 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                                       value="{{ old('birth_place', $student->birth_place) }}" placeholder="Kota Lahir" required>
                                @error('birth_place') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="birth_date" class="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                <input type="date" name="birth_date" id="birth_date" 
                                       class="block w-full py-2.5 px-3 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                                       value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}" required>
                                @error('birth_date') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="gender" class="block text-sm font-medium text-slate-700 mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                                <select name="gender" id="gender" 
                                        class="block w-full py-2.5 px-3 text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="male" {{ old('gender', $student->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">No. Telepon <span class="text-rose-500">*</span></label>
                                <input type="text" name="phone" id="phone" 
                                       class="block w-full py-2.5 px-3 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                                       value="{{ old('phone', $student->phone) }}" placeholder="08..." required>
                                @error('phone') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap <span class="text-rose-500">*</span></label>
                                <textarea name="address" id="address" rows="3" 
                                          class="block w-full py-2.5 px-3 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                                          placeholder="Jalan, RT/RW, Kelurahan, Kecamatan..." required>{{ old('address', $student->address) }}</textarea>
                                @error('address') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Akademik -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
                        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Informasi Akademik</h3>
                                <p class="text-sm text-slate-500">Data kelas dan status siswa.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-slate-700 mb-1">Kelas <span class="text-rose-500">*</span></label>
                                <select name="class_id" id="class_id" 
                                        class="block w-full py-2.5 px-3 text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200" required>
                                    <option value="">Pilih Kelas</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="major_id" class="block text-sm font-medium text-slate-700 mb-1">Jurusan <span class="text-rose-500">*</span></label>
                                <select name="major_id" id="major_id" 
                                        class="block w-full py-2.5 px-3 text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->id }}" {{ old('major_id', $student->major_id) == $major->id ? 'selected' : '' }}>
                                            {{ $major->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('major_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-slate-700 mb-1">Tahun Ajaran <span class="text-rose-500">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" 
                                        class="block w-full py-2.5 px-3 text-slate-900 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200" required>
                                    <option value="">Pilih Tahun Ajaran</option>
                                    @foreach($academicYears as $academicYear)
                                        <option value="{{ $academicYear->id }}" {{ old('academic_year_id', $student->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                            {{ $academicYear->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="max_loan" class="block text-sm font-medium text-slate-700 mb-1">Maks. Peminjaman <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="max_loan" id="max_loan" 
                                           class="block w-full py-2.5 px-3 pr-16 text-slate-900 placeholder-slate-400 bg-white border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 sm:text-sm transition-all duration-200"
                                           value="{{ old('max_loan', $student->max_loan ?? 3) }}" min="1" max="10" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-500 text-sm bg-slate-50 border-l border-slate-200 px-3 rounded-r-lg my-px mr-px">
                                        Buku
                                    </div>
                                </div>
                                @error('max_loan') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2 pt-4 border-t border-slate-100">
                                <label class="flex items-start gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors group">
                                    <div class="flex items-center h-6">
                                        <input type="checkbox" name="is_active" value="1" 
                                               class="w-5 h-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                               {{ old('is_active', $student->is_active ?? true) ? 'checked' : '' }}>
                                    </div>
                                    <div>
                                        <span class="block font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">Status Aktif</span>
                                        <span class="block text-sm text-slate-500">Centang untuk menandai siswa ini sebagai siswa aktif.</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</x-app-layout>
