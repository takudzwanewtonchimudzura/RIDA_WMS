<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoreholeBitSizeInterval extends Model
{
    protected $fillable = [
        'borehole_id',
        'bit_size_mm',
        'depth_from_m',
        'depth_to_m',
        'position',
    ];

    protected $casts = [
        'bit_size_mm' => 'integer',
        'depth_from_m' => 'decimal:2',
        'depth_to_m' => 'decimal:2',
        'position' => 'integer',
    ];

    public function borehole(): BelongsTo
    {
        return $this->belongsTo(Borehole::class);
    }
}