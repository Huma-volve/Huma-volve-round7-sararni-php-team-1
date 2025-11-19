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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');


            // Booking Reference
            $table->string('booking_reference', 50)->unique();

            // Category & Item
            $table->enum('category', ['tour', 'flight', 'car', 'hotel'])->index();
            $table->unsignedBigInteger('item_id')->comment('ID of tour/flight/car/hotel');

            // Status
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'refunded'])
                ->default('pending')
                ->index();

            // Pricing
            $table->decimal('total_price', 10, 2);
            $table->string('currency', 3)->default('USD');

            // Payment
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded', 'partial'])
                ->default('pending')
                ->index();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();

            // Dates (مشتركة - nullable حسب النوع)
            $table->date('booking_date')->index();
            $table->time('booking_time')->nullable();
            $table->date('check_in_date')->nullable()->comment('For hotels');
            $table->date('check_out_date')->nullable()->comment('For hotels');
            $table->date('pickup_date')->nullable()->comment('For cars/tours');
            $table->date('dropoff_date')->nullable()->comment('For cars');

            // Common fields
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->enum('cancelled_by', ['user', 'admin', 'system'])->nullable();


            // For flights only
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->enum('trip_type', ['one_way','round_trip','multi_city']);
            

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
