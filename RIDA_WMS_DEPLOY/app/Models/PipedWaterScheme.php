<?php
// app/Models/PipedWaterScheme.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipedWaterScheme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'village',
        'ward',
        'water_source',
        'water_source_other',
        'year_established',
        'established_by',
        'pump_type',
        'power_type',
        'network_length',
        'storage_tanks',
        'stand_pipes_count',
        'functionality',
        'functionality_issues',
        'households_served',
        'population_served',
        'women_served',
        'men_served',
        'children_served',
        'has_management_committee',
        'committee_trained',
        'committee_functional',
        'has_revolving_fund',
        'fund_amount',
        'committee_members_total',
        'committee_women',
        'committee_men',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'has_management_committee' => 'boolean',
        'committee_trained' => 'boolean',
        'committee_functional' => 'boolean',
        'has_revolving_fund' => 'boolean',
        'network_length' => 'decimal:2',
        'storage_tanks' => 'decimal:2',
        'fund_amount' => 'decimal:2'
    ];

    // Relationship for maintenance records (if needed later)
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(PipedWaterSchemeMaintenance::class);
    }

    public function qualityTests(): HasMany
    {
        return $this->hasMany(WaterQualityTest::class);
    }

    public function latestQualityTest(): HasOne
    {
        return $this->hasOne(WaterQualityTest::class)->latestOfMany('tested_at');
    }
}