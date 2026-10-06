@extends('layouts.app')

@section('title', 'Add New Irrigation Scheme')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('irrigation-schemes.index') }}">Irrigation Schemes</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Irrigation Scheme</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('irrigation-schemes.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                            <hr>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="name_of_scheme" class="form-label">Scheme Name *</label>
                            <input type="text" class="form-control @error('name_of_scheme') is-invalid @enderror" 
                                   id="name_of_scheme" name="name_of_scheme" 
                                   value="{{ old('name_of_scheme') }}" required>
                            @error('name_of_scheme')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="village" class="form-label">Village *</label>
                            <input type="text" class="form-control @error('village') is-invalid @enderror" 
                                   id="village" name="village" 
                                   value="{{ old('village') }}" required>
                            @error('village')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ward" class="form-label">Ward *</label>
                            <input type="text" class="form-control @error('ward') is-invalid @enderror" 
                                   id="ward" name="ward" value="{{ old('ward') }}" required>
                            @error('ward')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="irrigation_type" class="form-label">Irrigation Type *</label>
                            <select class="form-select @error('irrigation_type') is-invalid @enderror" 
                                    id="irrigation_type" name="irrigation_type" required>
                                <option value="">Select Type</option>
                                <option value="Sprinkler" {{ old('irrigation_type') == 'Sprinkler' ? 'selected' : '' }}>Sprinkler</option>
                                <option value="Canal" {{ old('irrigation_type') == 'Canal' ? 'selected' : '' }}>Canal</option>
                                <option value="Drip" {{ old('irrigation_type') == 'Drip' ? 'selected' : '' }}>Drip</option>
                                <option value="Draghose" {{ old('irrigation_type') == 'Draghose' ? 'selected' : '' }}>Draghose</option>
                                <option value="Center Pivot" {{ old('irrigation_type') == 'Center Pivot' ? 'selected' : '' }}>Center Pivot</option>
                                <option value="Other" {{ old('irrigation_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('irrigation_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-cogs me-2"></i>Technical Details</h5>
                            <hr>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="type_of_water_source" class="form-label">Water Source *</label>
                            <select class="form-select @error('type_of_water_source') is-invalid @enderror" 
                                    id="type_of_water_source" name="type_of_water_source" required>
                                <option value="">Select Source</option>
                                <option value="Borehole" {{ old('type_of_water_source') == 'Borehole' ? 'selected' : '' }}>Borehole</option>
                                <option value="Spring" {{ old('type_of_water_source') == 'Spring' ? 'selected' : '' }}>Spring</option>
                                <option value="River" {{ old('type_of_water_source') == 'River' ? 'selected' : '' }}>River</option>
                                <option value="Dam" {{ old('type_of_water_source') == 'Dam' ? 'selected' : '' }}>Dam</option>
                                <option value="Sand Abstraction" {{ old('type_of_water_source') == 'Sand Abstraction' ? 'selected' : '' }}>Sand Abstraction</option>
                                <option value="Other" {{ old('type_of_water_source') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type_of_water_source')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="year_established" class="form-label">Year Established</label>
                            <input type="number" class="form-control @error('year_established') is-invalid @enderror" 
                                   id="year_established" name="year_established" 
                                   value="{{ old('year_established') }}" min="1900" max="{{ date('Y') }}">
                            @error('year_established')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="type_of_pump" class="form-label">Type of Pump</label>
                            <select class="form-select @error('type_of_pump') is-invalid @enderror" 
                                    id="type_of_pump" name="type_of_pump">
                                <option value="">Select Pump</option>
                                <option value="Centrifugal" {{ old('type_of_pump') == 'Centrifugal' ? 'selected' : '' }}>Centrifugal</option>
                                <option value="Submersible" {{ old('type_of_pump') == 'Submersible' ? 'selected' : '' }}>Submersible</option>
                                <option value="Solar Pump" {{ old('type_of_pump') == 'Solar Pump' ? 'selected' : '' }}>Solar Pump</option>
                                <option value="Diesel Pump" {{ old('type_of_pump') == 'Diesel Pump' ? 'selected' : '' }}>Diesel Pump</option>
                                <option value="Other" {{ old('type_of_pump') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type_of_pump')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="type_of_power" class="form-label">Power Source</label>
                            <select class="form-select @error('type_of_power') is-invalid @enderror" 
                                    id="type_of_power" name="type_of_power">
                                <option value="">Select Power</option>
                                <option value="Solar" {{ old('type_of_power') == 'Solar' ? 'selected' : '' }}>Solar</option>
                                <option value="Electricity" {{ old('type_of_power') == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                                <option value="Fossil Fuel" {{ old('type_of_power') == 'Fossil Fuel' ? 'selected' : '' }}>Fossil Fuel</option>
                                <option value="Wind" {{ old('type_of_power') == 'Wind' ? 'selected' : '' }}>Wind</option>
                                <option value="Gravity" {{ old('type_of_power') == 'Gravity' ? 'selected' : '' }}>Gravity</option>
                            </select>
                            @error('type_of_power')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="size_in_hectares" class="form-label">Size (Hectares)</label>
                            <input type="number" class="form-control @error('size_in_hectares') is-invalid @enderror" 
                                   id="size_in_hectares" name="size_in_hectares" 
                                   value="{{ old('size_in_hectares') }}" min="0" step="0.01">
                            @error('size_in_hectares')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="total_storage_tanks_in_m3" class="form-label">Storage Tanks (m³)</label>
                            <input type="number" class="form-control @error('total_storage_tanks_in_m3') is-invalid @enderror" 
                                   id="total_storage_tanks_in_m3" name="total_storage_tanks_in_m3" 
                                   value="{{ old('total_storage_tanks_in_m3') }}" min="0">
                            @error('total_storage_tanks_in_m3')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-seedling me-2"></i>Crop Information</h5>
                            <hr>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="crop_pattern" class="form-label">Crop Pattern</label>
                            <input type="text" class="form-control @error('crop_pattern') is-invalid @enderror" 
                                   id="crop_pattern" name="crop_pattern" 
                                   value="{{ old('crop_pattern') }}" placeholder="e.g., Wheat, Maize, Vegetables">
                            @error('crop_pattern')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="plot_size" class="form-label">Plot Size (per farmer)</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('plot_size') is-invalid @enderror" 
                                   id="plot_size" name="plot_size" 
                                value="{{ old('plot_size') }}" placeholder="e.g., 0.5">
                            @error('plot_size')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-users me-2"></i>Beneficiary Information</h5>
                            <hr>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="number_of_beneficiaries_in_hh" class="form-label">Households Benefiting</label>
                            <input type="number" class="form-control @error('number_of_beneficiaries_in_hh') is-invalid @enderror" 
                                   id="number_of_beneficiaries_in_hh" name="number_of_beneficiaries_in_hh" 
                                   value="{{ old('number_of_beneficiaries_in_hh', 0) }}" min="0">
                            @error('number_of_beneficiaries_in_hh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="functionality_of_the_scheme" class="form-label">Scheme Status *</label>
                            <select class="form-select @error('functionality_of_the_scheme') is-invalid @enderror" 
                                    id="functionality_of_the_scheme" name="functionality_of_the_scheme" required>
                                <option value="">Select Status</option>
                                <option value="Functional" {{ old('functionality_of_the_scheme') == 'Functional' ? 'selected' : '' }}>Functional</option>
                                <option value="Partially Functional" {{ old('functionality_of_the_scheme') == 'Partially Functional' ? 'selected' : '' }}>Partially Functional</option>
                                <option value="Nonfunctional" {{ old('functionality_of_the_scheme') == 'Nonfunctional' ? 'selected' : '' }}>Nonfunctional</option>
                            </select>
                            @error('functionality_of_the_scheme')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-user-friends me-2"></i>Management Committee</h5>
                            <hr>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="management_committee_available" 
                                       name="management_committee_available" 
                                       value="1" {{ old('management_committee_available') ? 'checked' : '' }}>
                                <label class="form-check-label" for="management_committee_available">
                                    Committee Available
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_trained" 
                                       name="is_trained" 
                                       value="1" {{ old('is_trained') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_trained">
                                    Committee Trained
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_functional" 
                                       name="is_functional" 
                                       value="1" {{ old('is_functional') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_functional">
                                    Committee Functional
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="revolving_fund" 
                                       name="revolving_fund" 
                                       value="1" {{ old('revolving_fund') ? 'checked' : '' }}>
                                <label class="form-check-label" for="revolving_fund">
                                    Has Revolving Fund
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="revolving_fund_amount" class="form-label">Revolving Fund Amount</label>
                            <input type="number" class="form-control @error('revolving_fund_amount') is-invalid @enderror" 
                                   id="revolving_fund_amount" name="revolving_fund_amount" 
                                   value="{{ old('revolving_fund_amount', 0) }}" min="0" step="0.01">
                            @error('revolving_fund_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="total_number_of_members" class="form-label">Total Committee Members</label>
                            <input type="number" class="form-control @error('total_number_of_members') is-invalid @enderror" 
                                   id="total_number_of_members" name="total_number_of_members" 
                                   value="{{ old('total_number_of_members', 0) }}" min="0">
                            @error('total_number_of_members')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="women_members" class="form-label">Women Committee Members</label>
                            <input type="number" class="form-control @error('women_members') is-invalid @enderror" 
                                   id="women_members" name="women_members" 
                                   value="{{ old('women_members', 0) }}" min="0">
                            @error('women_members')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('irrigation-schemes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Irrigation Scheme
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection