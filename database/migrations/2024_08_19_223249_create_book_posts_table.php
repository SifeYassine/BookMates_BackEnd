<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('offerer_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('offeredBook_id')->constrained('books')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('wishedBook_id')->constrained('books')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_posts');
    }
};
