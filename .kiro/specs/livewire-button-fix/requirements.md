# Requirements Document

## Introduction

Dokumen ini menjelaskan perbaikan bug pada fitur Generate Barcode dan halaman Peminjaman Buku di aplikasi perpustakaan sekolah. Tombol-tombol pada kedua fitur tersebut tidak berfungsi karena konflik antara Alpine.js yang di-import manual dengan Alpine.js yang sudah termasuk dalam Livewire 3.

## Glossary

- **Livewire**: Framework Laravel untuk membangun komponen UI dinamis tanpa menulis JavaScript
- **Alpine.js**: Library JavaScript ringan untuk menambahkan interaktivitas pada halaman web
- **Barcode_Generator**: Komponen Livewire untuk generate barcode buku
- **Loan_Form**: Komponen Livewire untuk form peminjaman buku
- **wire:click**: Directive Livewire untuk menangani event klik

## Requirements

### Requirement 1: Perbaikan Konflik Alpine.js

**User Story:** Sebagai pengguna, saya ingin tombol-tombol di aplikasi berfungsi dengan benar, sehingga saya dapat menggunakan fitur generate barcode dan peminjaman buku.

#### Acceptance Criteria

1. WHEN pengguna mengklik tombol di halaman Generate Barcode, THE Barcode_Generator SHALL merespons dengan aksi yang sesuai
2. WHEN pengguna mengklik tombol "Cari Siswa" di halaman Peminjaman, THE Loan_Form SHALL membuka modal pencarian siswa
3. WHEN pengguna mengklik tombol "Cari Buku" di halaman Peminjaman, THE Loan_Form SHALL membuka modal pencarian buku
4. THE System SHALL menghapus import Alpine.js duplikat dari app.js karena Livewire 3 sudah menyertakan Alpine.js secara otomatis

### Requirement 2: Validasi Fungsionalitas Barcode Generator

**User Story:** Sebagai pustakawan, saya ingin dapat memilih buku dan generate barcode, sehingga saya dapat mencetak label barcode untuk koleksi buku.

#### Acceptance Criteria

1. WHEN pengguna mengklik baris buku di tabel, THE Barcode_Generator SHALL memilih buku tersebut dan menampilkan detailnya
2. WHEN pengguna mengklik tombol "Generate", THE Barcode_Generator SHALL membuat barcode baru sesuai jumlah yang diminta
3. WHEN pengguna mengklik checkbox barcode, THE Barcode_Generator SHALL menambahkan barcode ke daftar cetak
4. WHEN pengguna mengklik tombol "Cetak", THE Barcode_Generator SHALL menampilkan preview cetak barcode

### Requirement 3: Validasi Fungsionalitas Form Peminjaman

**User Story:** Sebagai pustakawan, saya ingin dapat mencari siswa dan buku untuk proses peminjaman, sehingga transaksi peminjaman dapat dilakukan dengan cepat.

#### Acceptance Criteria

1. WHEN pengguna mengetik di input pencarian siswa dalam modal, THE Loan_Form SHALL menampilkan hasil pencarian siswa yang sesuai
2. WHEN pengguna mengklik siswa dari hasil pencarian, THE Loan_Form SHALL memilih siswa tersebut dan menutup modal
3. WHEN pengguna mengetik di input pencarian buku dalam modal, THE Loan_Form SHALL menampilkan hasil pencarian buku yang tersedia
4. WHEN pengguna mengklik buku dari hasil pencarian, THE Loan_Form SHALL memilih buku tersebut dan menutup modal
5. WHEN pengguna scan barcode di input barcode, THE Loan_Form SHALL mencari dan memilih buku dengan barcode tersebut
