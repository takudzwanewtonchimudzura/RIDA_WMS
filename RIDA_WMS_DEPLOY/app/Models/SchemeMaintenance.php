<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchemeMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Scheme Reference
        'piped_water_scheme_id',
        
        // Scheme Information
        'scheme_name',
        'village',
        'ward',
        
        // Technical Details
        'water_source_type',
        'year_established',
        'pump_type',
        'power_type',
        'total_network_metres',
        'total_storage_tanks_m3',
        'number_of_standpipes',
        
        // Maintenance Details
        'repaired_by',
        'works_done',
        'spares_fitted',
        'spares_received_by',
        'contact_details',
        'date_repaired',
        'date_last_repaired',
        
        // System Fields
        'created_by',
        'updated_by',
        'deleted_by',
        'remarks',
    ];

    protected $casts = [
        'year_established' => 'integer',
        'total_network_metres' => 'decimal:2',
        'total_storage_tanks_m3' => 'decimal:2',
        'number_of_standpipes' => 'integer',
        'date_repaired' => 'date',
        'date_last_repaired' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the piped water scheme for this maintenance.
     */
    public function pipedWaterScheme(): BelongsTo
    {
        return $this->belongsTo(PipedWaterScheme::class);
    }

    /**
     * Get the formatted repair date.
     */
    public function getFormattedRepairDateAttribute(): string
    {
        if (!$this->date_repaired) {
            return 'N/A';
        }

        return $this->date_repaired->format('M d, Y');
    }

    /**
     * Get the formatted last repair date.
     */
    public function getFormattedLastRepairDateAttribute(): string
    {
        if (!$this->date_last_repaired) {
            return 'N/A';
        }

        return $this->date_last_repaired->format('M d, Y');
    }

    /**
     * Check if maintenance was done recently (within last 6 months).
     */
    public function isRecentMaintenance(): bool
    {
        if (!$this->date_repaired) {
            return false;
        }

        return $this->date_repaired->diffInMonths(now()) <= 6;
    }
    
}