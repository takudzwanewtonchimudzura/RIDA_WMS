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
            $user->isAdmin() || ($user->canCapture() && (int) $record->created_by === (int) $user->id),
            403
        );
    }

    protected function authorizeRecordView(Request $request, Model $record): void
    {
        $user = $request->user();
        abort_unless(
            !$user->isOperator() || (int) $record->created_by === (int) $user->id,
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

    protected function authorizeApproval(Request $request, Model $record): void
    {
        abort_unless($request->user()->canApprove(), 403);
        abort_if((int) $record->created_by === (int) $request->user()->id, 403, 'A different supervisor must approve this record.');
    }

    protected function scopeRecordsToOwner(Request $request, $query)
    {
        if ($request->user()->isOperator()) {
            $query->where('created_by', $request->user()->id);
        }

        return $query;
    }
}