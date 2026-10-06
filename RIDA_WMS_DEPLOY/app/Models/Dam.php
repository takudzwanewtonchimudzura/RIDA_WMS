<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Basic Information
        'name',
        'village',
        'ward',
        'district',
        'province',
        
        // Location
        'latitude',
        'longitude',
        
        // Dam Wall Details
        'wall_length',
        'wall_height',
        'water_depth',
        'wall_condition',
        'wall_condition_comment',
        
        // Spillway Details
        'spillway_width',
        'spillway_type',
        'spillway_condition',
        'spillway_condition_comment',
        'throwback',
        
        // Capacity Details
        'capacity',
        'wall_volume',
        
        // Structural Features
        'has_stone_pitching',
        'has_star_grass',
        
        // System Fields
        'created_by',
        'updated_by',
        'deleted_by',
        'approved_at',
        'approved_by',
        'general_comments',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'wall_length' => 'decimal:2',
        'wall_height' => 'decimal:2',
        'water_depth' => 'decimal:2',
        'spillway_width' => 'decimal:2',
        'throwback' => 'decimal:2',
        'capacity' => 'decimal:2',
        'wall_volume' => 'decimal:2',
        'has_stone_pitching' => 'boolean',
        'has_star_grass' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the maintenance records for the dam.
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(DamMaintenance::class);
    }

    /**
     * Get the full location address.
     */
    public function getFullLocationAttribute(): string
    {
        $parts = [];
        if ($this->village) $parts[] = $this->village;
        if ($this->ward) $parts[] = "Ward: {$this->ward}";
        if ($this->district) $parts[] = $this->district;
        if ($this->province) $parts[] = $this->province;
        
        return implode(', ', $parts);
    }

    /**
     * Get the formatted coordinates.
     */
    public function getFormattedCoordinatesAttribute(): string
    {
        if (!$this->latitude || !$this->longitude) {
            return 'Not set';
        }

        return number_format($this->latitude, 6) . ', ' . number_format($this->longitude, 6);
    }

    /**
     * Get the current water level percentage.
     */
    public function getWaterLevelPercentageAttribute(): float
    {
        if (!$this->capacity || !$this->water_depth) {
            return 0;
        }

        return min(100, ($this->water_depth / $this->capacity) * 100);
    }

    /**
     * Get water level status.
     */
    public function getWaterLevelStatusAttribute(): string
    {
        $percentage = $this->water_level_percentage;
        
        if ($percentage >= 80) return 'Full';
        if ($percentage >= 50) return 'Moderate';
        if ($percentage >= 20) return 'Low';
        return 'Critical';
    }

    /**
     * Get condition badge color.
     */
    public function getConditionBadgeAttribute(): string
    {
        $conditions = [
            'good' => 'success',
            'fair' => 'warning', 
            'poor' => 'danger',
            'critical' => 'dark',
        ];

        return $conditions[$this->wall_condition] ?? 'secondary';
    }

    /**
     * Check if dam needs attention.
     */
    public function getNeedsAttentionAttribute(): bool
    {
        return in_array($this->wall_condition, ['poor', 'critical']) || 
               in_array($this->spillway_condition, ['poor', 'critical']);
    }

    /**
     * Scope a query to only include dams by province.
     */
    public function scopeByProvince($query, $province)
    {
        return $query->where('province', $province);
    }

    /**
     * Scope a query to only include dams needing attention.
     */
    public function scopeNeedsAttention($query)
    {
        return $query->where(function($q) {
            $q->whereIn('wall_condition', ['poor', 'critical'])
              ->orWhereIn('spillway_condition', ['poor', 'critical']);
        });
    }
}