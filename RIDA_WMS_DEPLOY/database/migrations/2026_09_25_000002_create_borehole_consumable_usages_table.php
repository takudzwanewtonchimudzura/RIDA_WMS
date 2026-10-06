<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borehole_consumable_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borehole_id')->constrained()->cascadeOnDelete();
            $table->enum('equipment_type', ['bit', 'hammer']);
            $table->string('serial_number');
            $table->unsignedInteger('quantity_used')->default(1);
            $table->timestamps();
            $table->index(['equipment_type', 'serial_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borehole_consumable_usages');
    }
};