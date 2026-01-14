<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classification_id')->constrained()->onDelete('restrict');
            $table->string('sub_ddc_code');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_classifications');
    }
};
