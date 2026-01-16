# Requirements Document

## Introduction

Fitur ini menambahkan kemampuan untuk menandai buku sebagai "Buku Paket Pelajaran" (textbook) pada sistem perpustakaan sekolah. Buku paket pelajaran memiliki perlakuan khusus dimana peminjaman buku tersebut tidak dihitung dalam batas maksimal peminjaman siswa. Hal ini memungkinkan siswa untuk meminjam buku paket pelajaran tanpa mengurangi kuota peminjaman buku umum mereka.

## Glossary

- **Book**: Entitas buku dalam sistem perpustakaan yang menyimpan informasi seperti judul, pengarang, penerbit, dan metadata lainnya
- **Textbook_Flag**: Atribut boolean pada buku yang menandakan apakah buku tersebut adalah buku paket pelajaran
- **Loan_Limit**: Batas maksimal jumlah buku yang dapat dipinjam oleh seorang siswa pada satu waktu
- **Active_Loan**: Peminjaman yang masih berstatus aktif (belum dikembalikan)
- **LoanService**: Service yang menangani logika peminjaman buku termasuk validasi batas peminjaman
- **BookCopy**: Salinan fisik dari sebuah buku yang dapat dipinjam

## Requirements

### Requirement 1: Textbook Flag pada Data Buku

**User Story:** As a librarian, I want to mark a book as a textbook (buku paket pelajaran), so that I can differentiate textbooks from regular books in the library system.

#### Acceptance Criteria

1. THE Book model SHALL have a boolean attribute `is_textbook` with default value `false`
2. WHEN a librarian creates a new book, THE System SHALL display a checkbox option to mark the book as a textbook
3. WHEN a librarian edits an existing book, THE System SHALL display the current textbook status and allow modification
4. WHEN a book is saved with `is_textbook` set to `true`, THE System SHALL persist this value to the database
5. WHEN displaying book details, THE System SHALL show whether the book is a textbook or not

### Requirement 2: Loan Limit Calculation Excluding Textbooks

**User Story:** As a student, I want textbook loans to not count against my borrowing limit, so that I can borrow textbooks in addition to my regular book quota.

#### Acceptance Criteria

1. WHEN calculating a student's active loan count for limit validation, THE LoanService SHALL exclude loans of books marked as textbooks
2. WHEN a student has reached their loan limit with regular books, THE LoanService SHALL still allow borrowing textbooks
3. WHEN a student borrows a textbook, THE System SHALL not decrement their available loan slots for regular books
4. WHEN displaying remaining loan slots to a student, THE System SHALL show the count based only on non-textbook loans

### Requirement 3: Textbook Indicator in Book Listings

**User Story:** As a librarian, I want to easily identify textbooks in book listings, so that I can quickly distinguish them from regular books.

#### Acceptance Criteria

1. WHEN displaying books in the book table, THE System SHALL show a visual indicator (badge/label) for textbooks
2. WHEN filtering books, THE System SHALL provide an option to filter by textbook status
3. WHEN exporting book reports, THE System SHALL include the textbook status in the export data

