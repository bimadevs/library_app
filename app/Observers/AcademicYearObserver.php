<?php

namespace App\Observers;

use App\Models\AcademicYear;

class AcademicYearObserver
{
    /**
     * Handle the AcademicYear "creating" event.
     */
    public function creating(AcademicYear $academicYear): void
    {
        // Default new records to active
        $academicYear->is_active = true;
    }

    /**
     * Handle the AcademicYear "created" event.
     */
    public function created(AcademicYear $academicYear): void
    {
        if ($academicYear->is_active) {
            $this->deactivateOthers($academicYear);
        }
    }

    /**
     * Handle the AcademicYear "updated" event.
     */
    public function updated(AcademicYear $academicYear): void
    {
        if ($academicYear->is_active) {
            $this->deactivateOthers($academicYear);
        }
    }

    /**
     * Deactivate other academic years.
     */
    protected function deactivateOthers(AcademicYear $activeYear): void
    {
        AcademicYear::where('id', '!=', $activeYear->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }
}
