<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_quality_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piped_water_scheme_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tested_by')->constrained('users')->restrictOnDelete();
            $table->date('tested_at');
            $table->decimal('ph', 5, 2)->nullable();
            $table->decimal('turbidity_ntu', 8, 2)->nullable();
            $table->unsignedInteger('ecoli_cfu_100ml')->nullable();
            $table->decimal('tds_mg_l', 8, 2)->nullable();
            $table->decimal('electrical_conductivity', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['piped_water_scheme_id', 'tested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_quality_tests');
    }
};
