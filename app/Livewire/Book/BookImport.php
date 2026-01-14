<?php

namespace App\Livewire\Book;

use App\Imports\BooksImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class BookImport extends Component
{
    use WithFileUploads;

    public $file;
    public bool $importing = false;
    public bool $showResults = false;
    public array $summary = [];
    public array $imported = [];
    public array $skipped = [];
    public array $failed = [];

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
    ];

    protected $messages = [
        'file.required' => 'File Excel wajib dipilih',
        'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV',
        'file.max' => 'Ukuran file maksimal 5MB',
    ];

    public function updatedFile()
    {
        $this->validateOnly('file');
    }

    public function import()
    {
        $this->validate();

        $this->importing = true;

        try {
            $import = new BooksImport();
            Excel::import($import, $this->file->getRealPath());

            $this->summary = $import->getSummary();
            $this->imported = $import->imported;
            $this->skipped = $import->skipped;
            $this->failed = $import->failed;
            $this->showResults = true;

            if ($this->summary['imported'] > 0) {
                session()->flash('success', "Berhasil mengimport {$this->summary['imported']} data buku.");
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal mengimport file: ' . $e->getMessage());
        }

        $this->importing = false;
        $this->reset('file');
    }

    public function resetImport()
    {
        $this->reset(['file', 'showResults', 'summary', 'imported', 'skipped', 'failed']);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_buku.csv"',
        ];

        $columns = [
            'kode_buku', 'judul', 'pengarang', 'penerbit', 'tempat_terbit', 
            'tahun_terbit', 'isbn', 'stok', 'jumlah_halaman', 'ketebalan',
            'klasifikasi_ddc', 'sub_klasifikasi', 'kategori', 'lokasi_rak',
            'deskripsi', 'sumber', 'tanggal_masuk', 'harga'
        ];
        $example = [
            'BK001', 'Pemrograman PHP', 'John Doe', 'Gramedia', 'Jakarta',
            '2024', '978-123-456-789', '5', '250', '2 cm',
            '000', '000 - 009', 'Fiksi', 'A-01-01',
            'Buku tentang pemrograman PHP', 'Pembelian', '2024-01-15', '150000'
        ];

        $callback = function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        return view('livewire.book.book-import');
    }
}
