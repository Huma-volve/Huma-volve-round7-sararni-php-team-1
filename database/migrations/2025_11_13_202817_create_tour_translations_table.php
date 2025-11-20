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
        Schema::create('tour_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name');
            $table->text('description');
            $table->text('short_description');
            $table->text('highlights')->nullable();
            $table->string('meeting_point')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->timestamps();

            $table->unique(['tour_id', 'locale']);
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_translations');
    }
};
