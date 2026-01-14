# Requirements Document

## Introduction

Aplikasi Perpustakaan Sekolah adalah sistem manajemen perpustakaan berbasis web yang dibangun menggunakan Laravel 12 dan MySQL. Sistem ini dirancang untuk mengelola seluruh operasional perpustakaan sekolah termasuk manajemen buku, siswa, peminjaman, pengembalian, denda, dan pelaporan.

## Glossary

- **Library_System**: Sistem aplikasi perpustakaan sekolah secara keseluruhan
- **Dashboard**: Halaman utama yang menampilkan statistik dan informasi penting
- **Master_Data**: Modul untuk mengelola data referensi (tahun ajaran, kelas, jurusan, klasifikasi, denda)
- **Student_Module**: Modul untuk mengelola data siswa
- **Book_Module**: Modul untuk mengelola data buku
- **Transaction_Module**: Modul untuk mengelola peminjaman dan pengembalian buku
- **Report_Module**: Modul untuk menghasilkan laporan
- **DDC**: Dewey Decimal Classification - sistem klasifikasi buku perpustakaan
- **NIS**: Nomor Induk Siswa
- **ISBN**: International Standard Book Number
- **Barcode**: Kode unik untuk identifikasi buku

## Requirements

### Requirement 1: Dashboard

**User Story:** As a library admin, I want to see a dashboard with key statistics and actionable insights, so that I can quickly understand the library's current status and take necessary actions.

#### Acceptance Criteria

1. WHEN the admin accesses the dashboard, THE Dashboard SHALL display total number of books (physical copies)
2. WHEN the admin accesses the dashboard, THE Dashboard SHALL display total number of book titles
3. WHEN the admin accesses the dashboard, THE Dashboard SHALL display total number of active students
4. WHEN the admin accesses the dashboard, THE Dashboard SHALL display a list of loans due today
5. WHEN the admin accesses the dashboard, THE Dashboard SHALL display a list of unpaid fines
6. WHEN loans are due today, THE Dashboard SHALL show borrower name, book title, and due date
7. WHEN fines are unpaid, THE Dashboard SHALL show student name, fine amount, and reason

### Requirement 2: Master Data - Academic Year

**User Story:** As a library admin, I want to manage academic years, so that I can organize student data by academic period.

#### Acceptance Criteria

1. WHEN the admin creates a new academic year, THE Master_Data SHALL store the academic year with a unique identifier
2. WHEN the admin views academic years, THE Master_Data SHALL display all academic years in a list
3. WHEN the admin edits an academic year, THE Master_Data SHALL update the academic year data
4. WHEN the admin deletes an academic year, THE Master_Data SHALL remove the academic year if no students are associated
5. IF an academic year has associated students, THEN THE Master_Data SHALL prevent deletion and display an error message

### Requirement 3: Master Data - Class

**User Story:** As a library admin, I want to manage class data, so that I can categorize students by their class level.

#### Acceptance Criteria

1. WHEN the admin creates a new class, THE Master_Data SHALL store the class name (e.g., X, XI, XII)
2. WHEN the admin views classes, THE Master_Data SHALL display all classes in a list
3. WHEN the admin edits a class, THE Master_Data SHALL update the class data
4. WHEN the admin deletes a class, THE Master_Data SHALL remove the class if no students are associated
5. IF a class has associated students, THEN THE Master_Data SHALL prevent deletion and display an error message

### Requirement 4: Master Data - Major (Jurusan)

**User Story:** As a library admin, I want to manage major/department data, so that I can categorize students by their study program.

#### Acceptance Criteria

1. WHEN the admin creates a new major, THE Master_Data SHALL store the major name and code
2. WHEN the admin views majors, THE Master_Data SHALL display all majors in a list
3. WHEN the admin edits a major, THE Master_Data SHALL update the major data
4. WHEN the admin deletes a major, THE Master_Data SHALL remove the major if no students are associated
5. IF a major has associated students, THEN THE Master_Data SHALL prevent deletion and display an error message

