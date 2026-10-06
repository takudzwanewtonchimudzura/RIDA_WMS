@extends('layouts.app')

@section('title', 'Edit Irrigation Scheme')
@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('irrigation-schemes.index') }}">Irrigation Schemes</a></li>
	<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header bg-primary text-white">
				<h4 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Irrigation Scheme</h4>
			</div>
			<div class="card-body">
				<form action="{{ route('irrigation-schemes.update', $scheme) }}" method="POST">
					@csrf
					@method('PUT')
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="name" class="form-label">Scheme Name *</label>
							<input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $scheme->name) }}" required>
							@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-6 mb-3">
							<label for="village" class="form-label">Village *</label>
							<input id="village" name="village" class="form-control @error('village') is-invalid @enderror" value="{{ old('village', $scheme->village) }}" required>
							@error('village')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="ward" class="form-label">Ward *</label>
							<input id="ward" name="ward" class="form-control @error('ward') is-invalid @enderror" value="{{ old('ward', $scheme->ward) }}" required>
							@error('ward')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="irrigation_type" class="form-label">Irrigation Type *</label>
							<input id="irrigation_type" name="irrigation_type" class="form-control @error('irrigation_type') is-invalid @enderror" value="{{ old('irrigation_type', $scheme->irrigation_type) }}" required>
							@error('irrigation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="water_source_type" class="form-label">Water Source *</label>
							<input id="water_source_type" name="water_source_type" class="form-control @error('water_source_type') is-invalid @enderror" value="{{ old('water_source_type', $scheme->water_source_type) }}" required>
							@error('water_source_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="year_established" class="form-label">Year Established</label>
							<input type="number" id="year_established" name="year_established" class="form-control @error('year_established') is-invalid @enderror" value="{{ old('year_established', $scheme->year_established) }}" min="1900" max="{{ date('Y') }}">
							@error('year_established')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="established_by" class="form-label">Established By</label>
							<input id="established_by" name="established_by" class="form-control @error('established_by') is-invalid @enderror" value="{{ old('established_by', $scheme->established_by) }}">
							@error('established_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="pump_type" class="form-label">Pump Type</label>
							<input id="pump_type" name="pump_type" class="form-control @error('pump_type') is-invalid @enderror" value="{{ old('pump_type', $scheme->pump_type) }}">
							@error('pump_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="power_type" class="form-label">Power Type</label>
							<input id="power_type" name="power_type" class="form-control @error('power_type') is-invalid @enderror" value="{{ old('power_type', $scheme->power_type) }}">
							@error('power_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="size_hectares" class="form-label">Area (hectares)</label>
							<input type="number" step="0.01" min="0" id="size_hectares" name="size_hectares" class="form-control @error('size_hectares') is-invalid @enderror" value="{{ old('size_hectares', $scheme->size_hectares) }}">
							@error('size_hectares')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="beneficiary_households" class="form-label">Beneficiary Households</label>
							<input type="number" min="0" id="beneficiary_households" name="beneficiary_households" class="form-control @error('beneficiary_households') is-invalid @enderror" value="{{ old('beneficiary_households', $scheme->beneficiary_households) }}">
							@error('beneficiary_households')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="plot_size" class="form-label">Plot Size</label>
							<input type="number" step="0.01" min="0" id="plot_size" name="plot_size" class="form-control @error('plot_size') is-invalid @enderror" value="{{ old('plot_size', $scheme->plot_size) }}">
							@error('plot_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="total_storage_tanks_m3" class="form-label">Storage Capacity (m3)</label>
							<input type="number" step="0.01" min="0" id="total_storage_tanks_m3" name="total_storage_tanks_m3" class="form-control @error('total_storage_tanks_m3') is-invalid @enderror" value="{{ old('total_storage_tanks_m3', $scheme->total_storage_tanks_m3) }}">
							@error('total_storage_tanks_m3')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-8 mb-3">
							<label for="crop_pattern" class="form-label">Crop Pattern</label>
							<input id="crop_pattern" name="crop_pattern" class="form-control @error('crop_pattern') is-invalid @enderror" value="{{ old('crop_pattern', $scheme->crop_pattern) }}">
							@error('crop_pattern')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-4 mb-3">
							<label for="functionality" class="form-label">Functionality *</label>
							<select id="functionality" name="functionality" class="form-select @error('functionality') is-invalid @enderror" required>
								@foreach(['Functional', 'Partially Functional', 'Nonfunctional'] as $status)
									<option value="{{ $status }}" @selected(old('functionality', $scheme->functionality) === $status)>{{ $status }}</option>
								@endforeach
							</select>
							@error('functionality')<div class="invalid-feedback">{{ $message }}</div>@enderror
						</div>
						<div class="col-md-12 mb-3">
							<label for="functionality_comments" class="form-label">Functionality Comments</label>
							<textarea id="functionality_comments" name="functionality_comments" class="form-control">{{ old('functionality_comments', $scheme->functionality_comments) }}</textarea>
						</div>
						<div class="col-md-12 mb-3">
							@foreach(['management_committee_available' => 'Management committee available', 'is_trained' => 'Committee trained', 'is_functional' => 'Committee functional', 'revolving_fund' => 'Has revolving fund'] as $field => $label)
								<label class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="{{ $field }}" value="1" @checked(old($field, $field === 'management_committee_available' ? $scheme->has_management_committee : ($field === 'is_trained' ? $scheme->committee_trained : ($field === 'is_functional' ? $scheme->committee_functional : $scheme->has_revolving_fund))))>
									<span class="form-check-label">{{ $label }}</span>
								</label>
							@endforeach
						</div>
						<div class="col-md-4 mb-3">
							<label for="fund_amount_available" class="form-label">Fund Amount</label>
							<input type="number" min="0" step="0.01" id="fund_amount_available" name="fund_amount_available" class="form-control" value="{{ old('fund_amount_available', $scheme->fund_amount_available) }}">
						</div>
						<div class="col-md-4 mb-3">
							<label for="committee_total_members" class="form-label">Committee Members</label>
							<input type="number" min="0" id="committee_total_members" name="committee_total_members" class="form-control" value="{{ old('committee_total_members', $scheme->committee_total_members) }}">
						</div>
						<div class="col-md-4 mb-3">
							<label for="committee_women" class="form-label">Women Members</label>
							<input type="number" min="0" id="committee_women" name="committee_women" class="form-control" value="{{ old('committee_women', $scheme->committee_women) }}">
						</div>
						<div class="col-md-4 mb-3">
							<label for="committee_men" class="form-label">Men Members</label>
							<input type="number" min="0" id="committee_men" name="committee_men" class="form-control" value="{{ old('committee_men', $scheme->committee_men) }}">
						</div>
						<div class="col-md-12 mb-3">
							<label for="remarks" class="form-label">Remarks</label>
							<textarea id="remarks" name="remarks" class="form-control">{{ old('remarks', $scheme->remarks) }}</textarea>
						</div>
					</div>
					<div class="d-flex justify-content-between">
						<a href="{{ route('irrigation-schemes.show', $scheme) }}" class="btn btn-secondary">Cancel</a>
						<button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Scheme</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection
