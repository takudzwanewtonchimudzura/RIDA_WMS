<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeologyLayer extends Model
{
    use HasFactory;

    protected $fillable = [
        'borehole_id',
        'sample_description',
        'description_layer_depth_from',
        'description_layer_depth_to',
        'penetration_rates_to',
        'penetration_rates_rate',
        'water_strike',
        'yield'
    ];

    protected $casts = [
        'description_layer_depth_from' => 'decimal:2',
        'description_layer_depth_to' => 'decimal:2',
        'penetration_rates_to' => 'decimal:2',
    ];

    public function borehole(): BelongsTo
    {
        return $this->belongsTo(Borehole::class);
    }

    /**
     * Get formatted description layer depth
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if ($this->description_layer_depth_from === 0) {
            return "from ground: {$this->description_layer_depth_to}m";
        } else {
            return "from {$this->description_layer_depth_from}m: {$this->description_layer_depth_to}m";
        }
    }

    /**
     * Get formatted penetration rates
     */
    public function getFormattedPenetrationAttribute(): string
    {
        if ($this->penetration_rates_to && $this->penetration_rates_rate) {
            return "To: {$this->penetration_rates_to}m; Rate: {$this->penetration_rates_rate}";
        }
        return '';
    }
}