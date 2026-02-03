<x-app-layout>
    <div x-data="{ 
        open: false, 
        isEdit: false, 
        formAction: '', 
        publisherName: '',
        openCreate() {
            this.isEdit = false;
            this.formAction = '{{ route('master.publishers.store') }}';
            this.publisherName = '';
            this.open = true;
        },
        openEdit(event) {
            this.isEdit = true;
            this.formAction = event.detail.url;
            this.publisherName = event.detail.name;
            this.open = true;
        }
    }" 
    x-init="
        @if($errors->any() && !old('_method'))
            openCreate();
            publisherName = '{{ old('name') }}';
        @endif
    "
    @open-modal-edit.window="openEdit($event)"
    class="space-y-8">

        <x-slot name="header">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Penerbit') }}
            </h2>
        </x-slot>

        <!-- Main Content -->
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden border border-slate-100">
            <div class="p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                    <div>
                        <h3 class="text-2xl font-bold text-slate-800 tracking-tight">Daftar Penerbit</h3>
                        <p class="text-slate-500 mt-1">Kelola dan atur penerbit buku perpustakaan Anda.</p>
                    </div>
                    
                    <button @click="openCreate()" 
                            class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white transition-all duration-200 bg-indigo-600 rounded-2xl hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 active:scale-95">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Penerbit
                    </button>
                </div>

                <div class="bg-slate-50 rounded-2xl p-1">
                    <livewire:master.publisher-table />
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
                            <h3 class="text-xl font-bold text-slate-800" id="modal-title" x-text="isEdit ? 'Edit Penerbit' : 'Tambah Penerbit Baru'"></h3>
                            <p class="text-sm text-slate-500 mt-1" x-text="isEdit ? 'Perbarui informasi penerbit yang dipilih.' : 'Tambahkan penerbit baru ke database.'"></p>
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

                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-semibold text-slate-700">Nama Penerbit <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           x-model="publisherName"
                                           class="block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-3 px-4 transition-shadow hover:shadow-md focus:shadow-lg" 
                                           placeholder="Contoh: Gramedia Pustaka Utama"
                                           required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-xs text-slate-500 pl-1">Nama penerbit harus unik.</p>
                            </div>

                            <div class="mt-8 flex flex-row-reverse gap-3">
                                <button type="submit" 
                                        class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-200 hover:bg-indigo-700 sm:w-auto transition-all active:scale-95">
                                    <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Penerbit'"></span>
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