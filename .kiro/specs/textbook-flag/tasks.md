# Implementation Plan: Textbook Flag Feature

## Overview

Implementasi fitur untuk menandai buku sebagai "Buku Paket Pelajaran" yang tidak dihitung dalam batas peminjaman siswa. Implementasi menggunakan PHP/Laravel dengan Livewire untuk komponen interaktif.

## Tasks

- [x] 1. Database Migration dan Model Update
  - [x] 1.1 Buat migration untuk menambahkan kolom `is_textbook` pada tabel books
    - Tambahkan kolom boolean `is_textbook` dengan default false
    - Posisi setelah kolom `price`
    - _Requirements: 1.1_
  - [x] 1.2 Update Book model dengan `is_textbook` attribute
    - Tambahkan ke `$fillable` array
    - Tambahkan ke `$casts` array sebagai boolean
    - _Requirements: 1.1_
  - [x] 1.3 Write property test untuk default is_textbook value
    - **Property 1: Default Textbook Value**
    - **Validates: Requirements 1.1**
  - [x] 1.4 Write property test untuk textbook persistence round-trip
    - **Property 2: Textbook Persistence Round-Trip**
    - **Validates: Requirements 1.4**

- [x] 2. Checkpoint - Jalankan migration dan pastikan tests pass
  - Jalankan `php artisan migrate`
  - Pastikan semua tests pass, tanyakan user jika ada pertanyaan

- [x] 3. Update BookController untuk handle is_textbook
  - [x] 3.1 Tambahkan validasi `is_textbook` di method store()
    - Tambahkan rule `'is_textbook' => 'boolean'`
    - _Requirements: 1.4_
  - [x] 3.2 Tambahkan validasi `is_textbook` di method update()
    - Tambahkan rule `'is_textbook' => 'boolean'`
    - _Requirements: 1.3, 1.4_

- [x] 4. Update Book Form View
  - [x] 4.1 Tambahkan checkbox is_textbook pada form buku
    - Tambahkan checkbox dengan label "Buku Paket Pelajaran"
    - Tambahkan helper text yang menjelaskan fungsi checkbox
    - Handle old() value dan existing book value
    - _Requirements: 1.2, 1.3_

- [x] 5. Update LoanService untuk exclude textbook dari loan limit
  - [x] 5.1 Tambahkan method getNonTextbookActiveLoansCount()
    - Query active loans yang book-nya bukan textbook
    - Return integer count
    - _Requirements: 2.1_
  - [x] 5.2 Modifikasi method validateLoanLimit()
    - Gunakan getNonTextbookActiveLoansCount() untuk validasi
    - _Requirements: 2.1, 2.2_
  - [x] 5.3 Modifikasi method canStudentBorrow()
    - Gunakan getNonTextbookActiveLoansCount() untuk check
    - _Requirements: 2.2_
  - [x] 5.4 Modifikasi method getRemainingLoanSlots()
    - Gunakan getNonTextbookActiveLoansCount() untuk kalkulasi
    - _Requirements: 2.4_
  - [x] 5.5 Write property test untuk loan count excludes textbooks
    - **Property 3: Loan Count Excludes Textbooks**
    - **Validates: Requirements 2.1, 2.3, 2.4**
  - [x] 5.6 Write property test untuk textbook borrowing at limit
    - **Property 4: Textbook Borrowing Allowed at Limit**
    - **Validates: Requirements 2.2**

- [x] 6. Checkpoint - Pastikan loan logic tests pass
  - Pastikan semua tests pass, tanyakan user jika ada pertanyaan

- [x] 7. Update BookTable Livewire Component
  - [x] 7.1 Tambahkan property filterTextbook dan query string
    - Tambahkan public property `$filterTextbook`
    - Tambahkan ke `$queryString` array
    - _Requirements: 3.2_
  - [x] 7.2 Tambahkan filter logic di render() method
    - Filter berdasarkan is_textbook value
    - Handle empty string untuk "semua buku"
    - _Requirements: 3.2_
  - [x] 7.3 Tambahkan updatingFilterTextbook() method untuk reset pagination
    - _Requirements: 3.2_
  - [x] 7.4 Update resetFilters() untuk include filterTextbook
    - _Requirements: 3.2_
  - [x] 7.5 Write property test untuk filter returns correct books
    - **Property 5: Filter Returns Correct Books**
    - **Validates: Requirements 3.2**

- [x] 8. Update Book Table View
  - [x] 8.1 Tambahkan filter dropdown untuk textbook status
    - Options: Semua Buku, Buku Paket, Buku Umum
    - Wire model ke filterTextbook
    - _Requirements: 3.2_
  - [x] 8.2 Tambahkan badge "Paket" untuk buku paket di tabel
    - Tampilkan badge dengan warna berbeda untuk buku paket
    - _Requirements: 3.1_

- [x] 9. Update Book Detail View
  - [x] 9.1 Tampilkan status buku paket di halaman detail buku
    - Tampilkan label/badge yang menunjukkan status textbook
    - _Requirements: 1.5_

- [x] 10. Update BookReportExport
  - [x] 10.1 Tambahkan kolom is_textbook di export
    - Tambahkan heading "Buku Paket"
    - Map value ke "Ya" atau "Tidak"
    - _Requirements: 3.3_

- [x] 11. Update BooksImport untuk handle is_textbook
  - [x] 11.1 Tambahkan mapping untuk kolom is_textbook di import
    - Handle nilai "Ya", "1", "true" sebagai true
    - Default ke false jika tidak ada atau nilai lain
    - _Requirements: 1.4_

- [x] 12. Update Download Template
  - [x] 12.1 Tambahkan kolom buku_paket di template CSV
    - Tambahkan ke columns array
    - Tambahkan contoh value di example array
    - _Requirements: 1.4_

- [x] 13. Final Checkpoint - Jalankan semua tests
  - Pastikan semua tests pass, tanyakan user jika ada pertanyaan

## Notes

- All tasks are required for comprehensive implementation
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties
- Unit tests validate specific examples and edge cases
