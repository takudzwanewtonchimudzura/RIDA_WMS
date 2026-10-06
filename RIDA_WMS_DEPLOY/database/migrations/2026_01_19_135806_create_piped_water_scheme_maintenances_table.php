<?php
// database/migrations/xxxx_xx_xx_create_piped_water_scheme_maintenances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePipedWaterSchemeMaintenancesTable extends Migration
{
    public function up()
    {
        Schema::create('piped_water_scheme_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piped_water_scheme_id')->constrained()->onDelete('cascade');
            $table->string('repaired_by')->nullable();
            $table->text('works_done')->nullable();
            $table->text('spares_fitted')->nullable();
            $table->string('spares_received_by')->nullable();
            $table->string('contact_detail')->nullable();
            $table->date('date_repaired')->nullable();
            $table->date('date_last_repaired')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('piped_water_scheme_maintenances');
    }
}