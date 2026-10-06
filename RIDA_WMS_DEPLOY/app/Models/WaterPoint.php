<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WaterPoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Basic Information
        'name',
        'village_name',
        'ward',
        
        // Location
        'latitude',
        'longitude',
        
        // Source Details
        'type_of_water_source',
        'year_drilled',
        'seasonality',
        'type_of_lifting_device',
        
        // Functionality
        'functionality',
        'strokes_before_water',
        'strokes_to_fill_20l',
        'number_of_pipes',
        
        // Infrastructure Condition
        'apron_condition',
        'runoff_channel_condition',
        'soakaway_condition',
        
        // Service Details
        'households_served',
        'population_served',
        'women_served',
        'men_served',
        'children_below_18',
        
        // Management Committee
        'has_water_committee',
        'committee_functional',
        'committee_total_members',
        'committee_women',
        'committee_men',
        
        // Maintenance History
        'last_repaired_by',
        'last_spares_fitted',
        'last_repair_date',
        
        // System Fields
        'created_by',
        'updated_by',
        'deleted_by',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
        'year_drilled' => 'integer',
        'strokes_before_water' => 'integer',
        'strokes_to_fill_20l' => 'integer',
        'number_of_pipes' => 'integer',
        'households_served' => 'integer',
        'population_served' => 'integer',
        'women_served' => 'integer',
        'men_served' => 'integer',
        'children_below_18' => 'integer',
        'has_water_committee' => 'boolean',
        'committee_functional' => 'boolean',
        'committee_total_members' => 'integer',
        'committee_women' => 'integer',
        'committee_men' => 'integer',
        'last_repair_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the total population served.
     */
    public function getTotalPopulationServedAttribute(): int
    {
        return $this->population_served ?? 0;
    }

    /**
     * Get the average strokes per 20L bucket.
     */
    public function getAverageStrokesAttribute(): ?float
    {
        if ($this->strokes_to_fill_20l) {
            return $this->strokes_to_fill_20l;
        }
        
        return null;
    }

    /**
     * Check if water point needs maintenance.
     */
    public function needsMaintenance(): bool
    {
        return in_array($this->apron_condition, ['bad', 'needs attention']) ||
               in_array($this->runoff_channel_condition, ['bad', 'needs attention']) ||
               in_array($this->soakaway_condition, ['bad', 'needs attention']) ||
               $this->functionality !== 'Functional';
    }

    /**
     * Get the days since last repair.
     */
    public function getDaysSinceLastRepairAttribute(): ?int
    {
        if (!$this->last_repair_date) {
            return null;
        }

        return now()->diffInDays($this->last_repair_date);
    }

    /**
     * Check if repair is overdue (more than 180 days).
     */
    public function isRepairOverdue(): bool
    {
        $daysSinceLastRepair = $this->days_since_last_repair;
        
        if ($daysSinceLastRepair === null) {
            return true;
        }

        return $daysSinceLastRepair > 180;
    }

    /**
     * Scope a query to only include functional water points.
     */
    public function scopeFunctional($query)
    {
        return $query->where('functionality', 'Functional');
    }

    /**
     * Scope a query to only include water points by source type.
     */
    public function scopeBySourceType($query, $type)
    {
        return $query->where('type_of_water_source', $type);
    }

    /**
     * Scope a query to only include water points needing attention.
     */
    public function scopeNeedsAttention($query)
    {
        return $query->where(function($q) {
            $q->where('apron_condition', 'bad')
              ->orWhere('apron_condition', 'needs attention')
              ->orWhere('runoff_channel_condition', 'bad')
              ->orWhere('runoff_channel_condition', 'needs attention')
              ->orWhere('soakaway_condition', 'bad')
              ->orWhere('soakaway_condition', 'needs attention')
              ->orWhere('functionality', '!=', 'Functional');
        });
    }
    
}