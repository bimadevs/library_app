# Requirements Document

## Introduction

Dokumen ini mendefinisikan perbaikan bug dan penambahan fitur untuk sistem perpustakaan sekolah. Perbaikan mencakup fitur import siswa/buku, kenaikan kelas, generate barcode, menu sumber buku, scan barcode NIS, dan transaksi peminjaman/pengembalian.

## Glossary

- **Student_Import_System**: Sistem untuk mengimport data siswa dari file Excel/CSV
- **Book_Import_System**: Sistem untuk mengimport data buku dari file Excel/CSV
- **Class_Promotion_System**: Sistem untuk menaikkan kelas siswa secara massal
- **Barcode_Generator**: Sistem untuk generate dan mencetak barcode buku
- **Book_Source_Master**: Data master untuk sumber perolehan buku
- **Loan_Transaction_System**: Sistem untuk mencatat peminjaman buku
- **Return_Transaction_System**: Sistem untuk mencatat pengembalian buku
- **NIS_Scanner**: Fitur untuk input NIS menggunakan barcode scanner

## Requirements

### Requirement 1: Download Template Import Siswa

**User Story:** As a librarian, I want to download a template file for student import, so that I can prepare data in the correct format.

#### Acceptance Criteria

1. WHEN a user clicks the download template button on student import page, THE Student_Import_System SHALL generate and download a CSV file with correct headers
2. THE Student_Import_System SHALL include example data row in the template file
3. THE Student_Import_System SHALL set proper Content-Type and Content-Disposition headers for file download

### Requirement 2: Import Siswa Functionality

**User Story:** As a librarian, I want to import student data from Excel/CSV files, so that I can add multiple students efficiently.

#### Acceptance Criteria

1. WHEN a valid Excel/CSV file is uploaded, THE Student_Import_System SHALL parse and validate each row
2. WHEN a row contains valid data, THE Student_Import_System SHALL create a new student record
3. WHEN a row contains duplicate NIS, THE Student_Import_System SHALL skip the row and report it as skipped
4. WHEN a row contains invalid data, THE Student_Import_System SHALL report the errors for that row
5. THE Student_Import_System SHALL display import summary with counts of imported, skipped, and failed records

### Requirement 3: Kenaikan Kelas (Class Promotion)

**User Story:** As a librarian, I want to promote students to a new class and academic year, so that I can update student records at the start of a new school year.

#### Acceptance Criteria

1. WHEN a source class is selected, THE Class_Promotion_System SHALL display all active students in that class
2. WHEN students are selected and promotion is confirmed, THE Class_Promotion_System SHALL update their class_id and academic_year_id
3. WHEN a student is in final year (XII), THE Class_Promotion_System SHALL skip that student and report the reason
4. THE Class_Promotion_System SHALL display promotion results with promoted and skipped students

### Requirement 4: Download Template Import Buku

**User Story:** As a librarian, I want to download a template file for book import, so that I can prepare data in the correct format.

#### Acceptance Criteria

1. WHEN a user clicks the download template button on book import page, THE Book_Import_System SHALL generate and download a CSV file with correct headers
2. THE Book_Import_System SHALL include example data row in the template file
3. THE Book_Import_System SHALL set proper Content-Type and Content-Disposition headers for file download

### Requirement 5: Import Buku Functionality

**User Story:** As a librarian, I want to import book data from Excel/CSV files, so that I can add multiple books efficiently.

#### Acceptance Criteria

1. WHEN a valid Excel/CSV file is uploaded, THE Book_Import_System SHALL parse and validate each row
2. WHEN a row contains valid data, THE Book_Import_System SHALL create a new book record
3. WHEN a row contains duplicate book code, THE Book_Import_System SHALL skip the row and report it as skipped
4. WHEN a row contains invalid data, THE Book_Import_System SHALL report the errors for that row
5. THE Book_Import_System SHALL display import summary with counts of imported, skipped, and failed records

### Requirement 6: Generate Barcode Buku

**User Story:** As a librarian, I want to generate barcodes for book copies, so that I can track individual book copies.

#### Acceptance Criteria

1. WHEN a book is selected from the list, THE Barcode_Generator SHALL display book details and available barcode slots
2. WHEN generate button is clicked, THE Barcode_Generator SHALL create new BookCopy records with unique barcodes
3. THE Barcode_Generator SHALL display generated barcodes with images
4. WHEN barcodes are selected for printing, THE Barcode_Generator SHALL display a print preview
5. THE Barcode_Generator SHALL allow printing selected barcodes

### Requirement 7: Menu Sumber Buku (Book Source Master)

**User Story:** As a librarian, I want to manage book sources as master data, so that I can select from predefined sources when adding books.

#### Acceptance Criteria

1. THE Book_Source_Master SHALL provide CRUD operations for book sources
2. THE Book_Source_Master SHALL display in the master data menu
3. WHEN adding/editing a book, THE System SHALL display book sources as a dropdown selection
4. THE Book_Source_Master SHALL validate that source names are unique

### Requirement 8: Scan Barcode untuk NIS Siswa

**User Story:** As a librarian, I want to input student NIS using barcode scanner, so that I can quickly identify students.

#### Acceptance Criteria

1. WHEN adding a new student, THE System SHALL provide a barcode scan input field for NIS
2. WHEN a barcode is scanned, THE System SHALL populate the NIS field with the scanned value
3. THE System SHALL support both manual input and barcode scanning for NIS

### Requirement 9: Halaman Peminjaman Buku

**User Story:** As a librarian, I want to create book loans, so that I can track which books are borrowed by students.

#### Acceptance Criteria

1. WHEN the search student button is clicked, THE Loan_Transaction_System SHALL open a modal to search students
2. WHEN searching for students, THE Loan_Transaction_System SHALL filter by NIS or name
3. WHEN a student is selected, THE Loan_Transaction_System SHALL display student details and loan status
4. WHEN the search book button is clicked, THE Loan_Transaction_System SHALL open a modal to search available books
5. WHEN searching for books, THE Loan_Transaction_System SHALL filter by barcode, code, or title
6. WHEN a book is selected, THE Loan_Transaction_System SHALL display book details
7. WHEN the form is submitted, THE Loan_Transaction_System SHALL create a new loan record

### Requirement 10: Halaman Pengembalian Buku

**User Story:** As a librarian, I want to process book returns, so that I can update loan status and calculate fines.

#### Acceptance Criteria

1. WHEN the search student button is clicked, THE Return_Transaction_System SHALL open a modal to search students with active loans
2. WHEN a student is selected, THE Return_Transaction_System SHALL display their borrowed books
3. WHEN books are selected for return, THE Return_Transaction_System SHALL calculate any applicable fines
4. WHEN the return is processed, THE Return_Transaction_System SHALL update loan status and create fine records if applicable
5. THE Return_Transaction_System SHALL display return summary with fine details
