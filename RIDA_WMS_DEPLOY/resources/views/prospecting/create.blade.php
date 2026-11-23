@extends('layouts.app')
@php $site = $prospectingSite ?? null; @endphp

@section('title', $site ? 'Correct Prospecting Survey' : 'New Prospecting Survey')
@section('page-title', 'Prospecting')
@section('content')
<div class="container-fluid" style="max-width: 1000px">
    <div class="mb-4"><a href="{{ route('prospecting.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Prospecting sites</a><h1 class="h3 mt-2">{{ $site ? 'Correct site survey' : 'New site survey' }}</h1></div>
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $site ? route('prospecting.update', $site) : route('prospecting.store') }}">
        @csrf
        @if($site) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-8"><label for="project_name" class="form-label">Project name</label><input id="project_name" name="project_name" value="{{ old('project_name', $site?->project_name) }}" class="form-control"></div>
            <div class="col-md-4"><label for="survey_date" class="form-label">Survey date *</label><input id="survey_date" type="date" name="survey_date" max="{{ today()->toDateString() }}" value="{{ old('survey_date', $site?->survey_date?->toDateString() ?? today()->toDateString()) }}" required class="form-control"></div>
            <div class="col-md-6"><label for="village" class="form-label">Village *</label><input id="village" name="village" value="{{ old('village', $site?->village) }}" required class="form-control"></div>
            <div class="col-md-2"><label for="ward" class="form-label">Ward *</label><input id="ward" name="ward" value="{{ old('ward', $site?->ward) }}" required class="form-control"></div>
            <div class="col-md-4"><label for="district" class="form-label">District *</label><input id="district" name="district" value="{{ old('district', $site?->district) }}" required class="form-control"></div>
            <div class="col-md-6"><label for="province" class="form-label">Province *</label><input id="province" name="province" value="{{ old('province', $site?->province) }}" required class="form-control"></div>
            <div class="col-md-3"><label for="latitude" class="form-label">Latitude</label><input id="latitude" type="number" step="0.0000001" min="-22.5" max="-15.5" name="latitude" value="{{ old('latitude', $site?->latitude) }}" class="form-control"></div>
            <div class="col-md-3"><label for="longitude" class="form-label">Longitude</label><input id="longitude" type="number" step="0.0000001" min="25" max="33.2" name="longitude" value="{{ old('longitude', $site?->longitude) }}" class="form-control"></div>
            <div class="col-md-6"><label for="siting_agency" class="form-label">Siting agency</label><select id="siting_agency" name="siting_agency" class="form-select"><option value="">Select agency</option><option value="Government" @selected(old('siting_agency', $site->siting_agency ?? '') === 'Government')>Government</option><option value="PISP" @selected(old('siting_agency', $site->siting_agency ?? '') === 'PISP')>PISP</option></select></div>
            <div class="col-md-6"><label for="prospector_name" class="form-label">Prospector</label><input id="prospector_name" name="prospector_name" value="{{ old('prospector_name', $site?->prospector_name ?? auth()->user()->name) }}" class="form-control"></div>
            <div class="col-12"><label for="remarks" class="form-label">Survey notes</label><textarea id="remarks" name="remarks" rows="4" class="form-control">{{ old('remarks', $site?->remarks) }}</textarea></div>
        </div>
        <div class="d-flex justify-content-between mt-4"><a href="{{ route('prospecting.index') }}" class="btn btn-outline-secondary">Cancel</a><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1" aria-hidden="true"></i>{{ $site ? 'Save corrections' : 'Submit survey' }}</button></div>
    </form>
</div>
@endsection