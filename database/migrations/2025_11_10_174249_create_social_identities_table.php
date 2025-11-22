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
        Schema::create('social_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // 'google', 'facebook', etc.
            $table->string('provider_user_id'); // Google sub, Facebook id, etc.
            $table->string('email')->nullable();
            $table->json('profile_json')->nullable(); // Store provider profile data
            $table->timestamps();

            $table->unique(['provider', 'provider_user_id']);
            $table->index('user_id');
            $table->index('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_identities');
    }
};
