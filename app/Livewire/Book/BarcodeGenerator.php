<?php

namespace App\Livewire\Book;

use App\Models\Book;
use App\Models\BookCopy;
use App\Services\BarcodeService;
use Livewire\Component;
use Livewire\WithPagination;

class BarcodeGenerator extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $selectedBookId = null;
    public ?Book $selectedBook = null;
    public int $quantity = 1;
    public array $generatedBarcodes = [];
    public bool $showPrintView = false;
    public array $selectedForPrint = [];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function hydrate()
    {
        // Reload selectedBook on each request to ensure it's available
        if ($this->selectedBookId) {
            $this->selectedBook = Book::with('copies')->find($this->selectedBookId);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedBookId = null;
        $this->selectedBook = null;
        $this->quantity = 1;
        $this->generatedBarcodes = [];
    }

    public function selectBook(int $bookId)
    {
        $this->selectedBookId = $bookId;
        $this->selectedBook = Book::with('copies')->find($bookId);
        $this->quantity = 1;
        $this->generatedBarcodes = [];
        $this->selectedForPrint = [];
        
        if ($this->selectedBook) {
            // Calculate max quantity
            $barcodeService = new BarcodeService();
            $availableSlots = $barcodeService->getAvailableSlots($this->selectedBook);
            
            if ($availableSlots > 0) {
                $this->quantity = min(1, $availableSlots);
            }
        }
    }

    public function generateBarcodes()
    {
        if (!$this->selectedBook) {
            session()->flash('error', 'Pilih buku terlebih dahulu.');
            return;
        }

        $barcodeService = new BarcodeService();
        $availableSlots = $barcodeService->getAvailableSlots($this->selectedBook);

        if ($this->quantity > $availableSlots) {
            session()->flash('error', "Jumlah barcode yang diminta ({$this->quantity}) melebihi stok tersedia ({$availableSlots}).");
            return;
        }

        if ($this->quantity < 1) {
            session()->flash('error', 'Jumlah barcode minimal 1.');
            return;
        }

        try {
            $copies = $barcodeService->generateBarcodes($this->selectedBook, $this->quantity);
            
            $this->generatedBarcodes = collect($copies)->map(function ($copy) use ($barcodeService) {
                return $barcodeService->getBarcodeData($copy);
            })->toArray();

            // Refresh the selected book to get updated copies count
            $this->selectedBook = Book::with('copies')->find($this->selectedBookId);

            session()->flash('success', "Berhasil generate {$this->quantity} barcode.");
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal generate barcode: ' . $e->getMessage());
        }
    }

    public function togglePrintSelection(string $barcode)
    {
        if (in_array($barcode, $this->selectedForPrint)) {
            $this->selectedForPrint = array_diff($this->selectedForPrint, [$barcode]);
        } else {
            $this->selectedForPrint[] = $barcode;
        }
    }

    public function selectAllForPrint()
    {
        if (!$this->selectedBook) return;
        
        $this->selectedForPrint = $this->selectedBook->copies->pluck('barcode')->toArray();
    }

    public function clearPrintSelection()
    {
        $this->selectedForPrint = [];
    }

    public function showPrint()
    {
        if (empty($this->selectedForPrint)) {
            session()->flash('error', 'Pilih barcode yang akan dicetak.');
            return;
        }

        $this->showPrintView = true;
    }

    public function closePrint()
    {
        $this->showPrintView = false;
    }

    public function getPrintData(): array
    {
        if (empty($this->selectedForPrint)) {
            return [];
        }

        $barcodeService = new BarcodeService();
        $copies = BookCopy::with('book')
            ->whereIn('barcode', $this->selectedForPrint)
            ->get();

        return $copies->map(fn($copy) => $barcodeService->getBarcodeData($copy))->toArray();
    }

    public function resetSelection()
    {
        $this->selectedBookId = null;
        $this->selectedBook = null;
        $this->quantity = 1;
        $this->generatedBarcodes = [];
        $this->selectedForPrint = [];
        $this->showPrintView = false;
    }

    public function render()
    {
        $books = Book::query()
            ->withCount(['copies', 'availableCopies'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('title')
            ->paginate(10);

        $printData = $this->showPrintView ? $this->getPrintData() : [];

        return view('livewire.book.barcode-generator', [
            'books' => $books,
            'printData' => $printData,
        ]);
    }
}
