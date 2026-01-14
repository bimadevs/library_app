# Design Document: School Library Management System

## Overview

Sistem Manajemen Perpustakaan Sekolah adalah aplikasi web berbasis Laravel 12 dengan MySQL sebagai database. Aplikasi ini menggunakan arsitektur MVC dengan Livewire untuk interaktivitas frontend tanpa perlu menulis JavaScript kompleks.

### Technology Stack

- **Backend Framework**: Laravel 12
- **Database**: MySQL
- **Frontend**: Blade Templates + Livewire 3 + Tailwind CSS
- **Excel Import/Export**: Maatwebsite Laravel Excel 3.1
- **PDF Generation**: DomPDF (barryvdh/laravel-dompdf)
- **Barcode Generation**: picqer/php-barcode-generator
- **Authentication**: Laravel Breeze (simple auth scaffolding)
- **Testing**: PHPUnit + Pest PHP

### Key Design Decisions

1. **Livewire untuk UI Interaktif**: Dipilih karena memungkinkan pembuatan komponen dinamis (search, filter, modal) tanpa menulis JavaScript kompleks
2. **Laravel Excel**: Library standar untuk import/export Excel di Laravel dengan validasi built-in
3. **Soft Deletes**: Semua model utama menggunakan soft delete untuk menjaga integritas data historis
4. **UUID untuk Barcode**: Menggunakan UUID untuk identifikasi unik setiap copy buku

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        Presentation Layer                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │   Blade     │  │  Livewire   │  │     Tailwind CSS        │  │
│  │  Templates  │  │ Components  │  │                         │  │
│  └─────────────┘  └─────────────┘  └─────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                       Application Layer                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │ Controllers │  │  Services   │  │      Form Requests      │  │
│  └─────────────┘  └─────────────┘  └─────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Domain Layer                             │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐  │
│  │   Models    │  │   Imports   │  │        Exports          │  │
│  │ (Eloquent)  │  │   (Excel)   │  │     (Excel/PDF)         │  │
│  └─────────────┘  └─────────────┘  └─────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Infrastructure Layer                        │
│  ┌─────────────────────────────────────────────────────────────┐│
│  │                     MySQL Database                          ││
│  └─────────────────────────────────────────────────────────────┘│
└─────────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Controllers

```
app/Http/Controllers/
├── DashboardController.php          # Dashboard statistics & insights
├── Master/
│   ├── AcademicYearController.php   # CRUD tahun ajaran
│   ├── ClassController.php          # CRUD kelas
│   ├── MajorController.php          # CRUD jurusan
│   ├── ClassificationController.php # CRUD klasifikasi DDC
│   ├── SubClassificationController.php # CRUD sub klasifikasi
│   ├── CategoryController.php       # CRUD kategori buku
│   ├── PublisherController.php      # CRUD penerbit
│   └── FineSettingController.php    # Pengaturan denda
├── StudentController.php            # CRUD siswa + import
├── BookController.php               # CRUD buku + import + barcode
├── Transaction/
│   ├── LoanController.php           # Peminjaman buku
│   └── ReturnController.php         # Pengembalian buku
└── Report/
    ├── LoanReportController.php     # Laporan peminjaman
    ├── FineReportController.php     # Laporan denda
    └── BookReportController.php     # Laporan buku
```

### Livewire Components

```
app/Livewire/
├── Dashboard/
│   ├── StatisticsCard.php           # Widget statistik
│   ├── DueTodayTable.php            # Tabel jatuh tempo hari ini
│   └── UnpaidFinesTable.php         # Tabel denda belum lunas
├── Student/
│   ├── StudentTable.php             # Tabel siswa dengan search/filter
│   ├── StudentForm.php              # Form input siswa
│   ├── StudentImport.php            # Import siswa dari Excel
│   └── ClassPromotion.php           # Kenaikan kelas
├── Book/
│   ├── BookTable.php                # Tabel buku dengan search/filter
│   ├── BookForm.php                 # Form input buku
│   ├── BookImport.php               # Import buku dari Excel
│   └── BarcodeGenerator.php         # Generate & print barcode
├── Transaction/
│   ├── LoanForm.php                 # Form peminjaman
│   ├── StudentSearch.php            # Modal search siswa
│   ├── BookSearch.php               # Modal search buku
│   └── ReturnForm.php               # Form pengembalian
└── Master/
    └── DataTable.php                # Generic datatable untuk master data
```

