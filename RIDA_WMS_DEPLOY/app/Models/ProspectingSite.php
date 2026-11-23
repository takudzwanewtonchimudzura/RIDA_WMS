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
        'submitted_at',
        'drilled_at',
        'drilled_by',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'survey_date' => 'date',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'drilled_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withTrashed();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by')->withTrashed();
    }

    public function borehole(): HasOne
    {
        return $this->hasOne(Borehole::class);
    }

    public function markDrilled(User $actor, string $date): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($actor, $date) {
            $site = static::query()->lockForUpdate()->findOrFail($this->id);
            if ($site->status !== 'approved' || !$site->reviewed_at || !$site->reviewed_by
                || (int) $site->reviewed_by === (int) $site->created_by) {
                throw \Illuminate\Validation\ValidationException::withMessages(['status' => 'Independent approval is required before drilling.']);
            }
            validator(['drilled_at' => $date], [
                'drilled_at' => ['required', 'date', 'after_or_equal:' . $site->reviewed_at->toIso8601String(), 'before_or_equal:now'],
            ])->validate();
            $site->update(['status' => 'drilled', 'drilled_at' => $date, 'drilled_by' => $actor->id]);
            AuditLog::record($site, 'drilled', ['drilled_at' => $date, 'approved_by' => $site->reviewed_by]);
            $this->refresh();
        });
    }
}