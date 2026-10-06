<?php
// database/migrations/xxxx_xx_xx_add_network_length_to_piped_water_schemes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNetworkLengthToPipedWaterSchemesTable extends Migration
{
    // public function up()
    // {
    //     Schema::table('piped_water_schemes', function (Blueprint $table) {
    //         $table->decimal('network_length', 10, 2)->nullable()->after('power_type');
    //         $table->decimal('storage_tanks', 10, 2)->nullable()->after('network_length');
    //         $table->integer('stand_pipes_count')->nullable()->after('storage_tanks');
    //         $table->enum('functionality', ['Functional', 'Partially Functional', 'Non functional'])->nullable()->after('stand_pipes_count');
    //         // Add any other missing columns here
    //     });
    // }

    // public function down()
    // {
    //     Schema::table('piped_water_schemes', function (Blueprint $table) {
    //         $table->dropColumn(['network_length', 'storage_tanks', 'stand_pipes_count', 'functionality']);
    //     });
    // }
}