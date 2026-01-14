<?php

namespace App\Livewire\Student;

use App\Imports\StudentsImport;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class StudentImport extends Component
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
            $import = new StudentsImport();
            Excel::import($import, $this->file->getRealPath());

            $this->summary = $import->getSummary();
            $this->imported = $import->imported;
            $this->skipped = $import->skipped;
            $this->failed = $import->failed;
            $this->showResults = true;

            if ($this->summary['imported'] > 0) {
                session()->flash('success', "Berhasil mengimport {$this->summary['imported']} data siswa.");
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

    public function render()
    {
        return view('livewire.student.student-import');
    }
}
