<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('water_points', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name');
            $table->string('village_name');
            $table->string('ward');
            
            // Location
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Source Details
            $table->string('type_of_water_source');
            $table->integer('year_drilled')->nullable();
            $table->string('seasonality')->nullable();
            $table->string('type_of_lifting_device')->nullable();
            
            // Functionality
            $table->enum('functionality', ['Functional', 'Partially Functional', 'Nonfunctional']);
            $table->integer('strokes_before_water')->nullable();
            $table->integer('strokes_to_fill_20l')->nullable();
            $table->integer('number_of_pipes')->nullable();
            
            // Infrastructure Condition
            $table->enum('apron_condition', ['good', 'bad', 'needs attention'])->nullable();
            $table->enum('runoff_channel_condition', ['good', 'bad', 'needs attention'])->nullable();
            $table->enum('soakaway_condition', ['good', 'bad', 'needs attention'])->nullable();
            
            // Service Details
            $table->integer('households_served')->nullable();
            $table->integer('population_served')->nullable();
            $table->integer('women_served')->nullable();
            $table->integer('men_served')->nullable();
            $table->integer('children_below_18')->nullable();
            
            // Management Committee
            $table->boolean('has_water_committee')->default(false);
            $table->boolean('committee_functional')->default(false);
            $table->integer('committee_total_members')->nullable();
            $table->integer('committee_women')->nullable();
            $table->integer('committee_men')->nullable();
            
            // Maintenance History
            $table->string('last_repaired_by')->nullable();
            $table->text('last_spares_fitted')->nullable();
            $table->date('last_repair_date')->nullable();
            
            // System Fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('ward');
            $table->index('functionality');
            $table->index('type_of_water_source');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('water_points');
    }
};