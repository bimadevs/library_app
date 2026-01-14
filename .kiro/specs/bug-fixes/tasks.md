# Implementation Plan: Bug Fixes dan Penambahan Fitur

## Overview

Implementasi perbaikan bug dan penambahan fitur untuk sistem perpustakaan sekolah. Fokus pada perbaikan fitur yang tidak berfungsi dan penambahan menu sumber buku.

## Tasks

- [x] 1. Perbaiki Download Template Import Siswa
  - Tambahkan route untuk download template di `routes/web.php`
  - Tambahkan method `downloadTemplate()` di `StudentController`
  - Update view `student-import.blade.php` untuk menggunakan link href ke route
  - _Requirements: 1.1, 1.2, 1.3_

- [x] 2. Perbaiki Download Template Import Buku
  - Tambahkan route untuk download template di `routes/web.php`
  - Tambahkan method `downloadTemplate()` di `BookController`
  - Update view `book-import.blade.php` untuk menggunakan link href ke route
  - _Requirements: 4.1, 4.2, 4.3_

- [x] 3. Perbaiki Fitur Kenaikan Kelas
  - Review dan perbaiki `ClassPromotion.php` Livewire component
  - Pastikan query siswa menggunakan relasi yang benar
  - Update view `class-promotion.blade.php` jika diperlukan
  - _Requirements: 3.1, 3.2, 3.3, 3.4_

- [x] 4. Perbaiki Fitur Generate Barcode
  - Review dan perbaiki `BarcodeGenerator.php` Livewire component
  - Pastikan wire:click berfungsi dengan benar
  - Update view `barcode-generator.blade.php` jika diperlukan
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

- [x] 5. Tambahkan Menu Sumber Buku (Book Source Master)
  - [x] 5.1 Buat migration untuk tabel `book_sources`
    - _Requirements: 7.1_
  - [x] 5.2 Buat model `BookSource`
    - _Requirements: 7.1_
  - [x] 5.3 Buat migration untuk menambahkan `book_source_id` ke tabel `books`
    - _Requirements: 7.3_
  - [x] 5.4 Update model `Book` dengan relasi ke `BookSource`
    - _Requirements: 7.3_
  - [x] 5.5 Buat controller `BookSourceController` dengan CRUD
    - _Requirements: 7.1, 7.4_
  - [x] 5.6 Buat Livewire component `BookSourceTable`
    - _Requirements: 7.1_
  - [x] 5.7 Buat views untuk book source (index, form)
    - _Requirements: 7.1, 7.2_
  - [x] 5.8 Update sidebar menu untuk menambahkan link Sumber Buku
    - _Requirements: 7.2_
  - [x] 5.9 Update form buku untuk menggunakan dropdown sumber buku
    - _Requirements: 7.3_
  - [x] 5.10 Tambahkan routes untuk book source
    - _Requirements: 7.1_

- [x] 6. Tambahkan Fitur Scan Barcode untuk NIS
  - Update view `students/form.blade.php` dengan input barcode scanner
  - Tambahkan icon dan styling untuk scanner input
  - _Requirements: 8.1, 8.2, 8.3_

- [x] 7. Perbaiki Halaman Peminjaman Buku
  - Review dan perbaiki `LoanForm.php` Livewire component
  - Pastikan method `openStudentModal()` dan `openBookModal()` berfungsi
  - Pastikan computed properties `students` dan `bookCopies` berfungsi
  - Update view `loan-form.blade.php` jika diperlukan
  - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7_

- [x] 8. Perbaiki Halaman Pengembalian Buku
  - Review dan perbaiki `ReturnForm.php` Livewire component
  - Pastikan method `openStudentModal()` berfungsi
  - Pastikan computed property `students` berfungsi
  - Update view `return-form.blade.php` jika diperlukan
  - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

- [x] 9. Checkpoint - Jalankan migrasi dan test manual
  - Jalankan `php artisan migrate`
  - Test semua fitur yang diperbaiki
  - Pastikan tidak ada error

- [x] 10. Write unit tests untuk fitur baru
  - Test BookSource CRUD operations
  - Test import functionality
  - _Requirements: 7.1, 7.4, 2.1-2.5, 5.1-5.5_

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Setiap task mereferensikan requirements spesifik untuk traceability
- Checkpoint memastikan validasi incremental
- Fokus pada perbaikan bug terlebih dahulu sebelum fitur baru
