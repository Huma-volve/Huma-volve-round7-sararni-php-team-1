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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('category', ['tour', 'hotel', 'car', 'flight'])->index();
            $table->unsignedBigInteger('item_id')->comment('ID of tour/hotel/car/flight');
            $table->timestamps();
            // Unique constraint
            $table->unique(['user_id', 'category', 'item_id']);
            // Indexes
            $table->index('user_id');
            $table->index('item_id');

            $table->unique(['user_id', 'category', 'item_id']);
            $table->index('user_id');
            $table->index('item_id');
            $table->index(['category', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
