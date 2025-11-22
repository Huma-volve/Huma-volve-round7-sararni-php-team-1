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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->string('amenities');
            $table->string('contact_info');
            $table->json('policies');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->tinyInteger('stars')->nullable();
            $table->integer('rooms_count')->nullable();
            $table->json('recommended')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();


           $table->index('category_id');
           $table->index('name');
       

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
