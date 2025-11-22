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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('code', 4);
            $table->string('purpose'); // 'verification', 'password_reset'
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->string('email')->nullable(); // For cases where user_id is null
            $table->timestamps();

            $table->index(['user_id', 'purpose', 'used']);
            $table->index(['email', 'purpose', 'used']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
