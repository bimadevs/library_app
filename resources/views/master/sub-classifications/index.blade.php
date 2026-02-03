<x-app-layout>
    <div x-data="{ 
        open: false, 
        isEdit: false, 
        formAction: '', 
        classificationId: '',
        subDdcCode: '',
        name: '',
        openCreate() {
            this.isEdit = false;
            this.formAction = '{{ route('master.sub-classifications.store') }}';
            this.classificationId = '';
            this.subDdcCode = '';
            this.name = '';
            this.open = true;
        },
        openEdit(event) {
            this.isEdit = true;
            this.formAction = event.detail.url;
            this.classificationId = event.detail.classification_id;
            this.subDdcCode = event.detail.sub_ddc_code;
            this.name = event.detail.name;
            this.open = true;
        }
    }" 
    x-init="
        @if($errors->any() && !old('_method'))
            openCreate();
            classificationId = '{{ old('classification_id') }}';
            subDdcCode = '{{ old('sub_ddc_code') }}';
            name = '{{ old('name') }}';
        @endif
    "
    @open-modal-edit.window="openEdit($event)"
    class="space-y-8">

        <x-slot name="header">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Sub Klasifikasi') }}
            </h2>
        </x-slot>

        <!-- Main Content -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden border border-slate-100">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Daftar Sub Klasifikasi</h3>
                        <p class="text-slate-500 mt-1">Kelola sub klasifikasi buku untuk pengelompokan yang lebih spesifik.</p>
                    </div>
                    
                    <button @click="openCreate()" 
                            class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white transition-all duration-200 bg-indigo-600 rounded-2xl hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 active:scale-95">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Sub Klasifikasi
                    </button>
                </div>

                <div class="bg-slate-50 rounded-2xl p-1">
                    <livewire:master.sub-classification-table />
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div x-show="open" 
             style="display: none;"
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
             
            <!-- Backdrop -->
            <div x-show="open"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
                 @click="open = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="open"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    
                    <!-- Header -->
                    <div class="bg-white px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 z-10">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800" id="modal-title" x-text="isEdit ? 'Edit Sub Klasifikasi' : 'Tambah Sub Klasifikasi Baru'"></h3>
                            <p class="text-sm text-slate-500 mt-1" x-text="isEdit ? 'Perbarui informasi sub klasifikasi.' : 'Tambahkan sub klasifikasi baru ke sistem.'"></p>
                        </div>
                        <button @click="open = false" class="rounded-full p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-colors">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-6 bg-slate-50/50">
                        <form :action="formAction" method="POST">
                            @csrf
                            <!-- Method Spoofing for Edit -->
                            <template x-if="isEdit">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="space-y-5">
                                <!-- Classification Select -->
                                <div class="space-y-2">
                                    <label for="classification_id" class="block text-sm font-semibold text-slate-700">Klasifikasi Induk <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <select name="classification_id" 
                                                id="classification_id" 
                                                x-model="classificationId"
                                                class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 transition-shadow hover:shadow-md focus:shadow-lg"
                                                required>
                                            <option value="">Pilih Klasifikasi</option>
                                            @foreach($classifications as $classification)
                                                <option value="{{ $classification->id }}">
                                                    {{ $classification->ddc_code }} - {{ $classification->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('classification_id')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Sub DDC Code -->
                                <div class="space-y-2">
                                    <label for="sub_ddc_code" class="block text-sm font-semibold text-slate-700">Kode Sub DDC <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" 
                                               name="sub_ddc_code" 
                                               id="sub_ddc_code" 
                                               x-model="subDdcCode"
                                               class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 transition-shadow hover:shadow-md focus:shadow-lg" 
                                               placeholder="Contoh: 000 - 009"
                                               required>
                                    </div>
                                    <p class="text-xs text-slate-500">Masukkan rentang kode sub DDC.</p>
                                    @error('sub_ddc_code')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Name -->
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-semibold text-slate-700">Nama Sub Klasifikasi <span class="text-rose-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" 
                                               name="name" 
                                               id="name" 
                                               x-model="name"
                                               class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 transition-shadow hover:shadow-md focus:shadow-lg" 
                                               placeholder="Contoh: Karya Umum"
                                               required>
                                    </div>
                                    @error('name')
                                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-8 flex flex-row-reverse gap-3">
                                <button type="submit" 
                                        class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 sm:w-auto transition-all active:scale-95">
                                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan'"></span>
                                </button>
                                <button type="button" 
                                        @click="open = false"
                                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
