<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CasingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'borehole_id',
        'description',
        'diameter',
        'depth_from',
        'depth_to',
        'position',
        'casing_type',
        'casing_class',
        'gravel_packed',
        'cement_sealed',
    ];

    protected $casts = [
        'diameter' => 'integer',
        'depth_from' => 'decimal:2',
        'depth_to' => 'decimal:2',
        'gravel_packed' => 'boolean',
        'cement_sealed' => 'boolean',
    ];

    public function borehole(): BelongsTo
    {
        return $this->belongsTo(Borehole::class);
    }

    /**
     * Get formatted depth range
     */
    public function getFormattedDepthRangeAttribute(): string
    {
        if ($this->depth_from === 0) {
            return "From ground level";
        } else {
            return "From {$this->depth_from}m";
        }
    }

    /**
     * Get formatted diameter with position
     */
    public function getFormattedDiameterAttribute(): string
    {
        $positions = ['First', 'Second', 'Third', 'Fourth', 'Fifth'];
        $index = $this->position - 1;
        
        if (isset($positions[$index]) && $this->diameter) {
            return "{$positions[$index]}: {$this->diameter}mm";
        }
        return $this->diameter ? "{$this->diameter}mm" : 'N/A';
    }

    /**
     * Get depth to value for display
     */
    public function getDepthToDisplayAttribute(): string
    {
        return $this->depth_to ? "To: {$this->depth_to}m" : '';
    }
}