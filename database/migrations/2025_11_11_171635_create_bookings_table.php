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
            $table->foreignId('tour_id')->constrained('tours')->onDelete('restrict');
            $table->string('booking_number')->unique();
            $table->date('tour_date');
            $table->time('tour_time')->nullable();
            $table->integer('adults_count')->default(0);
            $table->integer('children_count')->default(0);
            $table->integer('infants_count')->default(0);
            $table->decimal('adult_price', 10, 2);
            $table->decimal('child_price', 10, 2)->nullable();
            $table->decimal('infant_price', 10, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'refunded'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tour_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('tour_date');
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
