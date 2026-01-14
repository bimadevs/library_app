<?php

namespace App\Livewire\Master;

use App\Models\Classification;
use App\Models\SubClassification;
use Livewire\Component;
use Livewire\WithPagination;

class SubClassificationTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $classificationFilter = '';
    public string $sortField = 'sub_ddc_code';
    public string $sortDirection = 'asc';
    public int $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'classificationFilter' => ['except' => ''],
        'sortField' => ['except' => 'sub_ddc_code'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClassificationFilter()
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
        $subClassifications = SubClassification::query()
            ->with('classification')
            ->withCount('books')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sub_ddc_code', 'like', '%' . $this->search . '%');
            })
            ->when($this->classificationFilter, function ($query) {
                $query->where('classification_id', $this->classificationFilter);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $classifications = Classification::orderBy('ddc_code')->get();

        return view('livewire.master.sub-classification-table', [
            'subClassifications' => $subClassifications,
            'classifications' => $classifications,
        ]);
    }
}
