<?php

namespace App\Livewire\Master;

use App\Models\BookSource;
use Livewire\Component;
use Livewire\WithPagination;

class BookSourceTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'name';
    public string $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
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

    public function delete(int $id)
    {
        $bookSource = BookSource::find($id);
        
        if (!$bookSource) {
            session()->flash('error', 'Sumber buku tidak ditemukan.');
            return;
        }

        if ($bookSource->books()->exists()) {
            session()->flash('error', 'Sumber buku tidak dapat dihapus karena masih digunakan oleh buku.');
            return;
        }

        $bookSource->delete();
        session()->flash('success', 'Sumber buku berhasil dihapus.');
    }

    public function render()
    {
        $bookSources = BookSource::query()
            ->withCount('books')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.master.book-source-table', [
            'bookSources' => $bookSources,
        ]);
    }
}
