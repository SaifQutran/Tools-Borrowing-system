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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tool name in Arabic
            $table->foreignId('tool_type_id')->constrained('tool_types')->onDelete('cascade');
            $table->string('code')->unique(); // Unique tool code
            $table->enum('status', ['available', 'borrowed'])->default('available');
            $table->boolean('seen_by_std')->default(true);
            $table->boolean('seen_by_emp')->default(true);
            $table->json('attributes')->nullable(); // Type-specific attributes (hall_number, microphone_type, specs, etc.)
            $table->string('qr_code_path')->nullable(); // Path to QR code image
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
