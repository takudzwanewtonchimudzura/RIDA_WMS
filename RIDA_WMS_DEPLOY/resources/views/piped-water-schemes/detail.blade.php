@extends('layouts.app')

@section('title', $scheme->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('piped-water-schemes.index') }}">Piped Water Schemes</a></li>
    <li class="breadcrumb-item active">{{ $scheme->name }}</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>{{ $scheme->name }}</h2>
    <div>
        <a href="{{ route('piped-water-schemes.index') }}" class="btn btn-outline-secondary">Back</a>
        <a href="{{ route('piped-water-schemes.edit', $scheme) }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i>Edit</a>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4"><strong>Village</strong><p>{{ $scheme->village }}</p></div>
            <div class="col-md-4"><strong>Ward</strong><p>{{ $scheme->ward }}</p></div>
            <div class="col-md-4"><strong>Functionality</strong><p>{{ $scheme->functionality }}</p></div>
            <div class="col-md-4"><strong>Water Source</strong><p>{{ $scheme->water_source }}@if($scheme->water_source_other) ({{ $scheme->water_source_other }})@endif</p></div>
            <div class="col-md-4"><strong>Year Established</strong><p>{{ $scheme->year_established }}</p></div>
            <div class="col-md-4"><strong>Established By</strong><p>{{ $scheme->established_by }}</p></div>
            <div class="col-md-4"><strong>Pump / Power</strong><p>{{ $scheme->pump_type }} / {{ $scheme->power_type }}</p></div>
            <div class="col-md-4"><strong>Network Length</strong><p>{{ number_format((float) $scheme->network_length, 2) }} m</p></div>
            <div class="col-md-4"><strong>Storage Capacity</strong><p>{{ number_format((float) $scheme->storage_tanks, 2) }} m3</p></div>
            <div class="col-md-4"><strong>Stand Pipes</strong><p>{{ $scheme->stand_pipes_count }}</p></div>
            <div class="col-md-4"><strong>Households Served</strong><p>{{ number_format($scheme->households_served) }}</p></div>
            <div class="col-md-4"><strong>Population Served</strong><p>{{ number_format($scheme->population_served) }}</p></div>
            <div class="col-md-4"><strong>Women / Men / Children</strong><p>{{ number_format($scheme->women_served) }} / {{ number_format($scheme->men_served) }} / {{ number_format($scheme->children_served) }}</p></div>
            <div class="col-md-12"><strong>Functionality Issues</strong><p>{{ $scheme->functionality_issues ?? 'N/A' }}</p></div>
            <div class="col-md-12"><strong>Remarks</strong><p>{{ $scheme->remarks ?? 'N/A' }}</p></div>
        </div>
    </div>
</div>
@endsection