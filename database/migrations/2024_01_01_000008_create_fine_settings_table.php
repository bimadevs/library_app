<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fine_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('daily_fine', 10, 2)->default(0);
            $table->decimal('lost_book_fine', 10, 2)->default(0);
            $table->enum('lost_fine_type', ['flat', 'book_price'])->default('flat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine_settings');
    }
};
