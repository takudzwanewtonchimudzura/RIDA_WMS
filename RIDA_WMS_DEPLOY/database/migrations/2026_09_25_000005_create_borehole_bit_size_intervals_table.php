<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borehole_bit_size_intervals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borehole_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('bit_size_mm');
            $table->decimal('depth_from_m', 8, 2)->default(0);
            $table->decimal('depth_to_m', 8, 2);
            $table->unsignedSmallInteger('position')->default(1);
            $table->timestamps();
            $table->index(['borehole_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borehole_bit_size_intervals');
    }
};