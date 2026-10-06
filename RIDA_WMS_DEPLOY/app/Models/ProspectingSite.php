<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProspectingSite extends Model
{
    protected $fillable = [
        'reference',
        'project_name',
        'village',
        'ward',
        'district',
        'province',
        'latitude',
        'longitude',
        'survey_date',
        'siting_agency',
        'prospector_name',
        'status',
        'remarks',
        'created_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'survey_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function borehole(): HasOne
    {
        return $this->hasOne(Borehole::class);
    }
}