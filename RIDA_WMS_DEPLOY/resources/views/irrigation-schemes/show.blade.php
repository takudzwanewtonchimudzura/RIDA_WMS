@extends('layouts.app')

@section('title', $scheme->name)
@section('breadcrumb')
	<li class="breadcrumb-item"><a href="{{ route('irrigation-schemes.index') }}">Irrigation Schemes</a></li>
	<li class="breadcrumb-item active">{{ $scheme->name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
	<h2>{{ $scheme->name }}</h2>
	<div>
		<a href="{{ route('irrigation-schemes.index') }}" class="btn btn-outline-secondary">Back</a>
		<a href="{{ route('irrigation-schemes.edit', $scheme) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Edit</a>
	</div>
</div>
<div class="card">
	<div class="card-body">
		<div class="row">
			<div class="col-md-4"><strong>Village</strong><p>{{ $scheme->village }}</p></div>
			<div class="col-md-4"><strong>Ward</strong><p>{{ $scheme->ward }}</p></div>
			<div class="col-md-4"><strong>Functionality</strong><p>{{ $scheme->functionality }}</p></div>
			<div class="col-md-4"><strong>Irrigation Type</strong><p>{{ $scheme->irrigation_type }}</p></div>
			<div class="col-md-4"><strong>Water Source</strong><p>{{ $scheme->water_source_type }}</p></div>
			<div class="col-md-4"><strong>Area</strong><p>{{ $scheme->formatted_size }}</p></div>
			<div class="col-md-4"><strong>Year Established</strong><p>{{ $scheme->year_established ?? 'N/A' }}</p></div>
			<div class="col-md-4"><strong>Established By</strong><p>{{ $scheme->established_by ?? 'N/A' }}</p></div>
			<div class="col-md-4"><strong>Pump / Power</strong><p>{{ $scheme->pump_type ?? 'N/A' }} / {{ $scheme->power_type ?? 'N/A' }}</p></div>
			<div class="col-md-4"><strong>Beneficiary Households</strong><p>{{ $scheme->beneficiary_households ?? 'N/A' }}</p></div>
			<div class="col-md-4"><strong>Plot Size</strong><p>{{ $scheme->plot_size ?? 'N/A' }}</p></div>
			<div class="col-md-4"><strong>Storage Capacity</strong><p>{{ $scheme->total_storage_tanks_m3 ?? 'N/A' }} m3</p></div>
			<div class="col-md-12"><strong>Crop Pattern</strong><p>{{ $scheme->crop_pattern ?? 'N/A' }}</p></div>
			<div class="col-md-12"><strong>Functionality Comments</strong><p>{{ $scheme->functionality_comments ?? 'N/A' }}</p></div>
			<div class="col-md-12"><strong>Remarks</strong><p>{{ $scheme->remarks ?? 'N/A' }}</p></div>
		</div>
	</div>
</div>
@endsection
