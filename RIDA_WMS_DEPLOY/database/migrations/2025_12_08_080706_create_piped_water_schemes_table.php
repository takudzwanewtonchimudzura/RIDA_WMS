<?php
// database/migrations/xxxx_xx_xx_create_piped_water_schemes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePipedWaterSchemesTable extends Migration
{
    public function up()
    {
        Schema::create('piped_water_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('village');
            $table->string('ward');
            $table->enum('water_source', ['borehole', 'deep well', 'spring', 'river', 'dam', 'sand abstraction', 'other']);
            $table->string('water_source_other')->nullable();
            $table->integer('year_established');
            $table->string('established_by');
            $table->string('pump_type');
            $table->enum('power_type', ['solar', 'electricity', 'fossil fuel', 'wind', 'gravity']);
            $table->decimal('network_length', 10, 2)->comment('in metres');
            $table->decimal('storage_tanks', 10, 2)->comment('in m³');
            $table->integer('stand_pipes_count');
            $table->enum('functionality', ['Functional', 'Partially Functional', 'Non functional']);
            $table->text('functionality_issues')->nullable();
            $table->integer('households_served');
            $table->integer('population_served');
            $table->integer('women_served');
            $table->integer('men_served');
            $table->integer('children_served');
            $table->boolean('has_management_committee')->default(false);
            $table->boolean('committee_trained')->default(false);
            $table->boolean('committee_functional')->default(false);
            $table->boolean('has_revolving_fund')->default(false);
            $table->decimal('fund_amount', 15, 2)->nullable();
            $table->integer('committee_members_total')->nullable();
            $table->integer('committee_women')->nullable();
            $table->integer('committee_men')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('piped_water_schemes');
    }
}