<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class StatisticsCard extends Component
{
    public string $title;
    public int|float $value;
    public string $icon;
    public string $color;
    public string $format = 'number';

    public function mount(
        string $title,
        int|float $value,
        string $icon = 'book',
        string $color = 'emerald',
        string $format = 'number'
    ): void {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
        $this->color = $color;
        $this->format = $format;
    }

    public function getFormattedValue(): string
    {
        if ($this->format === 'currency') {
            return 'Rp ' . number_format($this->value, 0, ',', '.');
        }
        return number_format($this->value, 0, ',', '.');
    }

    public function render()
    {
        return view('livewire.dashboard.statistics-card');
    }
}