### Requirement 5: Master Data - Classification (DDC)

**User Story:** As a library admin, I want to manage DDC classification data, so that I can categorize books according to the Dewey Decimal Classification system.

#### Acceptance Criteria

1. WHEN the admin creates a new classification, THE Master_Data SHALL store the DDC code and classification name
2. WHEN the admin inputs DDC code "000", THE Master_Data SHALL accept it as a valid three-digit code
3. WHEN the admin views classifications, THE Master_Data SHALL display all classifications with DDC code and name
4. WHEN the admin edits a classification, THE Master_Data SHALL update the classification data
5. WHEN the admin deletes a classification, THE Master_Data SHALL remove the classification if no books or sub-classifications are associated
6. IF a classification has associated data, THEN THE Master_Data SHALL prevent deletion and display an error message

### Requirement 6: Master Data - Sub Classification

**User Story:** As a library admin, I want to manage sub-classification data, so that I can further categorize books within main DDC classifications.

#### Acceptance Criteria

1. WHEN the admin creates a sub-classification, THE Master_Data SHALL require selection of parent DDC classification
2. WHEN the admin creates a sub-classification, THE Master_Data SHALL store sub-DDC code and sub-classification name
3. WHEN the admin inputs sub-DDC "000 - 009" with name "Ilmu Umum dan Komputer", THE Master_Data SHALL store it correctly
4. WHEN the admin views sub-classifications, THE Master_Data SHALL display parent DDC, sub-DDC code, and name
5. WHEN the admin edits a sub-classification, THE Master_Data SHALL update the sub-classification data
6. WHEN the admin deletes a sub-classification, THE Master_Data SHALL remove it if no books are associated

### Requirement 7: Master Data - Fine Settings

**User Story:** As a library admin, I want to configure fine settings, so that I can define penalty amounts for late returns and lost books.

#### Acceptance Criteria

1. WHEN the admin sets daily fine amount, THE Master_Data SHALL store the nominal fine per day
2. WHEN the admin sets lost book fine, THE Master_Data SHALL allow flat price or book price-based calculation
3. IF lost book fine is set to follow book price, THEN THE Master_Data SHALL calculate fine based on book's recorded price
4. WHEN the admin updates fine settings, THE Master_Data SHALL apply new settings to future transactions only
5. WHEN viewing fine settings, THE Master_Data SHALL display current daily fine and lost book fine configuration

### Requirement 8: Student Import

**User Story:** As a library admin, I want to import student data from Excel files, so that I can quickly add multiple students at once.

#### Acceptance Criteria

1. WHEN the admin uploads an Excel file, THE Student_Module SHALL validate the file format
2. WHEN the Excel file contains valid data, THE Student_Module SHALL import all student records
3. WHEN the Excel file contains invalid data, THE Student_Module SHALL display specific error messages for each invalid row
4. WHEN importing students, THE Student_Module SHALL validate NIS uniqueness
5. IF a duplicate NIS is found, THEN THE Student_Module SHALL skip the duplicate and report it
6. WHEN import completes, THE Student_Module SHALL display summary of imported, skipped, and failed records

### Requirement 9: Student Input

**User Story:** As a library admin, I want to manually input student data, so that I can add individual students to the system.

#### Acceptance Criteria

1. WHEN the admin inputs student data, THE Student_Module SHALL require NIS, name, birth place, birth date, address, class, major, gender, academic year, phone number, and max loan limit
2. WHEN selecting class, THE Student_Module SHALL display options from Master_Data classes
3. WHEN selecting major, THE Student_Module SHALL display options from Master_Data majors
4. WHEN selecting academic year, THE Student_Module SHALL display options from Master_Data academic years
5. WHEN the admin saves student data, THE Student_Module SHALL validate all required fields
6. IF NIS already exists, THEN THE Student_Module SHALL display an error message and prevent duplicate entry
7. WHEN student is saved successfully, THE Student_Module SHALL display confirmation message

