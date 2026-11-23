<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Services\ReportCache;
use App\Services\SystemSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SystemSettingsController extends Controller
{
    public function edit(Request $request, SystemSettings $settings)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $values = $settings->all();
        $mailer = config('mail.default');
        $emailConfigured = !in_array($mailer, ['log', 'array'], true);
        return view('settings.edit', compact('values', 'mailer', 'emailConfigured'));
    }

    public function update(Request $request, SystemSettings $settings)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'organization_name' => 'required|string|max:100',
            'support_email' => 'nullable|email|max:255',
            'idle_timeout' => 'required|integer|between:5,120',
            'default_page_size' => ['required', 'integer', Rule::in([10, 20, 25, 50, 100])],
            'capture_cost_limit' => 'required|numeric|between:1,100000|decimal:0,2',
            'ph_min' => 'required|numeric|between:6.5,8.5|decimal:0,2',
            'ph_max' => 'required|numeric|between:6.5,8.5|gte:ph_min|decimal:0,2',
            'tds_max' => 'required|integer|between:1,600',
            'turbidity_max' => 'required|numeric|between:0.01,5|decimal:0,2',
        ]);
        unset($data['current_password']);
        foreach (['idle_timeout', 'default_page_size', 'tds_max'] as $field) {
            $data[$field] = (int) $data[$field];
        }
        foreach (['capture_cost_limit', 'ph_min', 'ph_max', 'turbidity_max'] as $field) {
            $data[$field] = (float) $data[$field];
        }
        DB::transaction(function () use ($data) {
            $record = SystemSetting::firstOrCreate(['key' => 'general'], ['values' => []]);
            $record = SystemSetting::lockForUpdate()->findOrFail($record->id);
            $before = array_replace(require config_path('rwims.php'), $record->values);
            $record->update(['values' => $data]);
            AuditLog::record($record, 'settings_updated', ['before' => $before, 'after' => $data]);
        });
        $settings->all(true);
        ReportCache::flush();
        return redirect()->route('settings.edit')->with('success', 'System settings saved. New thresholds apply to current reports; stored measurements and costs were not changed.');
    }
}
