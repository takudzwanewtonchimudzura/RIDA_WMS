<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boreholes', function (Blueprint $table) {
            $table->enum('pump_installation_status', ['successful', 'unsuccessful'])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('boreholes', function (Blueprint $table) {
            $table->dropColumn('pump_installation_status');
        });
    }
};