### Service Classes

```
app/Services/
├── DashboardService.php             # Business logic dashboard
├── StudentService.php               # Business logic siswa
├── BookService.php                  # Business logic buku
├── LoanService.php                  # Business logic peminjaman
├── ReturnService.php                # Business logic pengembalian
├── FineCalculatorService.php        # Kalkulasi denda
├── BarcodeService.php               # Generate barcode
└── ReportService.php                # Generate laporan
```

### Import Classes

```
app/Imports/
├── StudentsImport.php               # Import siswa dari Excel
└── BooksImport.php                  # Import buku dari Excel
```

### Export Classes

```
app/Exports/
├── LoanReportExport.php             # Export laporan peminjaman
├── FineReportExport.php             # Export laporan denda
└── BookReportExport.php             # Export laporan buku
```

## Data Models

### Entity Relationship Diagram

```
┌──────────────────┐     ┌──────────────────┐     ┌──────────────────┐
│  academic_years  │     │     classes      │     │      majors      │
├──────────────────┤     ├──────────────────┤     ├──────────────────┤
│ id               │     │ id               │     │ id               │
│ name             │     │ name             │     │ code             │
│ is_active        │     │ level            │     │ name             │
│ timestamps       │     │ timestamps       │     │ timestamps       │
└──────────────────┘     └──────────────────┘     └──────────────────┘
         │                        │                        │
         └────────────────────────┼────────────────────────┘
                                  │
                                  ▼
                    ┌──────────────────────────┐
                    │        students          │
                    ├──────────────────────────┤
                    │ id                       │
                    │ nis (unique)             │
                    │ name                     │
                    │ birth_place              │
                    │ birth_date               │
                    │ address                  │
                    │ class_id (FK)            │
                    │ major_id (FK)            │
                    │ gender                   │
                    │ academic_year_id (FK)    │
                    │ phone                    │
                    │ max_loan                 │
                    │ is_active                │
                    │ timestamps               │
                    │ deleted_at               │
                    └──────────────────────────┘
                                  │
                                  │
┌──────────────────┐              │              ┌──────────────────┐
│ classifications  │              │              │ sub_classifications│
├──────────────────┤              │              ├──────────────────┤
│ id               │◄─────────────┼──────────────│ id               │
│ ddc_code         │              │              │ classification_id│
│ name             │              │              │ sub_ddc_code     │
│ timestamps       │              │              │ name             │
└──────────────────┘              │              │ timestamps       │
         │                        │              └──────────────────┘
         │                        │                       │
         ▼                        │                       ▼
┌──────────────────────────────────────────────────────────────────┐
│                            books                                  │
├──────────────────────────────────────────────────────────────────┤
│ id                          │ isbn                                │
│ code (unique)               │ stock                               │
│ title                       │ page_count                          │
│ author                      │ thickness                           │
│ publisher_id (FK)           │ classification_id (FK)              │
│ publish_place               │ sub_classification_id (FK)          │
│ publish_year                │ category_id (FK)                    │
│ shelf_location              │ description (nullable)              │
│ source                      │ price (nullable)                    │
│ entry_date                  │ timestamps, deleted_at              │
└──────────────────────────────────────────────────────────────────┘
                                  │
                                  ▼
                    ┌──────────────────────────┐
                    │       book_copies        │
                    ├──────────────────────────┤
                    │ id                       │
                    │ book_id (FK)             │
                    │ barcode (unique)         │
                    │ status (available/       │
                    │         borrowed/lost)   │
                    │ timestamps               │
                    └──────────────────────────┘
                                  │
                                  ▼
                    ┌──────────────────────────┐
                    │          loans           │
                    ├──────────────────────────┤
                    │ id                       │
                    │ student_id (FK)          │
                    │ book_copy_id (FK)        │
                    │ loan_date                │
                    │ due_date                 │
                    │ return_date (nullable)   │
                    │ loan_type (regular/      │
                    │   semester/custom)       │
                    │ status (active/returned/ │
                    │         overdue/lost)    │
                    │ timestamps               │
                    └──────────────────────────┘
                                  │
                                  ▼
                    ┌──────────────────────────┐
                    │          fines           │
                    ├──────────────────────────┤
                    │ id                       │
                    │ loan_id (FK)             │
                    │ student_id (FK)          │
                    │ type (late/lost)         │
                    │ amount                   │
                    │ days_overdue             │
                    │ is_paid                  │
                    │ paid_at (nullable)       │
                    │ timestamps               │
                    └──────────────────────────┘

┌──────────────────┐     ┌──────────────────┐     ┌──────────────────┐
│   publishers     │     │    categories    │     │  fine_settings   │
├──────────────────┤     ├──────────────────┤     ├──────────────────┤
│ id               │     │ id               │     │ id               │
│ name             │     │ name             │     │ daily_fine       │
│ timestamps       │     │ timestamps       │     │ lost_book_fine   │
└──────────────────┘     └──────────────────┘     │ lost_fine_type   │
                                                  │ (flat/book_price)│
                                                  │ timestamps       │
                                                  └──────────────────┘
```

