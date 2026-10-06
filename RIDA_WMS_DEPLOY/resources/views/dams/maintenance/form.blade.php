@extends('layouts.app')

@section('title', isset($maintenance) ? 'Edit Dam Maintenance' : 'Add Dam Maintenance')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dams.index') }}">Dams</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dams.show', $dam) }}">{{ $dam->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dams.maintenance.index', $dam) }}">Maintenance</a></li>
    <li class="breadcrumb-item active">{{ isset($maintenance) ? 'Edit' : 'Add' }}</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header"><h2 class="h5 mb-0">{{ isset($maintenance) ? 'Edit' : 'Add' }} Maintenance Record for {{ $dam->name }}</h2></div>
    <div class="card-body">
        <form method="POST" action="{{ isset($maintenance) ? route('dams.maintenance.update', [$dam, $maintenance]) : route('dams.maintenance.store', $dam) }}">
            @csrf
            @if(isset($maintenance)) @method('PUT') @endif
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="maintenance_type" class="form-label">Maintenance Type *</label>
                    <select id="maintenance_type" name="maintenance_type" class="form-select" required>
                        @foreach($maintenanceTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('maintenance_type', $maintenance->maintenance_type ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('maintenance_type')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="maintenance_date" class="form-label">Date *</label>
                    <input type="date" id="maintenance_date" name="maintenance_date" class="form-control" value="{{ old('maintenance_date', isset($maintenance) ? $maintenance->maintenance_date->format('Y-m-d') : '') }}" required>
                    @error('maintenance_date')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">Status *</label>
                    <select id="status" name="status" class="form-select" required>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $maintenance->status ?? 'planned') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3"><label for="wall_repair_details" class="form-label">Wall Repair Details</label><textarea id="wall_repair_details" name="wall_repair_details" class="form-control">{{ old('wall_repair_details', $maintenance->wall_repair_details ?? '') }}</textarea></div>
                <div class="col-md-6 mb-3"><label for="spillway_repair_details" class="form-label">Spillway Repair Details</label><textarea id="spillway_repair_details" name="spillway_repair_details" class="form-control">{{ old('spillway_repair_details', $maintenance->spillway_repair_details ?? '') }}</textarea></div>
                <div class="col-md-6 mb-3"><label for="other_works" class="form-label">Other Works</label><textarea id="other_works" name="other_works" class="form-control">{{ old('other_works', $maintenance->other_works ?? '') }}</textarea></div>
                <div class="col-md-3 mb-3"><label for="estimated_cost" class="form-label">Estimated Cost</label><input type="number" min="0" step="0.01" id="estimated_cost" name="estimated_cost" class="form-control" value="{{ old('estimated_cost', $maintenance->estimated_cost ?? '') }}"></div>
                <div class="col-md-3 mb-3"><label for="actual_cost" class="form-label">Actual Cost</label><input type="number" min="0" step="0.01" id="actual_cost" name="actual_cost" class="form-control" value="{{ old('actual_cost', $maintenance->actual_cost ?? '') }}"></div>
                <div class="col-md-6 mb-3"><label for="contractor_name" class="form-label">Contractor</label><input id="contractor_name" name="contractor_name" class="form-control" value="{{ old('contractor_name', $maintenance->contractor_name ?? '') }}"></div>
                <div class="col-md-6 mb-3"><label for="contractor_contact" class="form-label">Contractor Contact</label><input id="contractor_contact" name="contractor_contact" class="form-control" value="{{ old('contractor_contact', $maintenance->contractor_contact ?? '') }}"></div>
                <div class="col-md-6 mb-3 form-check ms-2"><input type="checkbox" id="stone_pitching_replaced" name="stone_pitching_replaced" value="1" class="form-check-input" @checked(old('stone_pitching_replaced', $maintenance->stone_pitching_replaced ?? false))><label for="stone_pitching_replaced" class="form-check-label">Stone pitching replaced</label></div>
                <div class="col-md-6 mb-3 form-check ms-2"><input type="checkbox" id="star_grass_replanted" name="star_grass_replanted" value="1" class="form-check-input" @checked(old('star_grass_replanted', $maintenance->star_grass_replanted ?? false))><label for="star_grass_replanted" class="form-check-label">Star grass replanted</label></div>
                <div class="col-md-12 mb-3"><label for="general_comments" class="form-label">Comments</label><textarea id="general_comments" name="general_comments" class="form-control">{{ old('general_comments', $maintenance->general_comments ?? '') }}</textarea></div>
            </div>
            <div class="d-flex justify-content-between">
                <a href="{{ route('dams.maintenance.index', $dam) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">{{ isset($maintenance) ? 'Update' : 'Save' }} Record</button>
            </div>
        </form>
    </div>
</div>
@endsection