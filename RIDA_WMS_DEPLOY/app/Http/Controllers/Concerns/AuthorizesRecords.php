<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait AuthorizesRecords
{
    protected function authorizeRecordUpdate(Request $request, Model $record): void
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() || ($user->isOperator() && (int) $record->created_by === (int) $user->id),
            403
        );
    }

    protected function authorizeRecordView(Request $request, Model $record): void
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() || $user->isViewer() || ($user->isOperator() && (int) $record->created_by === (int) $user->id),
            403
        );
    }

    protected function authorizeRecordDelete(Request $request, ?Model $record = null): void
    {
        $user = $request->user();
        abort_unless(
            $user->isAdmin() || ($record && $user->isOperator() && (int) $record->created_by === (int) $user->id),
            403
        );
    }

    protected function authorizeAdministrator(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403);
    }

    protected function scopeRecordsToOwner(Request $request, $query)
    {
        if ($request->user()->isOperator()) {
            $query->where('created_by', $request->user()->id);
        }

        return $query;
    }
}