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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade'); 
            $table->string('model'); 
            $table->string('category',50)->nullable(); // SUV, Sedan, Economy...
            $table->string('make')->nullable();
            $table->integer('seats_count');
            $table->integer('doors');
            $table->string('fuel_type',50)->nullable(); // Gasoline, Electric...
            $table->string('transmission',50)->nullable(); // Automatic / Manual
            $table->integer('luggage_capacity')->nullable();
            $table->boolean('air_conditioning')->default(true);
            $table->json('features')->nullable(); // GPS, Bluetooth, etc 
            $table->foreignId('pickup_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->foreignId('dropoff_location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->string('license_requirements')->nullable();
            $table->json('availability_calendar')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->index('pickup_location_id');
            $table->index('dropoff_location_id');
            $table->index('category');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
