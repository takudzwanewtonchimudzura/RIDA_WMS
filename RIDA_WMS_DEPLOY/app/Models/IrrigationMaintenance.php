<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IrrigationMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Scheme Reference
        'irrigation_scheme_id',
        
        // Scheme Information
        'scheme_name',
        'village',
        'ward',
        
        // Technical Details
        'water_source_type',
        'year_established',
        'established_by',
        'pump_type',
        'power_type',
        'irrigation_type',
        'total_storage_tanks_m3',
        
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
        'total_storage_tanks_m3' => 'decimal:2',
        'date_repaired' => 'date',
        'date_last_repaired' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the irrigation scheme for this maintenance.
     */
    public function irrigationScheme(): BelongsTo
    {
        return $this->belongsTo(IrrigationScheme::class);
    }

    /**
     * Get the days since last repair.
     */
    public function getDaysSinceLastRepairAttribute(): ?int
    {
        if (!$this->date_last_repaired) {
            return null;
        }

        return now()->diffInDays($this->date_last_repaired);
    }

    /**
     * Check if maintenance is overdue (more than 365 days since last repair).
     */
    public function isMaintenanceOverdue(): bool
    {
        $daysSinceLastRepair = $this->days_since_last_repair;
        
        if ($daysSinceLastRepair === null) {
            return true;
        }

        return $daysSinceLastRepair > 365;
    }
}