### Model Definitions

#### Student Model
```php
class Student extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'nis', 'name', 'birth_place', 'birth_date', 'address',
        'class_id', 'major_id', 'gender', 'academic_year_id',
        'phone', 'max_loan', 'is_active'
    ];
    
    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
        'max_loan' => 'integer'
    ];
    
    public function class(): BelongsTo
    public function major(): BelongsTo
    public function academicYear(): BelongsTo
    public function loans(): HasMany
    public function fines(): HasMany
    public function activeLoans(): HasMany
    public function unpaidFines(): HasMany
}
```

#### Book Model
```php
class Book extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'code', 'title', 'author', 'publisher_id', 'publish_place',
        'publish_year', 'isbn', 'stock', 'page_count', 'thickness',
        'classification_id', 'sub_classification_id', 'category_id',
        'shelf_location', 'description', 'source', 'entry_date', 'price'
    ];
    
    protected $casts = [
        'entry_date' => 'date',
        'stock' => 'integer',
        'price' => 'decimal:2'
    ];
    
    public function publisher(): BelongsTo
    public function classification(): BelongsTo
    public function subClassification(): BelongsTo
    public function category(): BelongsTo
    public function copies(): HasMany
    public function availableCopies(): HasMany
}
```

#### BookCopy Model
```php
class BookCopy extends Model
{
    use HasFactory;
    
    protected $fillable = ['book_id', 'barcode', 'status'];
    
    public function book(): BelongsTo
    public function loans(): HasMany
    public function currentLoan(): HasOne
    
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
```

#### Loan Model
```php
class Loan extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'student_id', 'book_copy_id', 'loan_date', 'due_date',
        'return_date', 'loan_type', 'status'
    ];
    
    protected $casts = [
        'loan_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date'
    ];
    
    public function student(): BelongsTo
    public function bookCopy(): BelongsTo
    public function fine(): HasOne
    
    public function isOverdue(): bool
    {
        return $this->status === 'active' && now()->gt($this->due_date);
    }
    
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) return 0;
        return now()->diffInDays($this->due_date);
    }
}
```

#### Fine Model
```php
class Fine extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'loan_id', 'student_id', 'type', 'amount',
        'days_overdue', 'is_paid', 'paid_at'
    ];
    
    protected $casts = [
        'amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime'
    ];
    
    public function loan(): BelongsTo
    public function student(): BelongsTo
}
```



## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Student NIS Uniqueness
*For any* two students in the system, their NIS values must be different.
**Validates: Requirements 8.4, 9.6**

### Property 2: Book Code Uniqueness
*For any* two books in the system, their book codes must be different.
**Validates: Requirements 12.3, 13.8**

### Property 3: Barcode Uniqueness
*For any* two book copies in the system, their barcode values must be different.
**Validates: Requirements 14.1, 14.2**

### Property 4: Loan Limit Enforcement
*For any* student, the count of their active loans must not exceed their max_loan value.
**Validates: Requirements 15.8**

### Property 5: Book Copy Availability Consistency
*For any* book copy, if it has an active loan (status='active'), then the book copy status must be 'borrowed'. If it has no active loan, the status must be 'available' or 'lost'.
**Validates: Requirements 15.7, 16.5**

