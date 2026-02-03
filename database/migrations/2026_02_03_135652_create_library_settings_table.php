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
        Schema::create('library_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->default('Perpustakaan Sekolah');
            $table->text('school_address')->nullable();
            $table->string('school_logo')->nullable(); // Path to storage
            $table->timestamps();
        });

        // Insert default immediately
        DB::table('library_settings')->insert([
            'school_name' => 'Perpustakaan Sekolah',
            'school_address' => 'Jl. Pendidikan No. 1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_settings');
    }
};
