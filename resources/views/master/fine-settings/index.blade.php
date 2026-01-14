<x-app-layout>
    <x-slot name="header">
        Pengaturan Denda
    </x-slot>

    <div class="card max-w-2xl">
        <h3 class="card-header">Konfigurasi Denda Perpustakaan</h3>
        <p class="text-sm text-slate-500 mb-6">Atur nominal denda keterlambatan dan denda buku hilang. Pengaturan ini akan berlaku untuk transaksi baru.</p>

        <form action="{{ route('master.fine-settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Daily Fine -->
                <div class="p-4 bg-slate-50 rounded-lg">
                    <label for="daily_fine" class="form-label">Denda Keterlambatan per Hari <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                        <input type="number" 
                               name="daily_fine" 
                               id="daily_fine" 
                               class="form-input pl-10 @error('daily_fine') border-red-500 @enderror" 
                               value="{{ old('daily_fine', $fineSetting->daily_fine ?? 0) }}"
                               min="0"
                               step="100"
                               required>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Nominal denda yang dikenakan untuk setiap hari keterlambatan pengembalian buku.</p>
                    @error('daily_fine')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lost Book Fine Type -->
                <div class="p-4 bg-slate-50 rounded-lg">
                    <label class="form-label">Jenis Denda Buku Hilang <span class="text-red-500">*</span></label>
                    <div class="mt-2 space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" 
                                   name="lost_fine_type" 
                                   value="flat" 
                                   class="mt-1 text-emerald-600 focus:ring-emerald-500"
                                   {{ old('lost_fine_type', $fineSetting->lost_fine_type ?? 'flat') === 'flat' ? 'checked' : '' }}>
                            <div>
                                <span class="font-medium text-slate-700">Nominal Tetap</span>
                                <p class="text-xs text-slate-500">Denda buku hilang menggunakan nominal tetap yang ditentukan di bawah.</p>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="radio" 
                                   name="lost_fine_type" 
                                   value="book_price" 
                                   class="mt-1 text-emerald-600 focus:ring-emerald-500"
                                   {{ old('lost_fine_type', $fineSetting->lost_fine_type ?? 'flat') === 'book_price' ? 'checked' : '' }}>
                            <div>
                                <span class="font-medium text-slate-700">Sesuai Harga Buku</span>
                                <p class="text-xs text-slate-500">Denda buku hilang mengikuti harga buku yang tercatat di sistem.</p>
                            </div>
                        </label>
                    </div>
                    @error('lost_fine_type')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lost Book Fine Amount -->
                <div class="p-4 bg-slate-50 rounded-lg" id="lost_book_fine_container">
                    <label for="lost_book_fine" class="form-label">Nominal Denda Buku Hilang (Tetap) <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                        <input type="number" 
                               name="lost_book_fine" 
                               id="lost_book_fine" 
                               class="form-input pl-10 @error('lost_book_fine') border-red-500 @enderror" 
                               value="{{ old('lost_book_fine', $fineSetting->lost_book_fine ?? 0) }}"
                               min="0"
                               step="1000"
                               required>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Nominal ini digunakan jika jenis denda buku hilang adalah "Nominal Tetap".</p>
                    @error('lost_book_fine')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-slate-200">
                <button type="submit" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const flatRadio = document.querySelector('input[value="flat"]');
            const bookPriceRadio = document.querySelector('input[value="book_price"]');
            const lostBookFineContainer = document.getElementById('lost_book_fine_container');

            function toggleLostBookFine() {
                if (bookPriceRadio.checked) {
                    lostBookFineContainer.style.opacity = '0.5';
                    lostBookFineContainer.querySelector('input').disabled = false;
                } else {
                    lostBookFineContainer.style.opacity = '1';
                    lostBookFineContainer.querySelector('input').disabled = false;
                }
            }

            flatRadio.addEventListener('change', toggleLostBookFine);
            bookPriceRadio.addEventListener('change', toggleLostBookFine);
            toggleLostBookFine();
        });
    </script>
</x-app-layout>
