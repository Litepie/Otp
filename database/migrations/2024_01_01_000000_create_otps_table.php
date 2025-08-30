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
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->index(); // email, phone, user_id, etc.
            $table->string('code', 32); // OTP code (hashed or encrypted)
            $table->string('type', 50)->default('default')->index(); // login, email_verification, password_reset, etc.
            $table->string('signature', 255); // Digital signature for verification
            $table->timestamp('expires_at')->index(); // When the OTP expires
            $table->timestamp('used_at')->nullable(); // When the OTP was used
            $table->unsignedTinyInteger('attempts')->default(0); // Number of verification attempts
            $table->unsignedTinyInteger('max_attempts')->default(3); // Maximum allowed attempts
            $table->json('data')->nullable(); // Additional data for the OTP
            $table->timestamps();

            // Composite index for efficient lookups
            $table->index(['identifier', 'type', 'expires_at']);
            
            // Index for cleanup operations
            $table->index(['expires_at', 'used_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
