<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\LibrarySetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observer for AcademicYear if needed, keeping original line or ensuring safety
        // \App\Models\AcademicYear::observe(\App\Observers\AcademicYearObserver::class);

        // Share Library Settings globally
        // Check if table exists to avoid errors during migration
        if (Schema::hasTable('library_settings')) {
            $setting = LibrarySetting::first();

            // If somehow empty (migration ran but insert failed?), create default
            if (!$setting) {
                $setting = LibrarySetting::create([
                    'school_name' => 'Perpustakaan Sekolah',
                    'school_address' => 'Jl. Pendidikan No. 1',
                ]);
            }

            View::share('librarySetting', $setting);
        }
    }
}
