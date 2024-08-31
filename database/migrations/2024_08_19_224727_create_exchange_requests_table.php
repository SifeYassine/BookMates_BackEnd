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
        Schema::create('exchange_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['accepted', 'pending', 'declined'])->default('pending');
            $table->boolean('notification_sent')->default(false); 
            $table->foreignId('requester_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('bookPost_id')->constrained('book_posts')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_requests');
    }
};
