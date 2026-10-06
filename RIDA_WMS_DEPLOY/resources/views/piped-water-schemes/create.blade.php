{{-- resources/views/piped-water-schemes/create.blade.php --}}
@extends('layouts.app')

@section('title', isset($pipedWaterScheme) ? 'Edit Piped Water Scheme' : 'Create New Piped Water Scheme')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1 class="h3 mb-0">{{ isset($pipedWaterScheme) ? 'Edit' : 'Add New' }} Piped Water Scheme</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('piped-water-schemes.index') }}">Piped Water Schemes</a></li>
                    <li class="breadcrumb-item active">{{ isset($pipedWaterScheme) ? 'Edit' : 'Add New' }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header text-center">
                    <h2 class="mb-0"><i class="fas fa-water-pump me-2"></i>{{ isset($pipedWaterScheme) ? 'Edit Piped Water Scheme' : 'Add New Piped Water Scheme' }}</h2>
                    <p class="mb-0 mt-2">Complete all sections to create a comprehensive water scheme record</p>
                </div>
                <div class="card-body">
                    <!-- Progress Bar -->
                    <div class="progress mb-4">
                        <div class="progress-bar" id="form-progress" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    <!-- Navigation Pills -->
                    <div class="form-navigation">
                        <div class="nav-pills-custom">
                            <div class="nav-pills-container">
                                <button class="nav-pill active" data-section="0">
                                    <i class="fas fa-info-circle"></i> 
                                    <span>Basic Info</span>
                                </button>
                                <button class="nav-pill" data-section="1">
                                    <i class="fas fa-tools"></i> 
                                    <span>Technical Specs</span>
                                </button>
                                <button class="nav-pill" data-section="2">
                                    <i class="fas fa-users"></i> 
                                    <span>Service Coverage</span>
                                </button>
                                <button class="nav-pill" data-section="3">
                                    <i class="fas fa-users-cog"></i> 
                                    <span>Management</span>
                                </button>
                                <button class="nav-pill" data-section="4">
                                    <i class="fas fa-clipboard-check"></i> 
                                    <span>Review</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Sections -->
                    <form method="POST" action="{{ isset($pipedWaterScheme) ? route('piped-water-schemes.update', $pipedWaterScheme) : route('piped-water-schemes.store') }}" id="schemeForm">
                        @csrf
                        @if(isset($pipedWaterScheme))
                            @method('PUT')
                        @endif
                        
                        <!-- Section 1: Basic Information -->
                        <div class="form-section active" id="section-0">
                            <h4 class="section-title"><i class="fas fa-info-circle text-primary"></i> Basic Information</h4>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label required">Scheme Name</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-tag"></i>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $pipedWaterScheme->name ?? '') }}" required placeholder="Enter scheme name">
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="village" class="form-label required">Village</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-home"></i>
                                        <input type="text" class="form-control" id="village" name="village" value="{{ old('village', $pipedWaterScheme->village ?? '') }}" required placeholder="Enter village name">
                                    </div>
                                    @error('village')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="ward" class="form-label required">Ward</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-map-pin"></i>
                                        <input type="text" class="form-control" id="ward" name="ward" value="{{ old('ward', $pipedWaterScheme->ward ?? '') }}" required placeholder="Enter ward">
                                    </div>
                                    @error('ward')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="water_source" class="form-label required">Water Source</label>
                                    <select class="form-control" id="water_source" name="water_source" required>
                                        <option value="">Select Source</option>
                                        <option value="borehole" {{ old('water_source', $pipedWaterScheme->water_source ?? '') == 'borehole' ? 'selected' : '' }}>Borehole</option>
                                        <option value="deep well" {{ old('water_source', $pipedWaterScheme->water_source ?? '') == 'deep well' ? 'selected' : '' }}>Deep Well</option>
                                        <option value="spring" {{ old('water_source', $pipedWaterScheme->water_source ?? '') == 'spring' ? 'selected' : '' }}>Spring</option>
                                        <option value="river" {{ old('water_source', $pipedWaterScheme->water_source ?? '') == 'river' ? 'selected' : '' }}>River</option>
                                        <option value="dam" {{ old('water_source', $pipedWaterScheme->water_source ?? '') == 'dam' ? 'selected' : '' }}>Dam</option>
                                        <option value="sand abstraction" {{ old('water_source', $pipedWaterScheme->water_source ?? '') == 'sand abstraction' ? 'selected' : '' }}>Sand Abstraction</option>
                                        <option value="other" {{ old('water_source', $pipedWaterScheme->water_source ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('water_source')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3" id="other_source_div" style="display: none;">
                                    <label for="water_source_other" class="form-label">Specify Other Source</label>
                                    <input type="text" class="form-control" id="water_source_other" name="water_source_other" value="{{ old('water_source_other', $pipedWaterScheme->water_source_other ?? '') }}" placeholder="Specify other water source">
                                    @error('water_source_other')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="year_established" class="form-label required">Year Established</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                        <input type="number" class="form-control" id="year_established" name="year_established" value="{{ old('year_established', $pipedWaterScheme->year_established ?? '') }}" min="1900" max="{{ date('Y') }}" required placeholder="e.g., 2010">
                                    </div>
                                    @error('year_established')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="established_by" class="form-label required">Established By</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-user-check"></i>
                                        <input type="text" class="form-control" id="established_by" name="established_by" value="{{ old('established_by', $pipedWaterScheme->established_by ?? '') }}" required placeholder="Organization or agency">
                                    </div>
                                    @error('established_by')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <div></div>
                                <button type="button" class="btn btn-primary next-btn" data-next="1">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 2: Technical Specifications -->
                        <div class="form-section" id="section-1">
                            <h4 class="section-title"><i class="fas fa-tools text-primary"></i> Technical Specifications</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pump_type" class="form-label required">Type of Pump</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-pump"></i>
                                        <input type="text" class="form-control" id="pump_type" name="pump_type" value="{{ old('pump_type', $pipedWaterScheme->pump_type ?? '') }}" required placeholder="e.g., Submersible, Hand Pump">
                                    </div>
                                    @error('pump_type')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="power_type" class="form-label required">Type of Power</label>
                                    <select class="form-control" id="power_type" name="power_type" required>
                                        <option value="">Select Power Type</option>
                                        <option value="solar" {{ old('power_type', $pipedWaterScheme->power_type ?? '') == 'solar' ? 'selected' : '' }}>Solar</option>
                                        <option value="electricity" {{ old('power_type', $pipedWaterScheme->power_type ?? '') == 'electricity' ? 'selected' : '' }}>Electricity</option>
                                        <option value="fossil fuel" {{ old('power_type', $pipedWaterScheme->power_type ?? '') == 'fossil fuel' ? 'selected' : '' }}>Fossil Fuel</option>
                                        <option value="wind" {{ old('power_type', $pipedWaterScheme->power_type ?? '') == 'wind' ? 'selected' : '' }}>Wind</option>
                                        <option value="gravity" {{ old('power_type', $pipedWaterScheme->power_type ?? '') == 'gravity' ? 'selected' : '' }}>Gravity</option>
                                    </select>
                                    @error('power_type')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="network_length" class="form-label required">Network Length (m)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-arrows-alt-h"></i>
                                        <input type="number" step="0.01" class="form-control" id="network_length" name="network_length" value="{{ old('network_length', $pipedWaterScheme->network_length ?? '') }}" required placeholder="Length in meters">
                                    </div>
                                    @error('network_length')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="storage_tanks" class="form-label required">Storage Tanks (m³)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-water"></i>
                                        <input type="number" step="0.01" class="form-control" id="storage_tanks" name="storage_tanks" value="{{ old('storage_tanks', $pipedWaterScheme->storage_tanks ?? '') }}" required placeholder="Capacity in cubic meters">
                                    </div>
                                    @error('storage_tanks')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="stand_pipes_count" class="form-label required">Stand Pipes</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-faucet"></i>
                                        <input type="number" class="form-control" id="stand_pipes_count" name="stand_pipes_count" value="{{ old('stand_pipes_count', $pipedWaterScheme->stand_pipes_count ?? '') }}" required placeholder="Number of stand pipes">
                                    </div>
                                    @error('stand_pipes_count')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="functionality" class="form-label required">Functionality</label>
                                    <select class="form-control" id="functionality" name="functionality" required>
                                        <option value="">Select Status</option>
                                        <option value="Functional" {{ old('functionality', $pipedWaterScheme->functionality ?? '') == 'Functional' ? 'selected' : '' }}>Functional</option>
                                        <option value="Partially Functional" {{ old('functionality', $pipedWaterScheme->functionality ?? '') == 'Partially Functional' ? 'selected' : '' }}>Partially Functional</option>
                                        <option value="Non functional" {{ old('functionality', $pipedWaterScheme->functionality ?? '') == 'Non functional' ? 'selected' : '' }}>Non functional</option>
                                    </select>
                                    @error('functionality')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="functionality_issues" class="form-label">Issues/Comments</label>
                                    <textarea class="form-control" id="functionality_issues" name="functionality_issues" rows="3" placeholder="Describe any issues, maintenance needs, or technical problems">{{ old('functionality_issues', $pipedWaterScheme->functionality_issues ?? '') }}</textarea>
                                    @error('functionality_issues')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="0">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary next-btn" data-next="2">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 3: Service Coverage -->
                        <div class="form-section" id="section-2">
                            <h4 class="section-title"><i class="fas fa-users text-primary"></i> Service Coverage</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="households_served" class="form-label required">Households Served</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-home"></i>
                                        <input type="number" class="form-control" id="households_served" name="households_served" value="{{ old('households_served', $pipedWaterScheme->households_served ?? '') }}" min="0" required placeholder="Number of households">
                                    </div>
                                    @error('households_served')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="population_served" class="form-label required">Total Population Served</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-users"></i>
                                        <input type="number" class="form-control" id="population_served" name="population_served" value="{{ old('population_served', $pipedWaterScheme->population_served ?? '') }}" min="0" required placeholder="Total population">
                                    </div>
                                    @error('population_served')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="women_served" class="form-label required">Women</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-female"></i>
                                        <input type="number" class="form-control" id="women_served" name="women_served" value="{{ old('women_served', $pipedWaterScheme->women_served ?? '') }}" min="0" required placeholder="Number of women">
                                    </div>
                                    @error('women_served')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="men_served" class="form-label required">Men</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-male"></i>
                                        <input type="number" class="form-control" id="men_served" name="men_served" value="{{ old('men_served', $pipedWaterScheme->men_served ?? '') }}" min="0" required placeholder="Number of men">
                                    </div>
                                    @error('men_served')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="children_served" class="form-label required">Children (&lt;18)</label>
                                    <div class="input-group-icon">
                                        <i class="fas fa-child"></i>
                                        <input type="number" class="form-control" id="children_served" name="children_served" value="{{ old('children_served', $pipedWaterScheme->children_served ?? '') }}" min="0" required placeholder="Number of children">
                                    </div>
                                    @error('children_served')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Coverage Calculations -->
                                <div class="col-12 mb-3">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6><i class="fas fa-calculator text-info"></i> Coverage Calculations</h6>
                                            <div class="row mt-2">
                                                <div class="col-md-4">
                                                    <label>Household Coverage</label>
                                                    <div class="form-control text-center" id="household_coverage_display">0%</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Population Coverage</label>
                                                    <div class="form-control text-center" id="population_coverage_display">0%</div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label>Gender Balance</label>
                                                    <div class="form-control text-center" id="gender_balance_display">0% Women</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="1">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary next-btn" data-next="3">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 4: Management Committee -->
                        <div class="form-section" id="section-3">
                            <h4 class="section-title"><i class="fas fa-users-cog text-primary"></i> Management Committee</h4>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_management_committee" name="has_management_committee" value="1" {{ old('has_management_committee', $pipedWaterScheme->has_management_committee ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_management_committee">
                                            Management Committee Available
                                        </label>
                                    </div>
                                    @error('has_management_committee')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3" id="committee_details" style="display: none;">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" id="committee_trained" name="committee_trained" value="1" {{ old('committee_trained', $pipedWaterScheme->committee_trained ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="committee_trained">
                                                            Committee Trained
                                                        </label>
                                                    </div>
                                                    @error('committee_trained')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" id="committee_functional" name="committee_functional" value="1" {{ old('committee_functional', $pipedWaterScheme->committee_functional ?? false) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="committee_functional">
                                                            Committee Functional
                                                        </label>
                                                    </div>
                                                    @error('committee_functional')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="committee_members_total" class="form-label">Total Members</label>
                                                    <input type="number" class="form-control" id="committee_members_total" name="committee_members_total" value="{{ old('committee_members_total', $pipedWaterScheme->committee_members_total ?? '') }}" min="0" placeholder="Total members">
                                                    @error('committee_members_total')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="committee_women" class="form-label">Women Members</label>
                                                    <input type="number" class="form-control" id="committee_women" name="committee_women" value="{{ old('committee_women', $pipedWaterScheme->committee_women ?? '') }}" min="0" placeholder="Women members">
                                                    @error('committee_women')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="committee_men" class="form-label">Men Members</label>
                                                    <input type="number" class="form-control" id="committee_men" name="committee_men" value="{{ old('committee_men', $pipedWaterScheme->committee_men ?? '') }}" min="0" placeholder="Men members">
                                                    @error('committee_men')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_revolving_fund" name="has_revolving_fund" value="1" {{ old('has_revolving_fund', $pipedWaterScheme->has_revolving_fund ?? false) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="has_revolving_fund">
                                            Revolving Fund Available
                                        </label>
                                    </div>
                                    @error('has_revolving_fund')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3" id="fund_details" style="display: none;">
                                    <div class="card border-success">
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="fund_amount" class="form-label">Fund Amount Available</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" class="form-control" id="fund_amount" name="fund_amount" value="{{ old('fund_amount', $pipedWaterScheme->fund_amount ?? '') }}" min="0" placeholder="Amount available">
                                                </div>
                                                @error('fund_amount')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="mb-0"><i class="fas fa-comments"></i> Additional Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="remarks" class="form-label">Remarks</label>
                                                <textarea class="form-control" id="remarks" name="remarks" rows="4" placeholder="Any additional comments or remarks">{{ old('remarks', $pipedWaterScheme->remarks ?? '') }}</textarea>
                                                @error('remarks')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="2">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <button type="button" class="btn btn-primary next-btn" data-next="4">
                                    Next <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Section 5: Review & Submit -->
                        <div class="form-section" id="section-4">
                            <h4 class="section-title"><i class="fas fa-clipboard-check text-primary"></i> Review & Submit</h4>
                            
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Please review all information before submitting. You can go back to edit any section.
                                    </div>
                                </div>

                                <!-- Summary Cards -->
                                <div class="col-md-6 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> Basic Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Scheme Name:</th>
                                                    <td id="review_name">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Village:</th>
                                                    <td id="review_village">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Ward:</th>
                                                    <td id="review_ward">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Water Source:</th>
                                                    <td id="review_water_source">-</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-tools"></i> Technical Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Pump Type:</th>
                                                    <td id="review_pump_type">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Power Type:</th>
                                                    <td id="review_power_type">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Network Length:</th>
                                                    <td id="review_network_length">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Functionality:</th>
                                                    <td id="review_functionality">-</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning">
                                            <h6 class="mb-0"><i class="fas fa-users"></i> Service Coverage</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Population Served:</th>
                                                    <td id="review_population_served">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Households Served:</th>
                                                    <td id="review_households_served">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Stand Pipes:</th>
                                                    <td id="review_stand_pipes">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Storage Capacity:</th>
                                                    <td id="review_storage_tanks">-</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-users-cog"></i> Management</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Management Committee:</th>
                                                    <td id="review_has_committee">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Revolving Fund:</th>
                                                    <td id="review_has_fund">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Established By:</th>
                                                    <td id="review_established_by">-</td>
                                                </tr>
                                                <tr>
                                                    <th>Year Established:</th>
                                                    <td id="review_year_established">-</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Optional: Final Verification Checkboxes (Commented out for now) -->
                                {{--
                                <div class="col-12 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning">
                                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Final Verification</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="data_verified" name="data_verified" value="1">
                                                <label class="form-check-label" for="data_verified">
                                                    I verify that all information provided is accurate to the best of my knowledge
                                                </label>
                                            </div>
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="terms_accepted" name="terms_accepted" value="1">
                                                <label class="form-check-label" for="terms_accepted">
                                                    I accept the terms and conditions for data submission
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                --}}
                            </div>
                            
                            <div class="form-footer">
                                <button type="button" class="btn btn-outline-primary prev-btn" data-prev="3">
                                    <i class="fas fa-arrow-left me-1"></i> Previous
                                </button>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('piped-water-schemes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                        <i class="fas fa-save me-2"></i> {{ isset($pipedWaterScheme) ? 'Update' : 'Save' }} Scheme
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #2c7fb8;
    --secondary-color: #7fcdbb;
    --accent-color: #edf8b1;
    --dark-color: #253237;
    --light-color: #f8f9fa;
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #17a2b8;
    --border-radius: 12px;
    --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, #1d6fa5 100%);
    --gradient-success: linear-gradient(135deg, var(--success-color) 0%, #1e7e34 100%);
    --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
}

.container {
    max-width: 1200px;
}

.card {
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    border: none;
    overflow: hidden;
    background: white;
    margin: 2rem auto;
    transition: var(--transition);
}

.card:hover {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.card-header {
    background: var(--gradient-primary);
    color: white;
    padding: 2.5rem 2rem;
    border-bottom: none;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.card-header h2, .card-header p {
    position: relative;
    z-index: 1;
    margin: 0;
}

.card-header h2 {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.card-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    font-weight: 400;
}

.card-body {
    padding: 2.5rem;
    background: var(--gradient-card);
    position: relative;
}

/* Progress Bar */
.progress {
    height: 10px;
    border-radius: 5px;
    margin-bottom: 2.5rem;
    background: linear-gradient(90deg, #e9ecef, #f8f9fa);
    overflow: hidden;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    position: relative;
}

.progress-bar {
    background: linear-gradient(90deg, var(--secondary-color), var(--primary-color));
    border-radius: 5px;
    transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
    background-image: linear-gradient(
        -45deg,
        rgba(255, 255, 255, 0.2) 25%,
        transparent 25%,
        transparent 50%,
        rgba(255, 255, 255, 0.2) 50%,
        rgba(255, 255, 255, 0.2) 75%,
        transparent 75%,
        transparent
    );
    background-size: 1rem 1rem;
    animation: move 1s linear infinite;
}

@keyframes move {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 1rem 0;
    }
}

/* Navigation Pills */
.form-navigation {
    margin-bottom: 2.5rem;
}

.nav-pills-custom {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(44, 127, 184, 0.1);
    position: relative;
    overflow: hidden;
}

.nav-pills-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.75rem;
}

.nav-pill {
    background: linear-gradient(145deg, #ffffff, #f8f9fa);
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 1rem 1.5rem;
    font-weight: 600;
    color: var(--dark-color);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-width: 140px;
    justify-content: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    font-size: 0.9rem;
}

.nav-pill::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s;
}

.nav-pill:hover::before {
    left: 100%;
}

.nav-pill:hover {
    background: linear-gradient(145deg, rgba(44, 127, 184, 0.1), rgba(127, 205, 187, 0.1));
    border-color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(44, 127, 184, 0.2);
}

.nav-pill.active {
    background: var(--gradient-primary);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(44, 127, 184, 0.3);
}

.nav-pill.completed {
    background: linear-gradient(145deg, rgba(40, 167, 69, 0.15), rgba(40, 167, 69, 0.1));
    color: var(--success-color);
    border-color: var(--success-color);
    position: relative;
}

.nav-pill.completed::after {
    content: '✓';
    position: absolute;
    top: -6px;
    right: -6px;
    background: var(--success-color);
    color: white;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
}

.nav-pill i {
    font-size: 1.1rem;
    transition: var(--transition);
}

.nav-pill.active i {
    transform: scale(1.1);
}

/* Form Sections */
.form-section {
    display: none;
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    background: white;
    border-radius: var(--border-radius);
    padding: 2.5rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    margin-bottom: 1rem;
}

.form-section.active {
    display: block;
}

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(30px);
    }
    to { 
        opacity: 1; 
        transform: translateY(0);
    }
}

.section-title {
    color: var(--primary-color);
    border-bottom: 3px solid var(--secondary-color);
    padding-bottom: 1.25rem;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    font-weight: 700;
    position: relative;
    font-size: 1.5rem;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 80px;
    height: 3px;
    background: var(--primary-color);
    border-radius: 2px;
}

.section-title i {
    font-size: 1.75rem;
    padding: 0.75rem;
    background: linear-gradient(145deg, rgba(44, 127, 184, 0.1), rgba(127, 205, 187, 0.1));
    border-radius: 10px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Form Controls */
.form-label {
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
    display: block;
}

.form-label.required::after {
    content: " *";
    color: var(--danger-color);
    font-weight: bold;
}

.form-control, .form-select {
    border-radius: 8px;
    padding: 0.875rem 1rem;
    border: 2px solid #e9ecef;
    transition: var(--transition);
    font-size: 0.95rem;
    background: #fafbfc;
    width: 100%;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(44, 127, 184, 0.15);
    background: white;
    transform: translateY(-1px);
    outline: none;
}

.form-control:hover, .form-select:hover {
    border-color: rgba(44, 127, 184, 0.5);
    background: white;
}

/* Input Groups with Icons */
.input-group-icon {
    position: relative;
}

.input-group-icon .form-control {
    padding-left: 3rem;
}

.input-group-icon i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--primary-color);
    z-index: 5;
    font-size: 1.1rem;
}

/* Buttons */
.btn {
    border-radius: 8px;
    padding: 0.875rem 1.75rem;
    font-weight: 600;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    border: none;
    font-size: 0.95rem;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: var(--gradient-primary);
    color: white;
    box-shadow: 0 4px 15px rgba(44, 127, 184, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(145deg, #1d6fa5, var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(44, 127, 184, 0.4);
    color: white;
}

.btn-success {
    background: var(--gradient-success);
    color: white;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-success:hover {
    background: linear-gradient(145deg, #1e7e34, var(--success-color));
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(40, 167, 69, 0.4);
    color: white;
}

.btn-outline-primary {
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    background: white;
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(44, 127, 184, 0.3);
}

/* Form Footer */
.form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 2.5rem;
    padding-top: 2rem;
    border-top: 2px solid #f1f3f4;
    gap: 1rem;
}

/* Validation States */
.is-valid {
    border-color: var(--success-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.15) !important;
}

.is-invalid {
    border-color: var(--danger-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: var(--danger-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    .card {
        margin: 1rem auto;
    }
    
    .card-header {
        padding: 2rem 1.5rem;
    }
    
    .card-header h2 {
        font-size: 1.75rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .form-section {
        padding: 1.5rem;
    }
    
    .form-footer {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-footer .btn {
        width: 100%;
        justify-content: center;
    }

    .nav-pills-container {
        gap: 0.5rem;
    }
    
    .nav-pill {
        min-width: 100px;
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }
    
    .nav-pill span {
        display: none;
    }
    
    .section-title {
        font-size: 1.25rem;
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .section-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .section-title i {
        width: 50px;
        height: 50px;
        font-size: 1.5rem;
    }
    
    .btn {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form navigation variables
    const sections = document.querySelectorAll('.form-section');
    const navPills = document.querySelectorAll('.nav-pill');
    const progressBar = document.getElementById('form-progress');
    const form = document.getElementById('schemeForm');
    const submitBtn = document.getElementById('submitBtn');
    let currentSection = 0;
    
    // Initialize form sections
    function initializeFormSections() {
        sections.forEach((section, index) => {
            section.style.display = 'none';
        });
        showSection(0);
    }
    
    // Show specific section
    function showSection(index) {
        sections.forEach((section, i) => {
            if (i === index) {
                section.style.display = 'block';
                section.classList.add('active');
            } else {
                section.style.display = 'none';
                section.classList.remove('active');
            }
        });
        updateProgress(index);
        updateNavigation(index);
    }
    
    // Update progress bar
    function updateProgress(index) {
        const progressPercentage = ((index + 1) / sections.length) * 100;
        progressBar.style.width = `${progressPercentage}%`;
        progressBar.setAttribute('aria-valuenow', progressPercentage);
    }
    
    // Update navigation pills
    function updateNavigation(activeIndex) {
        navPills.forEach((pill, index) => {
            pill.classList.remove('active', 'completed');
            if (index === activeIndex) {
                pill.classList.add('active');
            } else if (index < activeIndex) {
                pill.classList.add('completed');
            }
        });
    }
    
    // Navigation function
    function navigateToSection(targetSection) {
        // Validate current section when moving forward
        if (targetSection > currentSection) {
            const currentSectionEl = sections[currentSection];
            const requiredFields = currentSectionEl.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                    
                    // Add error message if not already present
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('invalid-feedback')) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback d-block';
                        errorDiv.textContent = 'This field is required';
                        errorDiv.style.color = '#dc3545';
                        errorDiv.style.fontSize = '0.875em';
                        errorDiv.style.marginTop = '0.25rem';
                        field.parentNode.appendChild(errorDiv);
                    }
                } else {
                    field.classList.remove('is-invalid');
                    // Remove error message if exists
                    const errorDiv = field.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv) {
                        errorDiv.remove();
                    }
                }
            });
            
            if (!isValid) {
                // Focus first invalid field
                const firstInvalid = currentSectionEl.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
        }
        
        currentSection = targetSection;
        showSection(targetSection);
        
        // Update review section if we're going to it
        if (targetSection === 4) {
            updateReviewSection();
        }
        
        return true;
    }
    
    // Navigation pill click handlers
    navPills.forEach((pill, index) => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            navigateToSection(index);
        });
    });
    
    // Next/Previous button handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.next-btn')) {
            const nextSection = parseInt(e.target.closest('.next-btn').dataset.next);
            navigateToSection(nextSection);
        }
        
        if (e.target.closest('.prev-btn')) {
            const prevSection = parseInt(e.target.closest('.prev-btn').dataset.prev);
            navigateToSection(prevSection);
        }
    });
    
    // Dynamic field toggles
    const waterSource = document.getElementById('water_source');
    const otherSourceDiv = document.getElementById('other_source_div');
    
    function toggleOtherSource() {
        if (waterSource.value === 'other') {
            otherSourceDiv.style.display = 'block';
        } else {
            otherSourceDiv.style.display = 'none';
        }
    }
    
    if (waterSource && otherSourceDiv) {
        waterSource.addEventListener('change', toggleOtherSource);
        toggleOtherSource();
    }
    
    // Management committee toggle
    const hasCommittee = document.getElementById('has_management_committee');
    const committeeDetails = document.getElementById('committee_details');
    
    function toggleCommitteeDetails() {
        if (hasCommittee.checked) {
            committeeDetails.style.display = 'block';
        } else {
            committeeDetails.style.display = 'none';
        }
    }
    
    if (hasCommittee && committeeDetails) {
        hasCommittee.addEventListener('change', toggleCommitteeDetails);
        toggleCommitteeDetails();
    }
    
    // Revolving fund toggle
    const hasFund = document.getElementById('has_revolving_fund');
    const fundDetails = document.getElementById('fund_details');
    
    function toggleFundDetails() {
        if (hasFund.checked) {
            fundDetails.style.display = 'block';
        } else {
            fundDetails.style.display = 'none';
        }
    }
    
    if (hasFund && fundDetails) {
        hasFund.addEventListener('change', toggleFundDetails);
        toggleFundDetails();
    }
    
    // Update review section
    function updateReviewSection() {
        // Basic info
        document.getElementById('review_name').textContent = document.getElementById('name')?.value || '-';
        document.getElementById('review_village').textContent = document.getElementById('village')?.value || '-';
        document.getElementById('review_ward').textContent = document.getElementById('ward')?.value || '-';
        
        // Water source display
        const waterSourceValue = document.getElementById('water_source')?.value;
        const otherSourceValue = document.getElementById('water_source_other')?.value;
        if (waterSourceValue === 'other' && otherSourceValue) {
            document.getElementById('review_water_source').textContent = otherSourceValue;
        } else {
            document.getElementById('review_water_source').textContent = waterSourceValue || '-';
        }
        
        // Technical details
        document.getElementById('review_pump_type').textContent = document.getElementById('pump_type')?.value || '-';
        document.getElementById('review_power_type').textContent = document.getElementById('power_type')?.value || '-';
        document.getElementById('review_network_length').textContent = document.getElementById('network_length')?.value ? 
            document.getElementById('network_length').value + ' m' : '-';
        document.getElementById('review_functionality').textContent = document.getElementById('functionality')?.value || '-';
        
        // Service coverage
        document.getElementById('review_population_served').textContent = document.getElementById('population_served')?.value || '-';
        document.getElementById('review_households_served').textContent = document.getElementById('households_served')?.value || '-';
        document.getElementById('review_stand_pipes').textContent = document.getElementById('stand_pipes_count')?.value || '-';
        document.getElementById('review_storage_tanks').textContent = document.getElementById('storage_tanks')?.value ? 
            document.getElementById('storage_tanks').value + ' m³' : '-';
        
        // Management
        const hasCommittee = document.getElementById('has_management_committee')?.checked ? 'Yes' : 'No';
        const hasFund = document.getElementById('has_revolving_fund')?.checked ? 'Yes' : 'No';
        document.getElementById('review_has_committee').textContent = hasCommittee;
        document.getElementById('review_has_fund').textContent = hasFund;
        document.getElementById('review_established_by').textContent = document.getElementById('established_by')?.value || '-';
        document.getElementById('review_year_established').textContent = document.getElementById('year_established')?.value || '-';
    }
    
    // Coverage calculations
    const householdsServedInput = document.getElementById('households_served');
    const populationServedInput = document.getElementById('population_served');
    const womenServedInput = document.getElementById('women_served');
    const menServedInput = document.getElementById('men_served');
    
    const householdCoverageDisplay = document.getElementById('household_coverage_display');
    const populationCoverageDisplay = document.getElementById('population_coverage_display');
    const genderBalanceDisplay = document.getElementById('gender_balance_display');
    
    function updateCoverageCalculations() {
        const householdsServed = parseFloat(householdsServedInput?.value) || 0;
        const populationServed = parseFloat(populationServedInput?.value) || 0;
        const womenServed = parseFloat(womenServedInput?.value) || 0;
        const menServed = parseFloat(menServedInput?.value) || 0;
        
        // These would ideally come from database or configuration
        const totalHouseholdsInArea = 1000; // Example value
        const totalPopulationInArea = 5000; // Example value
        
        let householdCoverage = 0;
        let populationCoverage = 0;
        let genderBalance = 0;
        
        if (totalHouseholdsInArea > 0) {
            householdCoverage = (householdsServed / totalHouseholdsInArea) * 100;
        }
        
        if (totalPopulationInArea > 0) {
            populationCoverage = (populationServed / totalPopulationInArea) * 100;
        }
        
        const totalAdults = womenServed + menServed;
        if (totalAdults > 0) {
            genderBalance = (womenServed / totalAdults) * 100;
        }
        
        // Update displays
        if (householdCoverageDisplay) {
            householdCoverageDisplay.textContent = householdCoverage.toFixed(1) + '%';
            householdCoverageDisplay.className = 'form-control text-center ' + 
                (householdCoverage >= 50 ? 'text-success' : householdCoverage >= 25 ? 'text-warning' : 'text-danger');
        }
        
        if (populationCoverageDisplay) {
            populationCoverageDisplay.textContent = populationCoverage.toFixed(1) + '%';
            populationCoverageDisplay.className = 'form-control text-center ' + 
                (populationCoverage >= 50 ? 'text-success' : populationCoverage >= 25 ? 'text-warning' : 'text-danger');
        }
        
        if (genderBalanceDisplay) {
            genderBalanceDisplay.textContent = genderBalance.toFixed(1) + '% Women';
            genderBalanceDisplay.className = 'form-control text-center ' + 
                (genderBalance >= 40 ? 'text-success' : genderBalance >= 30 ? 'text-warning' : 'text-danger');
        }
    }
    
    // Add event listeners for coverage calculations
    if (householdsServedInput) householdsServedInput.addEventListener('input', updateCoverageCalculations);
    if (populationServedInput) populationServedInput.addEventListener('input', updateCoverageCalculations);
    if (womenServedInput) womenServedInput.addEventListener('input', updateCoverageCalculations);
    if (menServedInput) menServedInput.addEventListener('input', updateCoverageCalculations);
    
    // Initialize calculations
    updateCoverageCalculations();
    
    // Clear validation on input
    document.querySelectorAll('[required]').forEach(field => {
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                const errorDiv = this.parentNode.querySelector('.invalid-feedback');
                if (errorDiv) {
                    errorDiv.remove();
                }
            }
        });
    });
    
    // Form submission - Show loading state
    form?.addEventListener('submit', function() {
        if (submitBtn) {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
            submitBtn.disabled = true;
        }
    });
    
    // Initialize form
    initializeFormSections();
});
</script>
@endsection