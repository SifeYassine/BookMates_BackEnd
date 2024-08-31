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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('rating');
            $table->timestamps();
            $table->foreignId('rater_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('ratee_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('exchangeRequest_id')->constrained('exchange_requests')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
