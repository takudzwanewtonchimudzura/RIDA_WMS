<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dams', function (Blueprint $table) {
            $table->id();
            
            // Basic Information (from document)
            $table->string('name');
            $table->string('village');
            $table->string('ward');
            $table->string('district');
            $table->string('province');
            
            // Coordinates
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Dam Wall Details
            $table->decimal('wall_length', 8, 2)->nullable()->comment('Length of wall in meters');
            $table->decimal('wall_height', 8, 2)->nullable()->comment('Height of wall in meters');
            $table->decimal('water_depth', 8, 2)->nullable()->comment('Current water depth in meters');
            
            // Wall Condition (from document: comment)
            $table->enum('wall_condition', ['good', 'fair', 'poor', 'critical'])->default('good');
            $table->text('wall_condition_comment')->nullable();
            
            // Spillway Details
            $table->decimal('spillway_width', 8, 2)->nullable();
            $table->string('spillway_type')->nullable()->comment('natural on rocks, artificial, other');
            $table->enum('spillway_condition', ['good', 'fair', 'poor', 'critical'])->default('good');
            $table->text('spillway_condition_comment')->nullable();
            
            $table->decimal('throwback', 8, 2)->nullable();
            $table->decimal('capacity', 12, 2)->nullable()->comment('Capacity in cubic meters');
            $table->decimal('wall_volume', 12, 2)->nullable()->comment('Volume of wall in cubic meters');
            
            // Structural Features
            $table->boolean('has_stone_pitching')->default(false)->comment('Upstream stone pitching');
            $table->boolean('has_star_grass')->default(false)->comment('Downstream star grass');
            
            // System Fields
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            
            // General Comments (from document)
            $table->text('general_comments')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['province', 'district']);
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dams');
    }
};