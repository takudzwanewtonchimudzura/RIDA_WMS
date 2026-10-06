<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheme_maintenances', function (Blueprint $table) {
            $table->id();
            
            // Scheme Reference
            $table->foreignId('piped_water_scheme_id')->nullable()->constrained('piped_water_schemes')->onDelete('cascade');
            
            // Scheme Information
            $table->string('scheme_name');
            $table->string('village');
            $table->string('ward');
            
            // Technical Details
            $table->string('water_source_type')->nullable();
            $table->integer('year_established')->nullable();
            $table->string('pump_type')->nullable();
            $table->string('power_type')->nullable();
            $table->decimal('total_network_metres', 10, 2)->nullable();
            $table->decimal('total_storage_tanks_m3', 10, 2)->nullable();
            $table->integer('number_of_standpipes')->nullable();
            
            // Maintenance Details
            $table->string('repaired_by')->nullable();
            $table->text('works_done')->nullable();
            $table->text('spares_fitted')->nullable();
            $table->string('spares_received_by')->nullable();
            $table->text('contact_details')->nullable();
            $table->date('date_repaired')->nullable();
            $table->date('date_last_repaired')->nullable();
            
            // System Fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('remarks')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('scheme_name');
            $table->index('piped_water_scheme_id');
            $table->index('date_repaired');
            $table->index(['village', 'ward']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheme_maintenances');
    }
};