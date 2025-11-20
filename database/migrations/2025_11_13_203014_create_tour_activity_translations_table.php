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
        Schema::create('tour_activity_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_activity_id')->constrained('tour_activities')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tour_activity_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_activity_translations');
    }
};
