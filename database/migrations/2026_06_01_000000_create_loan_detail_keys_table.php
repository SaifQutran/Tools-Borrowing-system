<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_detail_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('value_type')->default('text');
            $table->timestamps();
        });

        DB::table('loan_detail_keys')->insert([
            ['name' => 'اسم القاعة', 'value_type' => 'hall', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'اسم الدكتور', 'value_type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'مرفقات', 'value_type' => 'text', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'أخرى', 'value_type' => 'text', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_detail_keys');
    }
};