### Requirement 10: Student Class Promotion

**User Story:** As a library admin, I want to promote students to the next class level, so that I can efficiently update student records at the start of a new academic year.

#### Acceptance Criteria

1. WHEN the admin selects a class for promotion, THE Student_Module SHALL display all students in that class
2. WHEN the admin confirms promotion for class "X TJKT", THE Student_Module SHALL update all students to "XI TJKT"
3. WHEN promotion is executed, THE Student_Module SHALL update the academic year for promoted students
4. WHEN promotion completes, THE Student_Module SHALL display summary of promoted students
5. IF a student cannot be promoted (e.g., already in final year), THEN THE Student_Module SHALL skip and report the student

### Requirement 11: Student List

**User Story:** As a library admin, I want to view all student data in a organized list, so that I can easily find and manage student information.

#### Acceptance Criteria

1. WHEN the admin views student list, THE Student_Module SHALL display all students with NIS, name, class, major, and status
2. WHEN the admin searches for a student, THE Student_Module SHALL filter results by NIS, name, class, or major
3. WHEN the admin clicks on a student, THE Student_Module SHALL display full student details
4. WHEN the admin edits a student, THE Student_Module SHALL allow updating all student fields
5. WHEN the admin deletes a student, THE Student_Module SHALL remove the student if no active loans exist
6. IF a student has active loans, THEN THE Student_Module SHALL prevent deletion and display an error message

### Requirement 12: Book Import

**User Story:** As a library admin, I want to import book data from Excel files, so that I can quickly add multiple books at once.

#### Acceptance Criteria

1. WHEN the admin uploads an Excel file, THE Book_Module SHALL validate the file format
2. WHEN the Excel file contains valid data, THE Book_Module SHALL import all book records
3. WHEN importing books, THE Book_Module SHALL validate book code uniqueness
4. WHEN import completes, THE Book_Module SHALL display summary of imported, skipped, and failed records
5. IF a duplicate book code is found, THEN THE Book_Module SHALL skip the duplicate and report it

### Requirement 13: Book Input

**User Story:** As a library admin, I want to manually input book data, so that I can add individual books to the system.

#### Acceptance Criteria

1. WHEN the admin inputs book data, THE Book_Module SHALL require book code, title, author, publisher, publish place, publish year, ISBN, stock quantity, page count, book thickness, DDC/classification, sub-classification, category, shelf location, source, and entry date
2. WHEN the admin inputs book data, THE Book_Module SHALL allow optional description field
3. WHEN selecting publisher, THE Book_Module SHALL display options from Master_Data or allow new entry
4. WHEN selecting DDC/classification, THE Book_Module SHALL display options from Master_Data classifications
5. WHEN selecting sub-classification, THE Book_Module SHALL filter options based on selected DDC
6. WHEN selecting category, THE Book_Module SHALL display options from Master_Data categories
7. WHEN the admin saves book data, THE Book_Module SHALL validate all required fields
8. IF book code already exists, THEN THE Book_Module SHALL display an error message

### Requirement 14: Book Barcode Generation

**User Story:** As a library admin, I want to generate and print barcodes for books, so that I can easily identify and track individual book copies.

#### Acceptance Criteria

1. WHEN the admin requests barcode generation, THE Book_Module SHALL generate unique barcodes for each book copy
2. WHEN generating barcodes, THE Book_Module SHALL create barcode content as "book_code + unique_copy_id"
3. WHEN the admin specifies print quantity, THE Book_Module SHALL generate that many unique barcodes based on stock
4. WHEN printing barcodes, THE Book_Module SHALL display book title and barcode on each label
5. WHEN barcodes are generated, THE Book_Module SHALL store barcode-to-copy mapping in database
6. IF requested quantity exceeds available stock, THEN THE Book_Module SHALL display an error message

### Requirement 15: Loan Transaction

**User Story:** As a library admin, I want to process book loans, so that students can borrow books from the library.

#### Acceptance Criteria

