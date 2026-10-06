<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DamMaintenance extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dam_maintenance';

    protected $fillable = [
        'dam_id',
        'maintenance_type',
        'maintenance_date',
        'wall_repair_details',
        'spillway_repair_details',
        'stone_pitching_replaced',
        'star_grass_replanted',
        'other_works',
        'estimated_cost',
        'actual_cost',
        'contractor_name',
        'contractor_contact',
        'status',
        'general_comments',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'stone_pitching_replaced' => 'boolean',
        'star_grass_replanted' => 'boolean',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    /**
     * Get the dam that owns the maintenance record.
     */
    public function dam(): BelongsTo
    {
        return $this->belongsTo(Dam::class);
    }

    /**
     * Get the formatted maintenance type.
     */
    public function getMaintenanceTypeTextAttribute(): string
    {
        $types = [
            'repair' => 'Repair',
            'rehabilitation' => 'Rehabilitation',
            'routine' => 'Routine Maintenance',
            'emergency' => 'Emergency Repair',
        ];

        return $types[$this->maintenance_type] ?? $this->maintenance_type;
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'planned' => 'info',
            'in_progress' => 'warning',
            'completed' => 'success',
            'cancelled' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}