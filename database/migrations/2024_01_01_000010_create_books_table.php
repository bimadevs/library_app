<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('author');
            $table->foreignId('publisher_id')->nullable()->constrained()->onDelete('restrict');
            $table->string('publish_place')->nullable();
            $table->year('publish_year')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('page_count')->nullable();
            $table->string('thickness')->nullable();
            $table->foreignId('classification_id')->constrained()->onDelete('restrict');
            $table->foreignId('sub_classification_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('restrict');
            $table->string('shelf_location')->nullable();
            $table->text('description')->nullable();
            $table->string('source')->nullable();
            $table->date('entry_date')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
