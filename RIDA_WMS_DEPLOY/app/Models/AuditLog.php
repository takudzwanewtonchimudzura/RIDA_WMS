<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'record_type', 'record_id', 'details'];

    protected $casts = ['details' => 'array', 'created_at' => 'datetime'];

    public static function record(Model $record, string $action, array $details = []): void
    {
        static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'record_type' => $record->getMorphClass(),
            'record_id' => $record->getKey(),
            'details' => $details,
        ]);
    }
}
