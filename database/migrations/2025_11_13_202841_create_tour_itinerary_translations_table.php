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
        Schema::create('tour_itinerary_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_itinerary_id')->constrained('tour_itineraries')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('title');
            $table->text('description');
            $table->string('location')->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();

            $table->unique(['tour_itinerary_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_itinerary_translations');
    }
};
