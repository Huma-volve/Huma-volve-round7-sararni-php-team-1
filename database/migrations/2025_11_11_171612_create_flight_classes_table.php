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
        Schema::create('flight_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');

            $table->decimal('price_per_seat', 10, 2)->nullable();
            $table->integer('seats_available')->nullable();

            $table->json('price_rules')->nullable();
            $table->json('baggage_rules')->nullable();
            $table->json('fare_conditions')->nullable();
            $table->json('taxes_fees_breakdown')->nullable();

            $table->boolean('refundable')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flight_classes');
    }
};
