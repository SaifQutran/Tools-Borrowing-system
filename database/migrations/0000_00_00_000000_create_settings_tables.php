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
        // Majors table
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Arabic name
            $table->timestamps();
        });

        // Academic Levels table
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Arabic name (السنة الأولى, الثانية, etc.)
            $table->timestamps();
        });

        // Departments table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Arabic name
            $table->timestamps();
        });

        // Halls table
        Schema::create('halls', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Hall number or name
            $table->timestamps();
        });

        // Tool Types table
        Schema::create('tool_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Arabic name (ميكروفون, جهاز عرض, etc.)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tool_types');
        Schema::dropIfExists('halls');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('levels');
        Schema::dropIfExists('majors');
    }
};
