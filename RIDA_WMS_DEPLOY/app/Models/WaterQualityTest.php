<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaterQualityTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'piped_water_scheme_id',
        'tested_by',
        'tested_at',
        'ph',
        'turbidity_ntu',
        'ecoli_cfu_100ml',
        'tds_mg_l',
        'electrical_conductivity',
        'notes',
    ];

    protected $casts = [
        'tested_at' => 'date',
        'ph' => 'decimal:2',
        'turbidity_ntu' => 'decimal:2',
        'tds_mg_l' => 'decimal:2',
        'electrical_conductivity' => 'decimal:2',
    ];

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(PipedWaterScheme::class, 'piped_water_scheme_id');
    }

    public function tester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tested_by');
    }

    public function getOverallQualityAttribute(): string
    {
        if ($this->ph === null && $this->turbidity_ntu === null && $this->ecoli_cfu_100ml === null) {
            return 'Not Tested';
        }

        if (($this->ph !== null && ($this->ph < 6.5 || $this->ph > 8.5)) ||
            ($this->turbidity_ntu !== null && $this->turbidity_ntu > 5) ||
            ($this->ecoli_cfu_100ml !== null && $this->ecoli_cfu_100ml > 0)) {
            return 'Needs Attention';
        }

        return 'Meets Standards';
    }
}
