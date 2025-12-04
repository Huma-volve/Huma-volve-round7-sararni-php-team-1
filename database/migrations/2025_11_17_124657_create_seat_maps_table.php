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
        Schema::create('flight_seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('flight_leg_id')->nullable()->constrained('flight_legs')->onDelete('cascade');
            $table->string('seat_number');
            $table->enum('status', ['available', 'reserved', 'blocked'])->default('available');
            $table->decimal('price', 10, 2)->nullable();
            $table->unique(['flight_id', 'seat_number']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seat_maps');
    }
};
