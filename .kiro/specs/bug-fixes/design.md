# Design Document

## Overview

Dokumen ini menjelaskan desain teknis untuk perbaikan bug dan penambahan fitur pada sistem perpustakaan sekolah. Perbaikan mencakup:

1. Fitur download template dan import siswa/buku
2. Fitur kenaikan kelas
3. Fitur generate barcode
4. Menu master sumber buku
5. Scan barcode untuk NIS siswa
6. Halaman peminjaman dan pengembalian buku

## Architecture

Sistem menggunakan arsitektur Laravel dengan Livewire untuk komponen interaktif. Perbaikan akan dilakukan pada:

- **Livewire Components**: StudentImport, BookImport, ClassPromotion, BarcodeGenerator, LoanForm, ReturnForm
- **Models**: BookSource (baru)
- **Controllers**: BookSourceController (baru)
- **Views**: Form siswa, form buku, sidebar menu

```
┌─────────────────────────────────────────────────────────────┐
│                      Blade Views                             │
├─────────────────────────────────────────────────────────────┤
│  Livewire Components                                         │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐        │
│  │StudentImport │ │ BookImport   │ │ClassPromotion│        │
│  └──────────────┘ └──────────────┘ └──────────────┘        │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐        │
│  │BarcodeGen    │ │ LoanForm     │ │ ReturnForm   │        │
│  └──────────────┘ └──────────────┘ └──────────────┘        │
├─────────────────────────────────────────────────────────────┤
│  Services                                                    │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐        │
│  │BarcodeService│ │ LoanService  │ │ReturnService │        │
│  └──────────────┘ └──────────────┘ └──────────────┘        │
├─────────────────────────────────────────────────────────────┤
│  Models                                                      │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐        │
│  │ BookSource   │ │   Student    │ │    Book      │        │
│  └──────────────┘ └──────────────┘ └──────────────┘        │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### 1. StudentImport Component Fix

**Masalah**: Method `downloadTemplate()` mengembalikan response stream yang tidak kompatibel dengan Livewire.

**Solusi**: Gunakan route terpisah untuk download template.

```php
// routes/web.php
Route::get('/students-import/template', [StudentController::class, 'downloadTemplate'])
    ->name('students.import.template');

// StudentController.php
public function downloadTemplate()
{
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="template_import_siswa.csv"',
    ];

    $columns = ['nis', 'nama', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'kelas', 'jurusan', 'jenis_kelamin', 'tahun_ajaran', 'telepon', 'maks_pinjam'];
    $example = ['1234567890', 'Nama Siswa', 'Jakarta', '2005-01-15', 'Jl. Contoh No. 123', 'X', 'TJKT', 'L', '2024/2025', '081234567890', '3'];

    $callback = function() use ($columns, $example) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);
        fputcsv($file, $example);
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
```

### 2. BookImport Component Fix

**Masalah**: Sama dengan StudentImport - method `downloadTemplate()` tidak berfungsi di Livewire.

**Solusi**: Gunakan route terpisah untuk download template.

```php
// routes/web.php
Route::get('/books-import/template', [BookController::class, 'downloadTemplate'])
    ->name('books.import.template');
```

### 3. ClassPromotion Component Fix

**Masalah**: Daftar siswa tidak muncul saat kelas asal dipilih.

**Analisis**: Komponen sudah benar, kemungkinan masalah pada:
- Data siswa tidak ada dengan `is_active = true`
- Relasi `class_id` tidak sesuai

**Solusi**: Pastikan query menggunakan relasi yang benar dan tambahkan debugging.

```php
// ClassPromotion.php - render method
public function render()
{
    $students = collect();
    if ($this->sourceClassId) {
        $students = Student::where('class_id', $this->sourceClassId)
            ->where('is_active', true)
            ->with(['major'])
            ->orderBy('name')
            ->get();
    }

    return view('livewire.student.class-promotion', [
        'classes' => SchoolClass::orderBy('name')->get(),
        'academicYears' => AcademicYear::orderBy('name', 'desc')->get(),
        'students' => $students,
        'selectedCount' => count($this->selectedStudents),
    ]);
}
```

### 4. BarcodeGenerator Component Fix

**Masalah**: Barcode tidak ter-generate saat buku dipilih.

**Analisis**: Komponen sudah benar, kemungkinan masalah pada:
- Livewire wire:click tidak terpanggil dengan benar
- JavaScript conflict

**Solusi**: Pastikan wire:click menggunakan format yang benar.

```html
<!-- barcode-generator.blade.php -->
<tr class="hover:bg-slate-50 cursor-pointer {{ $selectedBookId === $book->id ? 'bg-emerald-50' : '' }}"
    wire:click="selectBook({{ $book->id }})">
