<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoreholeConsumableUsage extends Model
{
    protected $fillable = [
        'borehole_id',
        'equipment_type',
        'serial_number',
        'quantity_used',
    ];

    protected $casts = [
        'quantity_used' => 'integer',
    ];

    public function borehole(): BelongsTo
    {
        return $this->belongsTo(Borehole::class);
    }
}