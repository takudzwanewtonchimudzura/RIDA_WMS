@extends('layouts.app')
@section('title', 'System Settings')
@section('content')
<div class="container py-3" style="max-width:1000px">
    <h2>System settings</h2>
    <p class="text-muted">Administrator-only configuration. Changes are audited and apply to subsequent requests. Database, encryption and mail credentials remain in deployment configuration.</p>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="card card-body mb-4">
        <h3 class="h5">Deployment status (read only)</h3>
        <dl class="row mb-0">
            <dt class="col-sm-4">Environment</dt><dd class="col-sm-8">{{ app()->environment() }}</dd>
            <dt class="col-sm-4">Mail transport</dt><dd class="col-sm-8">{{ $mailer }} &mdash; {{ $emailConfigured ? 'Transport selected; delivery requires a verified provider configuration' : 'Not delivering email; reset requests show a configuration error' }}</dd>
            <dt class="col-sm-4">Public application URL</dt><dd class="col-sm-8 text-break">{{ config('app.url') }}</dd>
            <dt class="col-sm-4">Reset link lifetime</dt><dd class="col-sm-8">{{ config('auth.passwords.users.expire') }} minutes</dd>
            <dt class="col-sm-4">Reset link request interval</dt><dd class="col-sm-8">{{ config('auth.passwords.users.throttle') }} seconds per account, plus request rate limits</dd>
            <dt class="col-sm-4">Session storage</dt><dd class="col-sm-8">{{ config('session.driver') }}</dd>
            <dt class="col-sm-4">Application timezone</dt><dd class="col-sm-8">{{ config('app.timezone') }}</dd>
        </dl>
        @unless($emailConfigured)<div class="alert alert-warning mb-0 mt-2">Configure SMTP or another supported provider privately before relying on email resets. Set APP_URL to the address users can actually reach. Never enter provider passwords in this form.</div>@endunless
    </div>
    <form method="POST" action="{{ route('settings.update') }}" class="card card-body">
        @csrf @method('PUT')
        <h3 class="h5">Organization and display</h3>
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label for="organization_name" class="form-label">Organization / application name</label><input id="organization_name" name="organization_name" value="{{ old('organization_name', $values['organization_name']) }}" required maxlength="100" class="form-control"></div>
            <div class="col-md-6"><label for="support_email" class="form-label">Support email (optional)</label><input id="support_email" name="support_email" type="email" value="{{ old('support_email', $values['support_email']) }}" class="form-control" maxlength="255"></div>
            <div class="col-md-6"><label for="idle_timeout" class="form-label">Idle timeout (5-120 minutes)</label><input id="idle_timeout" name="idle_timeout" type="number" min="5" max="120" value="{{ old('idle_timeout', $values['idle_timeout']) }}" required class="form-control"><small class="text-muted">An active browser sends keep-alive requests; this limit measures inactivity.</small></div>
            <div class="col-md-6"><label for="default_page_size" class="form-label">Default list page size</label><select id="default_page_size" name="default_page_size" class="form-select" required>@foreach([10,20,25,50,100] as $size)<option value="{{ $size }}" @selected((int) old('default_page_size', $values['default_page_size']) === $size)>{{ $size }}</option>@endforeach</select></div>
        </div>
        <h3 class="h5">Capture and screening policy</h3>
        <div class="alert alert-info">These controls may tighten the UAT limits, not relax them: cost maximum 100,000; pH within 6.5-8.5; TDS at most 600 mg/L; turbidity at most 5 NTU. Changes reclassify current reports, not historical measurements. This is not full drinking-water certification.</div>
        <div class="row g-3">
            <div class="col-md-6"><label for="capture_cost_limit" class="form-label">Maximum captured cost / unit price / fund amount</label><input id="capture_cost_limit" name="capture_cost_limit" type="number" min="1" max="100000" step="0.01" value="{{ old('capture_cost_limit', $values['capture_cost_limit']) }}" required class="form-control"><small class="text-muted">Applies to borehole USD cost, stock unit costs, invoice unit prices/tax, scheme funds and dam-maintenance amounts. Not an invoice-total or project-budget limit.</small></div>
            @foreach(['ph_min' => ['Minimum pH', 6.5, 8.5, '0.01'], 'ph_max' => ['Maximum pH', 6.5, 8.5, '0.01'], 'tds_max' => ['Maximum TDS (mg/L)', 1, 600, '1'], 'turbidity_max' => ['Maximum turbidity (NTU)', 0.01, 5, '0.01']] as $field => [$label,$min,$max,$step])
            <div class="col-md-6"><label for="{{ $field }}" class="form-label">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" type="number" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" value="{{ old($field, $values[$field]) }}" required class="form-control"></div>
            @endforeach
            <div class="col-12"><label for="current_password" class="form-label">Your current password to authorize changes</label><input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="form-control"></div>
        </div>
        <div class="mt-4"><button type="submit" class="btn btn-primary">Save settings</button></div>
    </form>
</div>
@endsection