### Property 6: Fine Calculation Correctness
*For any* late return, the fine amount must equal (days_overdue × daily_fine_rate).
**Validates: Requirements 7.1, 16.6**

### Property 7: Lost Book Fine Calculation
*For any* lost book fine with flat price type, the amount must equal the configured lost_book_fine. For book_price type, the amount must equal the book's price.
**Validates: Requirements 7.2, 7.3, 16.7**

### Property 8: Classification Hierarchy Integrity
*For any* sub-classification, its parent classification_id must reference an existing classification.
**Validates: Requirements 6.1**

### Property 9: Student Data Integrity on Delete
*For any* student with active loans, deletion must be prevented.
**Validates: Requirements 11.5, 11.6**

### Property 10: Master Data Referential Integrity
*For any* master data (class, major, academic_year, classification) with associated records, deletion must be prevented.
**Validates: Requirements 2.4, 2.5, 3.4, 3.5, 4.4, 4.5, 5.5, 5.6**

### Property 11: Stock and Copy Count Consistency
*For any* book, the count of its book_copies must equal its stock value.
**Validates: Requirements 14.3, 14.6**

### Property 12: Class Promotion Correctness
*For any* class promotion from level X to level Y, all students in the source class must be moved to the target class with updated academic year.
**Validates: Requirements 10.2, 10.3**

### Property 13: Import Validation Round-Trip
*For any* valid Excel import data, importing then exporting the same data should produce equivalent records (excluding auto-generated fields).
**Validates: Requirements 8.2, 12.2**

### Property 14: Loan Date Ordering
*For any* loan, the loan_date must be less than or equal to due_date, and if return_date exists, loan_date must be less than or equal to return_date.
**Validates: Requirements 15.6**

### Property 15: Dashboard Statistics Accuracy
*For any* dashboard view, the total books count must equal the sum of all book copies, total titles must equal the count of distinct books, and active students must equal students where is_active=true.
**Validates: Requirements 1.1, 1.2, 1.3**

## Error Handling

### Validation Errors
- All form inputs validated using Laravel Form Requests
- Excel import validation with detailed error messages per row
- Duplicate detection for NIS, book codes, and barcodes

### Business Logic Errors
- Loan limit exceeded: Display warning, prevent transaction
- Book not available: Display warning, prevent transaction
- Delete with dependencies: Display error, list dependent records
- Invalid class promotion: Skip and report affected students

### System Errors
- Database connection failures: Display user-friendly error, log details
- File upload failures: Display error with retry option
- PDF/Excel generation failures: Display error, offer alternative format

### Error Response Format
```php
// API/AJAX responses
{
    "success": false,
    "message": "Human-readable error message",
    "errors": {
        "field_name": ["Specific validation error"]
    }
}

// Flash messages for web
session()->flash('error', 'Operation failed: reason');
session()->flash('success', 'Operation completed successfully');
```

## Testing Strategy

### Testing Framework
- **PHPUnit**: Unit tests dan feature tests
- **Pest PHP**: Property-based testing dengan plugin pest-plugin-faker
- **Laravel Dusk**: Browser testing (optional)

### Unit Tests
Unit tests untuk:
- Model relationships dan accessors
- Service class methods
- Fine calculation logic
- Barcode generation
- Date calculations

### Property-Based Tests
Setiap correctness property akan diimplementasikan sebagai property-based test menggunakan Pest PHP dengan minimum 100 iterasi per test.

Format tag: **Feature: school-library, Property {number}: {property_text}**

```php
// Example property test structure
it('enforces student NIS uniqueness', function () {
    // Feature: school-library, Property 1: Student NIS Uniqueness
    $this->repeat(100, function () {
        $nis = fake()->unique()->numerify('##########');
        Student::factory()->create(['nis' => $nis]);
        
        expect(fn() => Student::factory()->create(['nis' => $nis]))
            ->toThrow(QueryException::class);
    });
});
```

### Feature Tests
Feature tests untuk:
- Controller endpoints (CRUD operations)
- Excel import/export functionality
- Loan and return workflows
- Report generation

### Integration Tests
- Full loan-return cycle
- Class promotion workflow
- Fine calculation and payment
- Dashboard data accuracy

### Test Coverage Goals
- Models: 100% coverage
- Services: 100% coverage
- Controllers: 80% coverage
- Livewire Components: 70% coverage
