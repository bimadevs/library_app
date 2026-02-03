<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE book_copies MODIFY COLUMN status ENUM('available', 'borrowed', 'lost', 'damaged', 'repair') DEFAULT 'available'");
        }
        // For SQLite, typically Laravel creates a VARCHAR without CHECK constraints for enums unless explicitly handled.
        // If strict mode or specific migrations were used, it might be different, but usually this is safe.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // This might fail if there is data with 'damaged' or 'repair' status
            DB::statement("ALTER TABLE book_copies MODIFY COLUMN status ENUM('available', 'borrowed', 'lost') DEFAULT 'available'");
        }
    }
};
