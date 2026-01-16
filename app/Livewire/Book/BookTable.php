<?php

namespace App\Livewire\Book;

use App\Models\Book;
use App\Models\Category;
use App\Models\Classification;
use Livewire\Component;
use Livewire\WithPagination;

class BookTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'title';
    public string $sortDirection = 'asc';
    public int $perPage = 10;
    public string $filterClassification = '';
    public string $filterCategory = '';
    public string $filterTextbook = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'title'],
        'sortDirection' => ['except' => 'asc'],
        'filterClassification' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterTextbook' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterClassification()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function updatingFilterTextbook()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['search', 'filterClassification', 'filterCategory', 'filterTextbook']);
        $this->resetPage();
    }

    public function render()
    {
        $books = Book::query()
            ->with(['classification', 'subClassification', 'category', 'publisher'])
            ->withCount(['copies', 'availableCopies'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%')
                      ->orWhere('author', 'like', '%' . $this->search . '%')
                      ->orWhere('isbn', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterClassification, function ($query) {
                $query->where('classification_id', $this->filterClassification);
            })
            ->when($this->filterCategory, function ($query) {
                $query->where('category_id', $this->filterCategory);
            })
            ->when($this->filterTextbook !== '', function ($query) {
                $query->where('is_textbook', $this->filterTextbook === '1');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.book.book-table', [
            'books' => $books,
            'classifications' => Classification::orderBy('ddc_code')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
