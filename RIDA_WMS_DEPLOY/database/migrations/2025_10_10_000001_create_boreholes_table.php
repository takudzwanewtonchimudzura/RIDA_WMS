<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Drop existing tables if they exist (clean slate)
        Schema::dropIfExists('casing_details');
        Schema::dropIfExists('geology_layers');
        Schema::dropIfExists('boreholes');

        // Create boreholes table
        Schema::create('boreholes', function (Blueprint $table) {
            $table->id();
            
            // General Information
            $table->string('borehole_serial_no')->unique();
            $table->string('province');
            $table->string('admin_district');
            $table->string('locality');
            $table->string('ward');
            $table->string('vidco')->nullable();
            $table->string('usage')->nullable();
            $table->string('dth_bit_serial_no')->nullable();
            $table->string('hammer_serial_no')->nullable();
            $table->date('date');
            
            // Rig Information
            $table->string('rig_number')->nullable();
            $table->string('rig_type')->nullable();
            $table->decimal('rdd_depth', 8, 2)->nullable();
            $table->string('rig_chasis_number')->nullable();
            $table->enum('siting_agency', ['Government', 'PISP'])->nullable();
            $table->string('pisp_specification')->nullable();
            $table->date('drilling_date_from')->nullable();
            $table->date('drilling_date_to')->nullable();
            
            // Location Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 10, 8)->nullable();
            $table->string('grid_reference')->nullable();
            $table->string('elevation')->nullable();
            
            // Drilling Details
            $table->integer('first_drilled_diameter_mm')->nullable();
            $table->integer('second_drilled_diameter_mm')->nullable();
            $table->integer('third_drilled_diameter_mm')->nullable();
            $table->decimal('final_borehole_depth_m', 8, 2)->nullable();
            $table->decimal('yield_lps', 8, 2)->nullable();
            $table->integer('development_hours')->nullable();
            
            // Casing & Design Fields
            $table->string('borehole_design')->nullable();
            $table->decimal('final_borehole_depth', 8, 2)->nullable();
            $table->decimal('blowing_yield', 8, 2)->nullable();
            $table->decimal('cement_sealing', 8, 2)->nullable();
            
            // Gravel Packing
            $table->decimal('gravel_packing_from_m', 8, 2)->nullable();
            $table->decimal('gravel_packing_to_m', 8, 2)->nullable();
            $table->string('gravel_type')->nullable();
            $table->decimal('gravel_thickness_m', 8, 2)->nullable();
            
            // Cement Sealing
            $table->decimal('cement_sealing_from_m', 8, 2)->nullable();
            $table->decimal('cement_sealing_to_m', 8, 2)->nullable();
            $table->string('cement_type')->nullable();
            $table->decimal('cement_volume_bags', 8, 2)->nullable();
            
            // Water Quality
            $table->integer('tds_mg_l')->nullable();
            $table->decimal('ph', 3, 1)->nullable();
            $table->decimal('turbidity_ntu', 8, 2)->nullable();
            $table->decimal('electrical_conductivity', 8, 2)->nullable();
            $table->string('water_color')->nullable();
            $table->string('odor')->nullable();
            $table->string('taste')->nullable();
            $table->text('water_quality_remarks')->nullable();
            
            // Pump Installation
            $table->decimal('pipe_length_m', 8, 2)->nullable();
            $table->decimal('cylinder_size_m', 8, 2)->nullable();
            $table->decimal('swl_m', 8, 2)->nullable();
            $table->decimal('dwl_m', 8, 2)->nullable();
            $table->string('pump_type')->nullable();
            $table->string('pump_model')->nullable();
            $table->decimal('pump_depth_m', 8, 2)->nullable();
            $table->date('pump_installation_date')->nullable();
            $table->text('rig_supervisor_remarks')->nullable();
            $table->date('record_date')->nullable();
            
            // Consumables
            $table->enum('fuel_type', ['DIESEL', 'PETROL'])->nullable();
            $table->string('electrodes')->nullable();
            $table->string('prospectors')->nullable();
            $table->string('hydraulic_oil')->nullable();
            $table->decimal('fuel_liters', 8, 2)->nullable();
            $table->decimal('hydraulic_oil_liters', 8, 2)->nullable();
            $table->decimal('gear_oil_liters', 8, 2)->nullable();
            $table->decimal('grease_kg', 8, 2)->nullable();
            $table->decimal('drilling_mileage_km', 8, 2)->nullable();
            $table->integer('drilling_bits_used')->nullable();
            $table->integer('hammer_rings_used')->nullable();
            $table->text('consumables_remarks')->nullable();
            
            // Project Information
            $table->string('funding_agency')->nullable();
            $table->string('project_name')->nullable();
            $table->string('contractor')->nullable();
            $table->integer('number_of_beneficiaries')->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->string('cost_currency')->default('USD');
            
            // Personnel
            $table->string('drilling_supervisor')->nullable();
            $table->string('hydrogeologist')->nullable();
            $table->string('technician')->nullable();
            $table->string('operator')->nullable();
            $table->string('assistant_operator')->nullable();
            
            // Time and Dates
            $table->date('drilling_start_date')->nullable();
            $table->date('drilling_end_date')->nullable();
            $table->date('development_date')->nullable();
            $table->date('test_pumping_date')->nullable();
            $table->integer('total_drilling_days')->nullable();
            
            // Remarks
            $table->text('supervisor_remarks')->nullable();
            $table->text('client_remarks')->nullable();
            $table->text('technical_remarks')->nullable();
            $table->text('challenges_encountered')->nullable();
            $table->text('recommendations')->nullable();
            
            // References
            $table->string('references')->nullable();
            $table->string('research')->nullable();
            
            // System Fields - Made nullable for safety
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for better performance with shorter names
            $table->index(['province', 'admin_district'], 'bh_province_district_idx');
            $table->index('borehole_serial_no', 'bh_serial_no_idx');
            $table->index('date', 'bh_date_idx');
            $table->index('approved_at', 'bh_approved_idx');
            $table->index('created_by', 'bh_created_by_idx');
            $table->index('funding_agency', 'bh_funding_agency_idx');
            $table->index('project_name', 'bh_project_name_idx');
        });

        // Create geology_layers table
        Schema::create('geology_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borehole_id')->constrained()->onDelete('cascade');
            $table->decimal('description_layer_depth_from', 8, 2)->nullable();
            $table->decimal('description_layer_depth_to', 8, 2)->nullable();
            $table->decimal('penetration_rates_to', 8, 2)->nullable();
            $table->string('penetration_rates_rate')->nullable();
            $table->string('water_strike')->nullable();
            $table->string('yield')->nullable();
            $table->timestamps();
            
            // Shorter index names
            $table->index('borehole_id', 'geo_borehole_id_idx');
            $table->index(['description_layer_depth_from', 'description_layer_depth_to'], 'geo_depth_range_idx');
        });

        // Create casing_details table
        Schema::create('casing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borehole_id')->constrained()->onDelete('cascade');
            $table->enum('description', ['Plain', 'Slotted'])->nullable();
            $table->integer('diameter')->nullable();
            $table->decimal('depth_from', 8, 2)->nullable();
            $table->decimal('depth_to', 8, 2)->nullable();
            $table->integer('position')->default(1);
            $table->timestamps();
            
            // Shorter index names
            $table->index('borehole_id', 'casing_borehole_id_idx');
            $table->index('position', 'casing_position_idx');
            $table->index(['depth_from', 'depth_to'], 'casing_depth_range_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('casing_details');
        Schema::dropIfExists('geology_layers');
        Schema::dropIfExists('boreholes');
    }
};