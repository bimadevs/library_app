<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('name');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('class_id')->constrained('classes')->onDelete('restrict');
            $table->foreignId('major_id')->constrained()->onDelete('restrict');
            $table->enum('gender', ['male', 'female']);
            $table->foreignId('academic_year_id')->constrained()->onDelete('restrict');
            $table->string('phone')->nullable();
            $table->integer('max_loan')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
