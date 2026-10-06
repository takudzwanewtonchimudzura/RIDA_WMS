<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boreholes', function (Blueprint $table) {
            $table->foreignId('prospecting_site_id')
                ->nullable()
                ->after('created_by')
                ->constrained('prospecting_sites')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('boreholes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('prospecting_site_id');
        });
    }
};