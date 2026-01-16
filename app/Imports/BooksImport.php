<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Category;
use App\Models\Classification;
use App\Models\Publisher;
use App\Models\SubClassification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class BooksImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $imported = [];
    public array $skipped = [];
    public array $failed = [];
    
    protected array $classificationMap = [];
    protected array $subClassificationMap = [];
    protected array $categoryMap = [];
    protected array $publisherMap = [];

    public function __construct()
    {
        // Pre-load mappings for performance
        $this->classificationMap = Classification::pluck('id', 'ddc_code')->toArray();
        $this->subClassificationMap = SubClassification::pluck('id', 'sub_ddc_code')->toArray();
        $this->categoryMap = Category::pluck('id', 'name')->toArray();
        $this->publisherMap = Publisher::pluck('id', 'name')->toArray();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-based index
            
            // Skip completely empty rows
            if ($this->isEmptyRow($row)) {
                continue;
            }

            // Validate the row
            $validator = Validator::make($row->toArray(), $this->rules(), $this->customValidationMessages());
            
            if ($validator->fails()) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            // Check for duplicate book code
            $code = trim($row['kode_buku'] ?? '');
            if (Book::where('code', $code)->exists()) {
                $this->skipped[] = [
                    'row' => $rowNumber,
                    'code' => $code,
                    'title' => $row['judul'] ?? '',
                    'reason' => 'Kode buku sudah terdaftar dalam sistem',
                ];
                continue;
            }

            // Check if code already imported in this batch
            if (in_array($code, array_column($this->imported, 'code'))) {
                $this->skipped[] = [
                    'row' => $rowNumber,
                    'code' => $code,
                    'title' => $row['judul'] ?? '',
                    'reason' => 'Kode buku duplikat dalam file import',
                ];
                continue;
            }

            // Resolve foreign keys
            $classificationId = $this->classificationMap[trim($row['klasifikasi_ddc'] ?? '')] ?? null;
            $subClassificationId = !empty(trim($row['sub_klasifikasi'] ?? '')) 
                ? ($this->subClassificationMap[trim($row['sub_klasifikasi'])] ?? null) 
                : null;
            $categoryId = $this->categoryMap[trim($row['kategori'] ?? '')] ?? null;
            $publisherId = $this->publisherMap[trim($row['penerbit'] ?? '')] ?? null;

            if (!$classificationId) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => ['Klasifikasi DDC "' . ($row['klasifikasi_ddc'] ?? '') . '" tidak ditemukan'],
                ];
                continue;
            }

            if (!$categoryId) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => ['Kategori "' . ($row['kategori'] ?? '') . '" tidak ditemukan'],
                ];
                continue;
            }

            if (!$publisherId) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => ['Penerbit "' . ($row['penerbit'] ?? '') . '" tidak ditemukan'],
                ];
                continue;
            }

            // Create the book
            try {
                $book = Book::create([
                    'code' => $code,
                    'title' => trim($row['judul'] ?? ''),
                    'author' => trim($row['pengarang'] ?? ''),
                    'publisher_id' => $publisherId,
                    'publish_place' => trim($row['tempat_terbit'] ?? ''),
                    'publish_year' => (int) ($row['tahun_terbit'] ?? date('Y')),
                    'isbn' => trim($row['isbn'] ?? '') ?: null,
                    'stock' => (int) ($row['stok'] ?? 1),
                    'page_count' => (int) ($row['jumlah_halaman'] ?? 0),
                    'thickness' => trim($row['ketebalan'] ?? '') ?: null,
                    'classification_id' => $classificationId,
                    'sub_classification_id' => $subClassificationId,
                    'category_id' => $categoryId,
                    'shelf_location' => trim($row['lokasi_rak'] ?? ''),
                    'description' => trim($row['deskripsi'] ?? '') ?: null,
                    'source' => trim($row['sumber'] ?? ''),
                    'entry_date' => $this->parseDate($row['tanggal_masuk'] ?? '') ?? now()->format('Y-m-d'),
                    'price' => !empty($row['harga']) ? (float) $row['harga'] : null,
                    'is_textbook' => $this->parseTextbookValue($row['buku_paket'] ?? ''),
                ]);

                $this->imported[] = [
                    'row' => $rowNumber,
                    'code' => $book->code,
                    'title' => $book->title,
                ];
            } catch (\Exception $e) {
                $this->failed[] = [
                    'row' => $rowNumber,
                    'data' => $row->toArray(),
                    'errors' => ['Gagal menyimpan data: ' . $e->getMessage()],
                ];
            }
        }
    }

    protected function isEmptyRow(Collection $row): bool
    {
        return $row->filter(fn($value) => !empty(trim($value ?? '')))->isEmpty();
    }

    protected function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Handle Excel numeric date format
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }

        // Try to parse various date formats
        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $value);
            if ($date !== false) {
                return $date->format('Y-m-d');
            }
        }

        return $value;
    }

    /**
     * Parse textbook value from import.
     * Handles "Ya", "1", "true" as true, defaults to false otherwise.
     */
    protected function parseTextbookValue($value): bool
    {
        if (empty($value)) {
            return false;
        }

        $normalizedValue = strtolower(trim($value));
        
        return in_array($normalizedValue, ['ya', '1', 'true', 'yes'], true);
    }

    public function rules(): array
    {
        return [
            'kode_buku' => 'required|string|max:20',
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:100',
            'penerbit' => 'required|string',
            'tempat_terbit' => 'required|string|max:100',
            'tahun_terbit' => 'required|numeric|min:1900|max:' . (date('Y') + 1),
            'stok' => 'required|numeric|min:1',
            'jumlah_halaman' => 'required|numeric|min:1',
            'klasifikasi_ddc' => 'required|string',
            'kategori' => 'required|string',
            'lokasi_rak' => 'required|string|max:50',
            'sumber' => 'required|string|max:100',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kode_buku.required' => 'Kode buku wajib diisi',
            'judul.required' => 'Judul wajib diisi',
            'pengarang.required' => 'Pengarang wajib diisi',
            'penerbit.required' => 'Penerbit wajib diisi',
            'tempat_terbit.required' => 'Tempat terbit wajib diisi',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi',
            'tahun_terbit.numeric' => 'Tahun terbit harus berupa angka',
            'tahun_terbit.min' => 'Tahun terbit minimal 1900',
            'stok.required' => 'Stok wajib diisi',
            'stok.numeric' => 'Stok harus berupa angka',
            'stok.min' => 'Stok minimal 1',
            'jumlah_halaman.required' => 'Jumlah halaman wajib diisi',
            'jumlah_halaman.numeric' => 'Jumlah halaman harus berupa angka',
            'klasifikasi_ddc.required' => 'Klasifikasi DDC wajib diisi',
            'kategori.required' => 'Kategori wajib diisi',
            'lokasi_rak.required' => 'Lokasi rak wajib diisi',
            'sumber.required' => 'Sumber wajib diisi',
        ];
    }

    public function getSummary(): array
    {
        return [
            'imported' => count($this->imported),
            'skipped' => count($this->skipped),
            'failed' => count($this->failed),
            'total' => count($this->imported) + count($this->skipped) + count($this->failed),
        ];
    }
}
