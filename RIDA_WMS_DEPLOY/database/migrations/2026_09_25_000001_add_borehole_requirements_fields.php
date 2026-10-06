<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('boreholes', function (Blueprint $table) {
            $table->string('village')->nullable();
            $table->string('water_appearance')->nullable();
            $table->decimal('final_cased_depth', 8, 2)->nullable();
            $table->string('installed_by_name')->nullable();
            $table->string('installed_by_organisation')->nullable();
        });

        DB::table('boreholes')
            ->whereNull('village')
            ->update(['village' => DB::raw('vidco')]);
        DB::table('boreholes')
            ->whereNull('final_cased_depth')
            ->update(['final_cased_depth' => DB::raw('final_borehole_depth')]);

        Schema::table('geology_layers', function (Blueprint $table) {
            $table->text('sample_description')->nullable();
        });

        Schema::table('casing_details', function (Blueprint $table) {
            $table->string('casing_type')->nullable();
            $table->string('casing_class')->nullable();
            $table->boolean('gravel_packed')->default(false);
            $table->boolean('cement_sealed')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('casing_details', function (Blueprint $table) {
            $table->dropColumn(['casing_type', 'casing_class', 'gravel_packed', 'cement_sealed']);
        });

        Schema::table('geology_layers', function (Blueprint $table) {
            $table->dropColumn('sample_description');
        });

        Schema::table('boreholes', function (Blueprint $table) {
            $table->dropColumn([
                'village',
                'water_appearance',
                'final_cased_depth',
                'installed_by_name',
                'installed_by_organisation',
            ]);
        });
    }
};