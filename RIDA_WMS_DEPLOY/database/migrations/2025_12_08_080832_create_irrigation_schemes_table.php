<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('irrigation_schemes', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('name');
            $table->string('village');
            $table->string('ward');
            
            // Irrigation Details
            $table->string('irrigation_type');
            $table->string('water_source_type');
            $table->integer('year_established')->nullable();
            $table->string('established_by')->nullable();
            
            // Technical Details
            $table->string('pump_type')->nullable();
            $table->string('power_type')->nullable();
            $table->decimal('size_hectares', 10, 2)->nullable();
            $table->integer('beneficiary_households')->nullable();
            $table->decimal('plot_size', 10, 2)->nullable();
            $table->text('crop_pattern')->nullable();
            $table->decimal('total_storage_tanks_m3', 10, 2)->nullable();
            
            // Functionality
            $table->enum('functionality', ['Functional', 'Partially Functional', 'Nonfunctional']);
            $table->text('functionality_comments')->nullable();
            
            // Management Committee
            $table->boolean('has_management_committee')->default(false);
            $table->boolean('committee_trained')->default(false);
            $table->boolean('committee_functional')->default(false);
            $table->boolean('has_revolving_fund')->default(false);
            $table->decimal('fund_amount_available', 15, 2)->nullable();
            $table->integer('committee_total_members')->nullable();
            $table->integer('committee_women')->nullable();
            $table->integer('committee_men')->nullable();
            
            // System Fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('ward');
            $table->index('irrigation_type');
            $table->index('water_source_type');
            $table->index('functionality');
            $table->index('size_hectares');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('irrigation_schemes');
    }
};