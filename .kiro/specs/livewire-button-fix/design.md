# Design Document: Livewire Button Fix

## Overview

Dokumen ini menjelaskan solusi teknis untuk memperbaiki bug tombol yang tidak berfungsi pada fitur Generate Barcode dan Form Peminjaman. Masalah utama adalah konflik Alpine.js yang menyebabkan directive `wire:click` Livewire tidak berfungsi.

## Architecture

### Root Cause Analysis

```
┌─────────────────────────────────────────────────────────────┐
│                    Current State (Broken)                    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  resources/js/app.js                                         │
│  ┌─────────────────────────────────────────┐                │
│  │ import Alpine from 'alpinejs';          │ ◄── Alpine #1  │
│  │ window.Alpine = Alpine;                 │                │
│  │ Alpine.start();                         │                │
│  └─────────────────────────────────────────┘                │
│                                                              │
│  @livewireScripts (in layout)                               │
│  ┌─────────────────────────────────────────┐                │
│  │ Livewire 3 includes Alpine.js           │ ◄── Alpine #2  │
│  │ automatically bundled                   │                │
│  └─────────────────────────────────────────┘                │
│                                                              │
│  Result: Two Alpine instances conflict,                      │
│          wire:click events not handled properly              │
└─────────────────────────────────────────────────────────────┘
```

### Solution Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Fixed State                               │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  resources/js/app.js                                         │
│  ┌─────────────────────────────────────────┐                │
│  │ import './bootstrap';                   │                │
│  │ // Alpine.js removed - Livewire 3       │                │
│  │ // provides it automatically            │                │
│  └─────────────────────────────────────────┘                │
│                                                              │
│  @livewireScripts (in layout)                               │
│  ┌─────────────────────────────────────────┐                │
│  │ Livewire 3 includes Alpine.js           │ ◄── Single    │
│  │ automatically bundled                   │     Alpine    │
│  └─────────────────────────────────────────┘                │
│                                                              │
│  Result: Single Alpine instance,                             │
│          wire:click events work correctly                    │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Affected Files

1. **resources/js/app.js** - Hapus import Alpine.js
2. **package.json** - Alpine.js dependency bisa dihapus (opsional)

### Livewire Components (No Changes Required)

Komponen-komponen berikut sudah benar implementasinya, hanya perlu fix Alpine.js:

1. `App\Livewire\Book\BarcodeGenerator`
   - Methods: `selectBook()`, `generateBarcodes()`, `togglePrintSelection()`, `showPrint()`
   
2. `App\Livewire\Transaction\LoanForm`
   - Methods: `openStudentModal()`, `openBookModal()`, `selectStudent()`, `selectBookCopy()`, `scanBarcode()`

## Data Models

Tidak ada perubahan pada data models.

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Barcode Selection Toggle Idempotence

*For any* barcode and initial selection state, toggling the selection twice SHALL return to the original state.

**Validates: Requirements 2.3**

### Property 2: Barcode Generation Count Correctness

*For any* book with available slots and valid quantity n, generating barcodes SHALL create exactly n new book copies.

**Validates: Requirements 2.2**

### Property 3: Search Results Match Query

*For any* search query string, all returned results (students or books) SHALL contain the query string in their searchable fields (NIS, name, barcode, title, or code).

**Validates: Requirements 3.1, 3.3**

### Property 4: Selection Updates State Correctly

*For any* valid student or book selection, the selection method SHALL set the corresponding selected property and close the modal.

**Validates: Requirements 3.2, 3.4**

### Property 5: Barcode Scan Selects Correct Book

*For any* valid barcode string that exists in the database with status 'available', scanning SHALL select the book copy with that exact barcode.

**Validates: Requirements 3.5**

## Error Handling

### Alpine.js Conflict Detection

Jika masih ada masalah setelah fix:
1. Check browser console untuk error Alpine.js
2. Pastikan tidak ada import Alpine.js lain di file JavaScript
3. Pastikan `@livewireScripts` ada di layout

### Livewire Component Errors

1. Method tidak ditemukan - pastikan method public
2. Property tidak reactive - gunakan `wire:model.live` untuk real-time updates

## Testing Strategy

### Unit Tests

Unit tests akan memverifikasi:
- Livewire component methods berfungsi dengan benar
- State changes sesuai ekspektasi
- Computed properties mengembalikan data yang benar

### Property-Based Tests

Property-based tests menggunakan Pest dengan plugin `pestphp/pest-plugin-faker` untuk:
- Generate random inputs untuk search queries
- Verify toggle idempotence
- Verify barcode generation count

### Test Configuration

- Framework: Pest PHP
- Minimum iterations: 100 per property test
- Tag format: **Feature: livewire-button-fix, Property {number}: {property_text}**

### Manual Testing Checklist

Setelah fix diterapkan, verifikasi manual:

1. **Barcode Generator**
   - [ ] Klik baris buku → buku terpilih
   - [ ] Klik tombol Generate → barcode dibuat
   - [ ] Klik checkbox → barcode ditambah ke daftar cetak
   - [ ] Klik Cetak → preview muncul

2. **Form Peminjaman**
   - [ ] Klik "Cari Siswa" → modal terbuka
   - [ ] Ketik di search → hasil muncul
   - [ ] Klik siswa → siswa terpilih, modal tertutup
   - [ ] Klik "Cari Buku" → modal terbuka
   - [ ] Ketik di search → hasil muncul
   - [ ] Klik buku → buku terpilih, modal tertutup
   - [ ] Scan barcode → buku terpilih
