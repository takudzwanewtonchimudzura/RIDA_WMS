@extends('layouts.app')

@php($editing = $maintenance->exists)
@section('title', $editing ? 'Edit Irrigation Maintenance' : 'New Irrigation Maintenance')
@section('page-title', 'Irrigation Schemes')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><a href="{{ $editing ? route('irrigation-maintenances.show', $maintenance) : route('irrigation-maintenances.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Maintenance records</a><h1 class="h3 mt-2 mb-0">{{ $editing ? 'Edit maintenance record' : 'Record irrigation maintenance' }}</h1></div>
    @if($schemes->isEmpty())<div class="alert alert-info">No irrigation schemes are available in your records.</div>@endif
    <form method="POST" action="{{ $editing ? route('irrigation-maintenances.update', $maintenance) : route('irrigation-maintenances.store') }}" class="border rounded p-4 bg-white">
        @csrf
        @if($editing)@method('PUT')@endif
        <div class="row g-3">
            <div class="col-md-6"><label for="irrigation_scheme_id" class="form-label">Irrigation scheme <span class="text-danger">*</span></label><select id="irrigation_scheme_id" name="irrigation_scheme_id" class="form-select @error('irrigation_scheme_id') is-invalid @enderror" required><option value="">Select scheme</option>@foreach($schemes as $scheme)<option value="{{ $scheme->id }}" @selected(old('irrigation_scheme_id', $maintenance->irrigation_scheme_id) == $scheme->id)>{{ $scheme->name }} · {{ $scheme->village }}, Ward {{ $scheme->ward }}</option>@endforeach</select>@error('irrigation_scheme_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="repaired_by" class="form-label">Repaired by <span class="text-danger">*</span></label><input id="repaired_by" name="repaired_by" value="{{ old('repaired_by', $maintenance->repaired_by) }}" class="form-control" maxlength="255" required></div>
            <div class="col-md-6"><label for="date_repaired" class="form-label">Repair date <span class="text-danger">*</span></label><input id="date_repaired" type="date" name="date_repaired" value="{{ old('date_repaired', $maintenance->date_repaired?->format('Y-m-d')) }}" class="form-control" required></div>
            <div class="col-md-6"><label for="date_last_repaired" class="form-label">Previous repair date</label><input id="date_last_repaired" type="date" name="date_last_repaired" value="{{ old('date_last_repaired', $maintenance->date_last_repaired?->format('Y-m-d')) }}" class="form-control">@error('date_last_repaired')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label for="works_done" class="form-label">Work performed <span class="text-danger">*</span></label><textarea id="works_done" name="works_done" rows="4" class="form-control" maxlength="4000" required>{{ old('works_done', $maintenance->works_done) }}</textarea></div>
            <div class="col-md-6"><label for="spares_fitted" class="form-label">Spares fitted</label><textarea id="spares_fitted" name="spares_fitted" rows="3" class="form-control" maxlength="4000">{{ old('spares_fitted', $maintenance->spares_fitted) }}</textarea></div>
            <div class="col-md-6"><label for="spares_received_by" class="form-label">Spares received by</label><input id="spares_received_by" name="spares_received_by" value="{{ old('spares_received_by', $maintenance->spares_received_by) }}" class="form-control" maxlength="255"></div>
            <div class="col-md-6"><label for="contact_details" class="form-label">Contact details</label><input id="contact_details" name="contact_details" value="{{ old('contact_details', $maintenance->contact_details) }}" class="form-control" maxlength="1000"></div>
            <div class="col-md-6"><label for="remarks" class="form-label">Remarks</label><textarea id="remarks" name="remarks" rows="2" class="form-control" maxlength="4000">{{ old('remarks', $maintenance->remarks) }}</textarea></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ $editing ? route('irrigation-maintenances.show', $maintenance) : route('irrigation-maintenances.index') }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary" type="submit" @disabled($schemes->isEmpty())>{{ $editing ? 'Save changes' : 'Save record' }}</button></div>
    </form>
</div>
@endsection