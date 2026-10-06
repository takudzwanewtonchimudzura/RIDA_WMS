<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Borehole extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // General Information
        'borehole_serial_no',
        'province',
        'admin_district',
        'locality', 
        'ward',
        'village',
        'vidco',
        'usage',
        'dth_bit_serial_no',
        'hammer_serial_no',
        'date',
        
        // Rig Information
        'rig_number',
        'rig_type',
        'rdd_depth',
        'rig_chasis_number',
        'siting_agency',
        'pisp_specification',
        'drilling_date_from',
        'drilling_date_to',
        
        // Location
        'latitude',
        'longitude', 
        'grid_reference',
        'elevation',
        
        // Drilling Details
        'first_drilled_diameter_mm',
        'second_drilled_diameter_mm',
        'third_drilled_diameter_mm', 
        'final_borehole_depth_m',
        'yield_lps',
        'development_hours',
        
        // Casing & Design Fields
        'borehole_design',
        'final_borehole_depth',
        'final_cased_depth',
        'blowing_yield',
        'cement_sealing',
        
        // Gravel Packing
        'gravel_packing_from_m',
        'gravel_packing_to_m',
        'gravel_type',
        'gravel_thickness_m',
        
        // Cement Sealing  
        'cement_sealing_from_m',
        'cement_sealing_to_m',
        'cement_type',
        'cement_volume_bags',
        
        // Water Quality
        'tds_mg_l',
        'ph',
        'turbidity_ntu',
        'electrical_conductivity',
        'water_color',
        'water_appearance',
        'odor', 
        'taste',
        'water_quality_remarks',
        
        // Pump Installation
        'pipe_length_m',
        'cylinder_size_m',
        'swl_m',
        'dwl_m',
        'pump_type',
        'pump_model',
        'pump_depth_m',
        'pump_installation_date',
        'pump_installation_status',
        'installed_by_name',
        'installed_by_organisation',
        'rig_supervisor_remarks',
        'record_date',
        
        // Consumables
        'fuel_type',
        'electrodes',
        'prospectors',
        'hydraulic_oil',
        'fuel_liters',
        'hydraulic_oil_liters', 
        'gear_oil_liters',
        'grease_kg',
        'drilling_mileage_km',
        'drilling_bits_used',
        'hammer_rings_used',
        'consumables_remarks',
        
        // Project Information
        'funding_agency',
        'project_name',
        'contractor',
        'number_of_beneficiaries',
        'total_cost',
        'cost_currency',
        
        // Personnel
        'drilling_supervisor',
        'hydrogeologist', 
        'technician',
        'operator',
        'assistant_operator',
        
        // Dates
        'drilling_start_date',
        'drilling_end_date',
        'development_date',
        'test_pumping_date',
        'total_drilling_days',
        
        // Remarks
        'supervisor_remarks',
        'client_remarks', 
        'technical_remarks',
        'challenges_encountered',
        'recommendations',
        
        // References
        'references',
        'research',
        
        // System Fields
        'created_by',
        'prospecting_site_id',
        'updated_by',
        'deleted_by',
        'approved_at', 
        'approved_by',
    ];

    protected $casts = [
        'date' => 'date',
        'drilling_date_from' => 'date',
        'drilling_date_to' => 'date',
        'drilling_start_date' => 'date',
        'drilling_end_date' => 'date',
        'development_date' => 'date',
        'test_pumping_date' => 'date',
        'pump_installation_date' => 'date',
        'record_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        
        // Numeric fields from migration
        'first_drilled_diameter_mm' => 'integer',
        'second_drilled_diameter_mm' => 'integer',
        'third_drilled_diameter_mm' => 'integer',
        'final_borehole_depth_m' => 'decimal:2',
        'final_borehole_depth' => 'decimal:2',
        'final_cased_depth' => 'decimal:2',
        'yield_lps' => 'decimal:2',
        'blowing_yield' => 'decimal:2',
        'development_hours' => 'integer',
        'rdd_depth' => 'decimal:2',
        'gravel_packing_from_m' => 'decimal:2',
        'gravel_packing_to_m' => 'decimal:2',
        'gravel_thickness_m' => 'decimal:2',
        'cement_sealing' => 'decimal:2',
        'cement_sealing_from_m' => 'decimal:2',
        'cement_sealing_to_m' => 'decimal:2',
        'cement_volume_bags' => 'decimal:2',
        'tds_mg_l' => 'integer',
        'ph' => 'decimal:1',
        'turbidity_ntu' => 'decimal:2',
        'electrical_conductivity' => 'decimal:2',
        'pipe_length_m' => 'decimal:2',
        'cylinder_size_m' => 'decimal:2',
        'swl_m' => 'decimal:2',
        'dwl_m' => 'decimal:2',
        'pump_depth_m' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'fuel_liters' => 'decimal:2',
        'hydraulic_oil_liters' => 'decimal:2',
        'gear_oil_liters' => 'decimal:2',
        'grease_kg' => 'decimal:2',
        'drilling_mileage_km' => 'decimal:2',
        'drilling_bits_used' => 'integer',
        'hammer_rings_used' => 'integer',
        'number_of_beneficiaries' => 'integer',
        'total_cost' => 'decimal:2',
        'total_drilling_days' => 'integer',
    ];

    /**
     * Get the geology layers for the borehole.
     */
    public function geologyLayers(): HasMany
    {
        return $this->hasMany(GeologyLayer::class);
    }

    /**
     * Get the casing details for the borehole.
     */
    public function casingDetails(): HasMany
    {
        return $this->hasMany(CasingDetail::class);
    }

    public function bitSizeIntervals(): HasMany
    {
        return $this->hasMany(BoreholeBitSizeInterval::class)->orderBy('position');
    }

    public function consumableUsages(): HasMany
    {
        return $this->hasMany(BoreholeConsumableUsage::class);
    }

    public function prospectingSite(): BelongsTo
    {
        return $this->belongsTo(ProspectingSite::class);
    }

    /**
     * Get the user who created the borehole.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the borehole.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if the borehole is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Scope a query to only include approved boreholes.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include pending boreholes.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get the formatted total cost with currency.
     */
    public function getFormattedTotalCostAttribute(): string
    {
        if (!$this->total_cost) {
            return 'N/A';
        }

        return number_format($this->total_cost, 2) . ' ' . ($this->cost_currency ?? 'USD');
    }

    /**
     * Get gravel packing range
     */
    public function getGravelPackingRangeAttribute(): string
    {
        if ($this->gravel_packing_from_m && $this->gravel_packing_to_m) {
            return "FROM: {$this->gravel_packing_from_m}m TO: {$this->gravel_packing_to_m}m";
        }
        return 'N/A';
    }

    /**
     * Get drilling date range
     */
    public function getDrillingDateRangeAttribute(): string
    {
        if ($this->drilling_date_from && $this->drilling_date_to) {
            return "FROM: {$this->drilling_date_from->format('M d, Y')} TO: {$this->drilling_date_to->format('M d, Y')}";
        }
        return 'N/A';
    }
}