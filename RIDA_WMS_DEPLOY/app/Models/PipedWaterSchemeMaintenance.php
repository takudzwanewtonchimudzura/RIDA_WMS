<?php
// app/Models/PipedWaterSchemeMaintenance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipedWaterSchemeMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'piped_water_scheme_id',
        'repaired_by',
        'works_done',
        'spares_fitted',
        'spares_received_by',
        'contact_detail',
        'date_repaired',
        'date_last_repaired',
        'remarks'
    ];

    public function pipedWaterScheme()
    {
        return $this->belongsTo(PipedWaterScheme::class);
    }
}