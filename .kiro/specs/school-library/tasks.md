# Implementation Plan: School Library Management System

## Overview

Implementasi sistem perpustakaan sekolah menggunakan Laravel 12 dengan Livewire 3 untuk UI interaktif. Implementasi dilakukan secara incremental, dimulai dari setup project, master data, kemudian fitur utama.

## Tasks

- [x] 1. Project Setup dan Konfigurasi
  - [x] 1.1 Install dependencies (Livewire, Laravel Excel, DomPDF, Barcode Generator, Laravel Breeze)
    - Run composer require livewire/livewire laravel/breeze maatwebsite/excel barryvdh/laravel-dompdf picqer/php-barcode-generator
    - Run php artisan breeze:install blade
    - Configure database connection untuk MySQL di .env
    - _Requirements: Project setup_

  - [x] 1.2 Setup Tailwind CSS dan layout dasar
    - Configure Tailwind dengan warna dan komponen yang sesuai
    - Buat layout utama dengan sidebar navigation
    - _Requirements: UI setup_

- [x] 2. Database Migrations dan Models
  - [x] 2.1 Buat migrations untuk semua tabel
    - academic_years, classes, majors, classifications, sub_classifications
    - publishers, categories, fine_settings
    - students, books, book_copies, loans, fines
    - _Requirements: Data Models_

  - [x] 2.2 Buat Eloquent Models dengan relationships
    - Implement semua model sesuai design document
    - Define relationships (belongsTo, hasMany, etc.)
    - Add soft deletes pada Student dan Book
    - _Requirements: Data Models_

  - [x] 2.3 Write property tests untuk model relationships
    - **Property 8: Classification Hierarchy Integrity**
    - **Validates: Requirements 6.1**

  - [x] 2.4 Write property tests untuk uniqueness constraints
    - **Property 1: Student NIS Uniqueness**
    - **Property 2: Book Code Uniqueness**
    - **Property 3: Barcode Uniqueness**
    - **Validates: Requirements 8.4, 9.6, 12.3, 13.8, 14.1, 14.2**

- [x] 3. Checkpoint - Database dan Models
  - Ensure all migrations run successfully
  - Ensure all model tests pass
  - Ask the user if questions arise

- [x] 4. Master Data Module
  - [x] 4.1 Implement Academic Year CRUD
    - Create AcademicYearController dengan index, create, store, edit, update, destroy
    - Create Livewire DataTable component untuk listing
    - Create Blade views untuk form dan list
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5_

  - [x] 4.2 Implement Class CRUD
    - Create ClassController dengan CRUD operations
    - Reuse Livewire DataTable component
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

  - [x] 4.3 Implement Major CRUD
    - Create MajorController dengan CRUD operations
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [x] 4.4 Implement Classification dan Sub-Classification CRUD
    - Create ClassificationController dan SubClassificationController
    - Sub-classification harus filter berdasarkan parent classification
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 5.6, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

  - [x] 4.5 Implement Publisher dan Category CRUD
    - Create PublisherController dan CategoryController
    - _Requirements: 13.3, 13.6_

  - [x] 4.6 Implement Fine Settings
    - Create FineSettingController untuk konfigurasi denda
    - Form untuk daily_fine, lost_book_fine, dan lost_fine_type
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

  - [x] 4.7 Write property tests untuk master data referential integrity
    - **Property 10: Master Data Referential Integrity**
    - **Validates: Requirements 2.4, 2.5, 3.4, 3.5, 4.4, 4.5, 5.5, 5.6**

- [x] 5. Checkpoint - Master Data Module
  - Ensure all master data CRUD works correctly
  - Ensure referential integrity tests pass
  - Ask the user if questions arise

- [x] 6. Student Module
  - [x] 6.1 Implement Student CRUD
    - Create StudentController dengan CRUD operations
    - Create StudentForm Livewire component dengan dropdown untuk class, major, academic_year
    - Create StudentTable Livewire component dengan search dan filter
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5, 9.6, 9.7, 11.1, 11.2, 11.3, 11.4, 11.5, 11.6_

  - [x] 6.2 Implement Student Import dari Excel
    - Create StudentsImport class dengan WithHeadingRow dan WithValidation
    - Create StudentImport Livewire component untuk upload dan preview
    - Handle duplicate NIS detection dan error reporting
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6_

  - [x] 6.3 Implement Class Promotion
    - Create ClassPromotion Livewire component
    - Logic untuk bulk update students dari satu kelas ke kelas berikutnya
    - Update academic_year saat promotion
    - Handle final year students
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

  - [x] 6.4 Write property tests untuk student module
    - **Property 9: Student Data Integrity on Delete**
    - **Property 12: Class Promotion Correctness**
    - **Validates: Requirements 10.2, 10.3, 11.5, 11.6**

- [x] 7. Checkpoint - Student Module
  - Ensure student CRUD, import, dan promotion works correctly
  - Ensure all student tests pass
  - Ask the user if questions arise

