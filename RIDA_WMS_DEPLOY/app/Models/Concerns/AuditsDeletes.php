<?php

namespace App\Models\Concerns;

use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

trait AuditsDeletes
{
    public function delete()
    {
        return DB::transaction(function () {
            $deleted = parent::delete();
            if ($deleted) {
                AuditLog::record($this, 'deleted');
            }
            return $deleted;
        });
    }
}
