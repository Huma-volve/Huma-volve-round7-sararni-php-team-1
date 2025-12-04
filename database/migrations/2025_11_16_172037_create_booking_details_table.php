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
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade');
            $table->json('meta')->comment('Flexible data in JSON format');


            $table->timestamps();

            $table->index('booking_id');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_details');
    }
};
<<<<<<< HEAD
=======


            // $table->foreignId('flight_leg_id')->nullable()->constrained()->nullOnDelete();

            // $table->foreignId('flight_seat_id')->nullable()->constrained()->nullOnDelete();
            // $table->foreignId('participant_id')->nullable()->constrained('booking_participants')->nullOnDelete();

            // $table->foreignId('class_id')->nullable()->constrained()->nullOnDelete();

            // $table->enum('direction', ['outbound', 'return', 'segment'])->default('segment');

            // $table->decimal('price', 10, 2)->nullable();
>>>>>>> 6e876ba9d73195e746d0ed47df06f9269b0e177e
