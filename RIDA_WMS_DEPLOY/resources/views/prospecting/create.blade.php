@extends('layouts.app')

@section('title', 'New Prospecting Survey')
@section('page-title', 'Prospecting')
@section('content')
<div class="container-fluid" style="max-width: 1000px">
    <div class="mb-4"><a href="{{ route('prospecting.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Prospecting sites</a><h1 class="h3 mt-2">New site survey</h1></div>
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('prospecting.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-8"><label for="project_name" class="form-label">Project name</label><input id="project_name" name="project_name" value="{{ old('project_name') }}" class="form-control"></div>
            <div class="col-md-4"><label for="survey_date" class="form-label">Survey date *</label><input id="survey_date" type="date" name="survey_date" value="{{ old('survey_date', today()->toDateString()) }}" required class="form-control"></div>
            <div class="col-md-6"><label for="village" class="form-label">Village *</label><input id="village" name="village" value="{{ old('village') }}" required class="form-control"></div>
            <div class="col-md-2"><label for="ward" class="form-label">Ward *</label><input id="ward" name="ward" value="{{ old('ward') }}" required class="form-control"></div>
            <div class="col-md-4"><label for="district" class="form-label">District *</label><input id="district" name="district" value="{{ old('district') }}" required class="form-control"></div>
            <div class="col-md-6"><label for="province" class="form-label">Province *</label><input id="province" name="province" value="{{ old('province') }}" required class="form-control"></div>
            <div class="col-md-3"><label for="latitude" class="form-label">Latitude</label><input id="latitude" type="number" step="0.0000001" min="-90" max="90" name="latitude" value="{{ old('latitude') }}" class="form-control"></div>
            <div class="col-md-3"><label for="longitude" class="form-label">Longitude</label><input id="longitude" type="number" step="0.0000001" min="-180" max="180" name="longitude" value="{{ old('longitude') }}" class="form-control"></div>
            <div class="col-md-6"><label for="siting_agency" class="form-label">Siting agency</label><select id="siting_agency" name="siting_agency" class="form-select"><option value="">Select agency</option><option value="Government" @selected(old('siting_agency') === 'Government')>Government</option><option value="PISP" @selected(old('siting_agency') === 'PISP')>PISP</option></select></div>
            <div class="col-md-6"><label for="prospector_name" class="form-label">Prospector</label><input id="prospector_name" name="prospector_name" value="{{ old('prospector_name', auth()->user()->name) }}" class="form-control"></div>
            <div class="col-12"><label for="remarks" class="form-label">Survey notes</label><textarea id="remarks" name="remarks" rows="4" class="form-control">{{ old('remarks') }}</textarea></div>
        </div>
        <div class="d-flex justify-content-between mt-4"><a href="{{ route('prospecting.index') }}" class="btn btn-outline-secondary">Cancel</a><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1" aria-hidden="true"></i>Submit survey</button></div>
    </form>
</div>
@endsection