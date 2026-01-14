<?php

namespace App\Livewire\Master;

use App\Models\Classification;
use Livewire\Component;
use Livewire\WithPagination;

class ClassificationTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'ddc_code';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'ddc_code'],
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

    public function render()
    {
        $classifications = Classification::query()
            ->withCount(['subClassifications', 'books'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('ddc_code', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.master.classification-table', [
            'classifications' => $classifications,
        ]);
    }
}