1. WHEN processing a loan, THE Transaction_Module SHALL allow student identification by barcode scan, NIS input, or student search
2. WHEN the admin clicks "Open" for student search, THE Transaction_Module SHALL display searchable student list by name
3. WHEN processing a loan, THE Transaction_Module SHALL allow book identification by barcode scan, book code input, or book search
4. WHEN the admin clicks "Open" for book search, THE Transaction_Module SHALL display searchable book list by title
5. WHEN selecting loan duration, THE Transaction_Module SHALL offer Regular (1 week default), Semester Package (6 months/1 year), and Custom (manual date input) options
6. WHEN the admin saves the loan, THE Transaction_Module SHALL record loan date, due date, student, and book copy
7. WHEN loan is saved, THE Transaction_Module SHALL update book copy availability status
8. IF student has reached max loan limit, THEN THE Transaction_Module SHALL prevent loan and display warning
9. IF book copy is not available, THEN THE Transaction_Module SHALL prevent loan and display warning
10. IF student has unpaid fines, THEN THE Transaction_Module SHALL display warning but allow loan

### Requirement 16: Return Transaction

**User Story:** As a library admin, I want to process book returns, so that students can return borrowed books.

#### Acceptance Criteria

1. WHEN processing a return, THE Transaction_Module SHALL allow student identification by barcode scan, NIS input, or student search
2. WHEN the admin clicks "Open" for student search, THE Transaction_Module SHALL display searchable student list
3. WHEN student is identified, THE Transaction_Module SHALL display all books currently borrowed by that student
4. WHEN the admin marks a book as returned, THE Transaction_Module SHALL record return date
5. WHEN return is processed, THE Transaction_Module SHALL update book copy availability status
6. IF book is returned late, THEN THE Transaction_Module SHALL calculate and record fine based on days overdue
7. IF book is marked as lost, THEN THE Transaction_Module SHALL calculate and record lost book fine
8. WHEN return is completed, THE Transaction_Module SHALL display any fines incurred

### Requirement 17: Loan Report

**User Story:** As a library admin, I want to generate loan reports, so that I can analyze borrowing patterns and library usage.

#### Acceptance Criteria

1. WHEN generating loan report, THE Report_Module SHALL allow selection of daily or monthly period
2. WHEN generating daily report, THE Report_Module SHALL display all loans for the selected date
3. WHEN generating monthly report, THE Report_Module SHALL display loan summary and details for the selected month
4. WHEN displaying loan report, THE Report_Module SHALL show student name, book title, loan date, due date, and return status
5. WHEN report is generated, THE Report_Module SHALL allow export to PDF or Excel format

### Requirement 18: Fine Report

**User Story:** As a library admin, I want to generate fine and compensation reports, so that I can track library revenue and outstanding payments.

#### Acceptance Criteria

1. WHEN generating fine report, THE Report_Module SHALL display all fines with student name, amount, reason, and payment status
2. WHEN generating fine report, THE Report_Module SHALL calculate total fines collected and outstanding
3. WHEN displaying fine report, THE Report_Module SHALL separate late return fines and lost book compensations
4. WHEN report is generated, THE Report_Module SHALL allow filtering by date range and payment status
5. WHEN report is generated, THE Report_Module SHALL allow export to PDF or Excel format

### Requirement 19: Book Report

**User Story:** As a library admin, I want to generate book reports, so that I can analyze book popularity and identify underutilized resources.

#### Acceptance Criteria

1. WHEN generating book report, THE Report_Module SHALL display top borrowed books with loan count
2. WHEN generating book report, THE Report_Module SHALL display books that have never been borrowed
3. WHEN displaying top borrowed books, THE Report_Module SHALL show book title, author, and total loan count
4. WHEN displaying never borrowed books, THE Report_Module SHALL show book title, author, and entry date
5. WHEN report is generated, THE Report_Module SHALL allow filtering by date range and category
6. WHEN report is generated, THE Report_Module SHALL allow export to PDF or Excel format