```

### 5. BookSource Master Data (New Feature)

**Model**: `BookSource`

```php
// app/Models/BookSource.php
class BookSource extends Model
{
    protected $fillable = ['name', 'description'];
}
```

**Migration**:

```php
Schema::create('book_sources', function (Blueprint $table) {
    $table->id();
    $table->string('name')->unique();
    $table->text('description')->nullable();
    $table->timestamps();
});
```

**Controller**: `BookSourceController` dengan CRUD operations.

**Relasi dengan Book**:

```php
// Book model
public function bookSource(): BelongsTo
{
    return $this->belongsTo(BookSource::class);
}
```

### 6. NIS Barcode Scanner (Student Form)

**Solusi**: Tambahkan input field dengan event listener untuk barcode scanner.

```html
<!-- students/form.blade.php -->
<div>
    <label for="nis" class="form-label">NIS <span class="text-red-500">*</span></label>
    <div class="flex gap-2">
        <input type="text" 
               name="nis" 
               id="nis" 
               class="form-input flex-1" 
               value="{{ old('nis', $student->nis) }}"
               placeholder="Scan barcode atau ketik NIS"
               autofocus
               required>
        <button type="button" 
                onclick="document.getElementById('nis').focus()"
                class="btn btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
            </svg>
        </button>
    </div>
</div>
```

### 7. LoanForm Component Fix

**Masalah**: Tombol cari siswa dan cari buku tidak berfungsi.

**Analisis**: Modal tidak terbuka karena `showStudentModal` dan `showBookModal` tidak berubah.

**Solusi**: Pastikan method `openStudentModal()` dan `openBookModal()` dipanggil dengan benar.

```php
// LoanForm.php
public function openStudentModal()
{
    $this->showStudentModal = true;
    $this->studentSearch = '';
}

public function openBookModal()
{
    $this->showBookModal = true;
    $this->bookSearch = '';
}
```

### 8. ReturnForm Component Fix

**Masalah**: Sama dengan LoanForm - modal tidak terbuka.

**Solusi**: Sama dengan LoanForm.

## Data Models

### BookSource Model (New)

```php
class BookSource extends Model
{
    protected $fillable = ['name', 'description'];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
```

### Book Model Update

```php
// Tambahkan relasi
public function bookSource(): BelongsTo
{
    return $this->belongsTo(BookSource::class);
}

// Update fillable
protected $fillable = [
    // ... existing fields
    'book_source_id', // tambahkan ini
];
```

### Migration untuk book_source_id

```php
Schema::table('books', function (Blueprint $table) {
    $table->foreignId('book_source_id')->nullable()->constrained('book_sources')->nullOnDelete();
});
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Import Summary Accuracy

*For any* import operation (student or book), the sum of imported, skipped, and failed counts SHALL equal the total number of rows processed.

**Validates: Requirements 2.5, 5.5**

### Property 2: Duplicate Detection

*For any* import row with a duplicate identifier (NIS for students, code for books), the system SHALL skip that row and include it in the skipped count.

**Validates: Requirements 2.3, 5.3**

### Property 3: Class Promotion Student Display

*For any* selected source class, the system SHALL display exactly all students where `class_id` equals the selected class and `is_active` is true.

**Validates: Requirements 3.1**

### Property 4: Class Promotion Update

*For any* set of selected students that are promoted, each student's `class_id` and `academic_year_id` SHALL be updated to the target values.

**Validates: Requirements 3.2**

### Property 5: Barcode Uniqueness

*For any* generated barcode, the barcode string SHALL be unique across all BookCopy records.

**Validates: Requirements 6.2**

### Property 6: Book Source Uniqueness

*For any* book source, the name SHALL be unique across all BookSource records.

**Validates: Requirements 7.4**

### Property 7: Student Search Filtering

*For any* search query in loan/return forms, the results SHALL only include students whose NIS or name contains the search string.

**Validates: Requirements 9.2**

### Property 8: Book Search Filtering

*For any* search query in loan form, the results SHALL only include available book copies whose barcode, book code, or book title contains the search string.

**Validates: Requirements 9.5**

### Property 9: Loan Creation

*For any* valid loan submission with a student and available book copy, a new Loan record SHALL be created with status 'active'.

**Validates: Requirements 9.7**

### Property 10: Return Processing

*For any* processed return, the loan status SHALL be updated to 'returned' or 'lost', and the book copy status SHALL be updated accordingly.

**Validates: Requirements 10.4**

## Error Handling

### Import Errors

- **File format error**: Display "File harus berformat Excel (.xlsx, .xls) atau CSV"
- **Missing required field**: Display "Kolom [field_name] wajib diisi pada baris [row_number]"
- **Invalid reference**: Display "[Entity] '[value]' tidak ditemukan pada baris [row_number]"
- **Duplicate entry**: Display "Data dengan [identifier] '[value]' sudah ada"

### Transaction Errors

- **Student not found**: Display "Siswa tidak ditemukan"
- **Book not available**: Display "Buku dengan barcode [barcode] tidak tersedia"
- **Loan limit exceeded**: Display "Siswa telah mencapai batas maksimal peminjaman"

## Testing Strategy

### Unit Tests

- Test import parsing logic
- Test barcode generation uniqueness
- Test fine calculation
- Test loan/return service methods

### Property-Based Tests

Menggunakan PHPUnit dengan data providers untuk property-based testing:

1. **Import Summary Property Test**: Generate random import data, verify summary counts
2. **Barcode Uniqueness Property Test**: Generate multiple barcodes, verify all unique
3. **Search Filtering Property Test**: Generate random search queries, verify filtering

### Integration Tests

- Test full import workflow
- Test loan creation workflow
- Test return processing workflow
- Test class promotion workflow
