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
        Schema::create('booking_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade');

            // Personal Info
            $table->enum('title', ['Mr', 'Mrs', 'Ms'])->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->date('date_of_birth')->nullable();

            // Passport Info
            $table->string('passport_number', 50)->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('nationality', 100)->nullable();

            // Contact
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();

            // Additional
            $table->text('special_requests')->nullable();
            $table->string('seat_number', 10)->nullable()->comment('For flights');
            $table->enum('type', ['adult','child','infant']);


            $table->timestamps();

            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_participants');
    }
};
