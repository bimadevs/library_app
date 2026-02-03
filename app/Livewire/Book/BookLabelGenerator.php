<?php

namespace App\Livewire\Book;

use App\Models\Book;
use App\Services\BarcodeService;
use Livewire\Component;
use Livewire\WithPagination;

class BookLabelGenerator extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedBookId = null;
    public $selectedCopies = [];
    public $showPrintView = false;
    public $printData = [];

    protected $queryString = ['search' => ['except' => '']];

    public function updatingSearch()
    {
        $this->resetPage();
        $this->reset(['selectedBookId', 'selectedCopies', 'showPrintView']);
    }

    public function selectBook($bookId)
    {
        $this->selectedBookId = $bookId;
        $this->selectedCopies = []; // Reset selection first
        $this->showPrintView = false;
        
        // Auto-select all available copies
        $book = Book::with('copies')->find($bookId);
        if ($book) {
            $this->selectedCopies = $book->copies->pluck('id')->map(fn($id) => (string) $id)->toArray();
        }
    }

    public function selectAll()
    {
        $book = Book::with('copies')->find($this->selectedBookId);
        if ($book) {
            $this->selectedCopies = $book->copies->pluck('id')->map(fn($id) => (string) $id)->toArray();
        }
    }

    public function clearSelection()
    {
        $this->selectedCopies = [];
    }

    public function generateLabels(BarcodeService $barcodeService)
    {
        if (empty($this->selectedCopies)) {
            session()->flash('error', 'Pilih minimal satu buku untuk dicetak.');
            return;
        }

        $copies = \App\Models\BookCopy::with('book.classification')->whereIn('id', $this->selectedCopies)->get();
        
        $this->printData = $copies->map(function ($copy) {
            return [
                'ddc_code' => $copy->book->classification->ddc_code ?? '000',
                'author' => $copy->book->author,
                'title' => $copy->book->title,
            ];
        })->toArray();

        $this->showPrintView = true;
    }

    public function closePrint()
    {
        $this->showPrintView = false;
        $this->printData = [];
    }

    public function render()
    {
        $books = Book::query()
            ->withCount('copies')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->orderBy('title')
            ->paginate(10);

        $selectedBook = $this->selectedBookId ? Book::with('copies')->find($this->selectedBookId) : null;

        return view('livewire.book.book-label-generator', [
            'books' => $books,
            'selectedBook' => $selectedBook,
        ]);
    }
}
