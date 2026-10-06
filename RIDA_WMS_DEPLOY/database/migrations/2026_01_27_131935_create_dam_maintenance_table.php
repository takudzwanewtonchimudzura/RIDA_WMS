<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dam_maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dam_id')->constrained()->onDelete('cascade');
            
            // Maintenance Details (from document)
            $table->enum('maintenance_type', ['repair', 'rehabilitation', 'routine', 'emergency']);
            $table->date('maintenance_date');
            
            // Wall repairs
            $table->text('wall_repair_details')->nullable()->comment('Nature of repairs or rehabilitation done on the wall');
            
            // Spillway repairs
            $table->text('spillway_repair_details')->nullable()->comment('Nature of repairs or rehabilitation done on the spillway');
            
            // Structural features
            $table->boolean('stone_pitching_replaced')->default(false);
            $table->boolean('star_grass_replanted')->default(false);
            
            // Other works
            $table->text('other_works')->nullable()->comment('Any other works carried out');
            
            // Costs
            $table->decimal('estimated_cost', 12, 2)->nullable();
            $table->decimal('actual_cost', 12, 2)->nullable();
            
            // Inspector/Contractor
            $table->string('contractor_name')->nullable();
            $table->string('contractor_contact')->nullable();
            
            // Status
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned');
            
            // System fields
            $table->text('general_comments')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dam_maintenance');
    }
};