<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IrrigationScheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Basic Information
        'name',
        'village',
        'ward',
        
        // Irrigation Details
        'irrigation_type',
        'water_source_type',
        'year_established',
        'established_by',
        
        // Technical Details
        'pump_type',
        'power_type',
        'size_hectares',
        'beneficiary_households',
        'plot_size',
        'crop_pattern',
        'total_storage_tanks_m3',
        
        // Functionality
        'functionality',
        'functionality_comments',
        
        // Management Committee
        'has_management_committee',
        'committee_trained',
        'committee_functional',
        'has_revolving_fund',
        'fund_amount_available',
        'committee_total_members',
        'committee_women',
        'committee_men',
        
        // System Fields
        'created_by',
        'updated_by',
        'deleted_by',
        'approved_at',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'year_established' => 'integer',
        'size_hectares' => 'decimal:2',
        'beneficiary_households' => 'integer',
        'plot_size' => 'decimal:2',
        'total_storage_tanks_m3' => 'decimal:2',
        'has_management_committee' => 'boolean',
        'committee_trained' => 'boolean',
        'committee_functional' => 'boolean',
        'has_revolving_fund' => 'boolean',
        'fund_amount_available' => 'decimal:2',
        'committee_total_members' => 'integer',
        'committee_women' => 'integer',
        'committee_men' => 'integer',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the maintenance records for the irrigation scheme.
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(IrrigationMaintenance::class, 'irrigation_scheme_id');
    }

    /**
     * Get the formatted size in hectares.
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->size_hectares) {
            return 'N/A';
        }

        return number_format($this->size_hectares, 2) . ' ha';
    }

    /**
     * Get the total area under irrigation.
     */
    public function getTotalAreaAttribute(): float
    {
        if ($this->plot_size && $this->beneficiary_households) {
            return $this->plot_size * $this->beneficiary_households;
        }
        
        return $this->size_hectares ?? 0;
    }

    /**
     * Get crop pattern as array.
     */
    public function getCropPatternArrayAttribute(): array
    {
        if (!$this->crop_pattern) {
            return [];
        }

        return array_map('trim', explode(',', $this->crop_pattern));
    }

    /**
     * Scope a query to only include schemes by irrigation type.
     */
    public function scopeByIrrigationType($query, $type)
    {
        return $query->where('irrigation_type', $type);
    }

    /**
     * Scope a query to only include schemes with revolving fund.
     */
    public function scopeWithRevolvingFund($query)
    {
        return $query->where('has_revolving_fund', true)
                     ->where('fund_amount_available', '>', 0);
    }
}