- [x] 8. Book Module
  - [x] 8.1 Implement Book CRUD
    - Create BookController dengan CRUD operations
    - Create BookForm Livewire component dengan dropdown untuk classification, sub_classification, category, publisher
    - Sub-classification dropdown harus filter berdasarkan selected classification
    - Create BookTable Livewire component dengan search dan filter
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7, 13.8_

  - [x] 8.2 Implement Book Import dari Excel
    - Create BooksImport class dengan WithHeadingRow dan WithValidation
    - Create BookImport Livewire component untuk upload dan preview
    - _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_

  - [x] 8.3 Implement Barcode Generation
    - Create BarcodeService untuk generate barcode menggunakan picqer/php-barcode-generator
    - Barcode format: book_code + "-" + copy_number (e.g., "BK001-001")
    - Create BarcodeGenerator Livewire component
    - Generate book_copies records saat barcode di-generate
    - Create printable barcode labels dengan book title
    - _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5, 14.6_

  - [x] 8.4 Write property tests untuk book module
    - **Property 11: Stock and Copy Count Consistency**
    - **Property 13: Import Validation Round-Trip**
    - **Validates: Requirements 8.2, 12.2, 14.3, 14.6**

- [x] 9. Checkpoint - Book Module
  - Ensure book CRUD, import, dan barcode generation works correctly
  - Ensure all book tests pass
  - Ask the user if questions arise

- [x] 10. Transaction Module - Loan
  - [x] 10.1 Implement Loan Service
    - Create LoanService dengan logic untuk create loan
    - Validate student loan limit
    - Validate book copy availability
    - Update book_copy status saat loan created
    - _Requirements: 15.6, 15.7, 15.8, 15.9_

  - [x] 10.2 Implement Loan Form UI
    - Create LoanForm Livewire component
    - Create StudentSearch Livewire component (modal dengan search by NIS/name)
    - Create BookSearch Livewire component (modal dengan search by barcode/title)
    - Implement barcode scanner input support
    - Dropdown untuk loan duration (Regular, Semester, Custom)
    - _Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 15.10_

  - [x] 10.3 Write property tests untuk loan
    - **Property 4: Loan Limit Enforcement**
    - **Property 5: Book Copy Availability Consistency**
    - **Property 14: Loan Date Ordering**
    - **Validates: Requirements 15.6, 15.7, 15.8**

- [x] 11. Transaction Module - Return
  - [x] 11.1 Implement Return Service
    - Create ReturnService dengan logic untuk process return
    - Calculate fine untuk late returns menggunakan FineCalculatorService
    - Handle lost book scenario
    - Update book_copy status saat return
    - _Requirements: 16.4, 16.5, 16.6, 16.7, 16.8_

  - [x] 11.2 Implement Fine Calculator Service
    - Create FineCalculatorService
    - Calculate late fine: days_overdue × daily_fine_rate
    - Calculate lost book fine: flat price atau book price
    - _Requirements: 7.1, 7.2, 7.3, 16.6, 16.7_

  - [x] 11.3 Implement Return Form UI
    - Create ReturnForm Livewire component
    - Search student dan display borrowed books
    - Checkbox untuk mark books as returned
    - Display calculated fines
    - _Requirements: 16.1, 16.2, 16.3_

  - [x] 11.4 Write property tests untuk return dan fine calculation
    - **Property 6: Fine Calculation Correctness**
    - **Property 7: Lost Book Fine Calculation**
    - **Validates: Requirements 7.1, 7.2, 7.3, 16.6, 16.7**

- [x] 12. Checkpoint - Transaction Module
  - Ensure loan dan return workflows work correctly
  - Ensure fine calculation is accurate
  - Ensure all transaction tests pass
  - Ask the user if questions arise

- [x] 13. Dashboard
  - [x] 13.1 Implement Dashboard Service
    - Create DashboardService untuk aggregate statistics
    - Total books (count book_copies)
    - Total titles (count books)
    - Active students (count students where is_active=true)
    - Loans due today
    - Unpaid fines
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

  - [x] 13.2 Implement Dashboard UI
    - Create DashboardController
    - Create StatisticsCard Livewire component
    - Create DueTodayTable Livewire component
    - Create UnpaidFinesTable Livewire component
    - _Requirements: 1.6, 1.7_

  - [x] 13.3 Write property tests untuk dashboard statistics
    - **Property 15: Dashboard Statistics Accuracy**
    - **Validates: Requirements 1.1, 1.2, 1.3**

- [x] 14. Report Module
  - [x] 14.1 Implement Loan Report
    - Create LoanReportController
    - Filter by daily/monthly period
    - Display loan details dengan student, book, dates, status
    - Export to PDF dan Excel
    - _Requirements: 17.1, 17.2, 17.3, 17.4, 17.5_

  - [x] 14.2 Implement Fine Report
    - Create FineReportController
    - Display fines dengan student, amount, reason, payment status
    - Calculate totals (collected dan outstanding)
    - Separate late fines dan lost book compensations
    - Filter by date range dan payment status
    - Export to PDF dan Excel
    - _Requirements: 18.1, 18.2, 18.3, 18.4, 18.5_

  - [x] 14.3 Implement Book Report
    - Create BookReportController
    - Top borrowed books dengan loan count
    - Never borrowed books
    - Filter by date range dan category
    - Export to PDF dan Excel
    - _Requirements: 19.1, 19.2, 19.3, 19.4, 19.5, 19.6_

  - [x] 14.4 Write unit tests untuk report generation
    - Test report data accuracy
    - Test export functionality
    - _Requirements: 17.x, 18.x, 19.x_

- [x] 15. Final Checkpoint
  - Ensure all features work correctly end-to-end
  - Ensure all tests pass
  - Review UI/UX consistency
  - Ask the user if questions arise

## Notes

- All tasks are required for comprehensive implementation
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- Property tests validate universal correctness properties
- Unit tests validate specific examples and edge cases
- Livewire components enable real-time search dan filtering tanpa full page reload
- Barcode scanner support menggunakan standard keyboard input (scanner acts as keyboard)
