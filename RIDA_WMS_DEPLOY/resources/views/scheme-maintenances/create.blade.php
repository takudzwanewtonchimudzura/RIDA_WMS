@extends('layouts.app')

@section('title', 'Add Maintenance Record')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('scheme-maintenances.index') }}">Scheme Maintenance</a></li>
    <li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Add Scheme Maintenance Record</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('scheme-maintenances.store') }}" method="POST">
                    @csrf
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-info-circle me-2"></i>Scheme Information</h5>
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
                            <label for="type_of_water_source" class="form-label">Water Source Type</label>
                            <select class="form-select @error('type_of_water_source') is-invalid @enderror" 
                                    id="type_of_water_source" name="type_of_water_source">
                                <option value="">Select Source</option>
                                <option value="Borehole" {{ old('type_of_water_source') == 'Borehole' ? 'selected' : '' }}>Borehole</option>
                                <option value="Deep Well" {{ old('type_of_water_source') == 'Deep Well' ? 'selected' : '' }}>Deep Well</option>
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
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-history me-2"></i>Scheme History</h5>
                            <hr>
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
                                <option value="Hand Pump" {{ old('type_of_pump') == 'Hand Pump' ? 'selected' : '' }}>Hand Pump</option>
                                <option value="Solar Pump" {{ old('type_of_pump') == 'Solar Pump' ? 'selected' : '' }}>Solar Pump</option>
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
                            <label for="total_network_in_metres" class="form-label">Network Length (m)</label>
                            <input type="number" class="form-control @error('total_network_in_metres') is-invalid @enderror" 
                                   id="total_network_in_metres" name="total_network_in_metres" 
                                   value="{{ old('total_network_in_metres') }}" min="0">
                            @error('total_network_in_metres')
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
                        
                        <div class="col-md-4 mb-3">
                            <label for="number_of_stand_pipes" class="form-label">Stand Pipes</label>
                            <input type="number" class="form-control @error('number_of_stand_pipes') is-invalid @enderror" 
                                   id="number_of_stand_pipes" name="number_of_stand_pipes" 
                                   value="{{ old('number_of_stand_pipes') }}" min="0">
                            @error('number_of_stand_pipes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-tools me-2"></i>Maintenance Details</h5>
                            <hr>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="repaired_by" class="form-label">Repaired By *</label>
                            <input type="text" class="form-control @error('repaired_by') is-invalid @enderror" 
                                   id="repaired_by" name="repaired_by" 
                                   value="{{ old('repaired_by') }}" required>
                            @error('repaired_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date_repaired" class="form-label">Date Repaired *</label>
                            <input type="date" class="form-control @error('date_repaired') is-invalid @enderror" 
                                   id="date_repaired" name="date_repaired" 
                                   value="{{ old('date_repaired') }}" required>
                            @error('date_repaired')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="date_last_repaired" class="form-label">Date Last Repaired</label>
                            <input type="date" class="form-control @error('date_last_repaired') is-invalid @enderror" 
                                   id="date_last_repaired" name="date_last_repaired" 
                                   value="{{ old('date_last_repaired') }}">
                            @error('date_last_repaired')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="works_done" class="form-label">Works Done *</label>
                            <textarea class="form-control @error('works_done') is-invalid @enderror" 
                                      id="works_done" name="works_done" 
                                      rows="3" required>{{ old('works_done') }}</textarea>
                            @error('works_done')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="spares_fitted" class="form-label">Spares Fitted</label>
                            <textarea class="form-control @error('spares_fitted') is-invalid @enderror" 
                                      id="spares_fitted" name="spares_fitted" 
                                      rows="3">{{ old('spares_fitted') }}</textarea>
                            @error('spares_fitted')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="spares_received_by" class="form-label">Spares Received By (Community Member)</label>
                            <input type="text" class="form-control @error('spares_received_by') is-invalid @enderror" 
                                   id="spares_received_by" name="spares_received_by" 
                                   value="{{ old('spares_received_by') }}">
                            @error('spares_received_by')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contact_detail" class="form-label">Contact Details</label>
                            <input type="text" class="form-control @error('contact_detail') is-invalid @enderror" 
                                   id="contact_detail" name="contact_detail" 
                                   value="{{ old('contact_detail') }}" placeholder="Phone number or email">
                            @error('contact_detail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5><i class="fas fa-comment me-2"></i>Remarks</h5>
                            <hr>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="remarks" class="form-label">Additional Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                      id="remarks" name="remarks" 
                                      rows="4">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('scheme-maintenances.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Maintenance Record
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection