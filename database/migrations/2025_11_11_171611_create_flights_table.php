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
       Schema::create('flights', function (Blueprint $table) {
            $table->id();

            $table->integer('flight_number');
            $table->foreignId('carrier_id')->constrained()->onDelete('cascade');
            $table->foreignId('aircraft_id')->nullable()->constrained('aircrafts')->onDelete('set null'); 
            $table->foreignId('destination_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('origin_id')->constrained('locations')->onDelete('cascade');
            $table->timestamp('arrival_time');
            $table->timestamp('departure_time');
            $table->integer('duration_minutes');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flights');
    }
};
