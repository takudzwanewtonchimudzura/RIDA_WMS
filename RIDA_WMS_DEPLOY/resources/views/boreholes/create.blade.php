@extends('layouts.app')

@section('title', 'Create Borehole')

@section('scripts')
    @include('boreholes.partials.pump-type-script')
@endsection

@section('content')
<div class="container my-5">
    <div class="card">
        <div class="card-header text-center">
            <h2 class="mb-0"><i class="fas fa-water me-2"></i>Create New Borehole Record</h2>
            <p class="mb-0 mt-2">Complete all sections to create a comprehensive borehole record</p>
        </div>
        <div class="card-body">
            <!-- Progress Bar -->
            <div class="progress mb-4">
                <div class="progress-bar" id="form-progress" role="progressbar" style="width: 12.5%;" aria-valuenow="12.5" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            
            <!-- Navigation Pills -->
            <div class="form-navigation">
                <div class="nav-pills-custom">
                    <div class="nav-pills-container">
                        <button class="nav-pill active" data-section="0">
                            <i class="fas fa-info-circle"></i> 
                            <span>General</span>
                        </button>
                        <button class="nav-pill" data-section="1">
                            <i class="fas fa-tools"></i> 
                            <span>Drilling</span>
                        </button>
                        <button class="nav-pill" data-section="2">
                            <i class="fas fa-mountain"></i> 
                            <span>Geology</span>
                        </button>
                        <button class="nav-pill" data-section="3">
                            <i class="fas fa-layer-group"></i> 
                            <span>Casing</span>
                        </button>
                        <button class="nav-pill" data-section="4">
                            <i class="fas fa-tint"></i> 
                            <span>Water</span>
                        </button>
                        <button class="nav-pill" data-section="5">
                            <i class="fas fa-pump-soap"></i> 
                            <span>Pump</span>
                        </button>
                        <button class="nav-pill" data-section="6">
                            <i class="fas fa-gas-pump"></i> 
                            <span>Consumables</span>
                        </button>
                        <button class="nav-pill" data-section="7">
                            <i class="fas fa-map-marker-alt"></i> 
                            <span>Location</span>
                        </button>
                        <button class="nav-pill" data-section="8">
                            <i class="fas fa-project-diagram"></i> 
                            <span>Project</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Form Sections -->
            <form method="POST" action="{{ route('boreholes.store') }}" id="boreholeForm" enctype="multipart/form-data">
                @csrf
                @if($prospectingSite)
                    <input type="hidden" name="prospecting_site_id" value="{{ $prospectingSite->id }}">
                    <div class="alert alert-info" role="status">Starting from approved prospecting {{ $prospectingSite->reference }}.</div>
                @endif
                
                <!-- General Information -->
                <div class="form-section active" id="section-0">
                    <h4 class="section-title"><i class="fas fa-info-circle text-primary"></i> General Information</h4>
                    
                    <div class="row">
                        <!-- Left Column - Basic Information -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="borehole_serial_no" class="form-label required">Borehole Serial No</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-hashtag"></i>
                                    <input type="text" name="borehole_serial_no" id="borehole_serial_no" required
                                        value="{{ old('borehole_serial_no') }}" class="form-control" placeholder="Enter serial number">
                                </div>
                                @error('borehole_serial_no')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label required">Date</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="date" name="date" id="date" required
                                        value="{{ old('date', date('Y-m-d')) }}" class="form-control">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="province" class="form-label required">Province</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-map"></i>
                                    <input type="text" name="province" id="province" required
                                        value="{{ old('province', $prospectingSite?->province ?? '') }}" class="form-control" placeholder="Enter province">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="admin_district" class="form-label required">Admin District</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-map-marked-alt"></i>
                                    <input type="text" name="admin_district" id="admin_district" required
                                        value="{{ old('admin_district', $prospectingSite?->district ?? '') }}" class="form-control" placeholder="Enter admin district">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="locality" class="form-label required">Locality</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-location-arrow"></i>
                                    <input type="text" name="locality" id="locality" required
                                        value="{{ old('locality', $prospectingSite?->village ?? '') }}" class="form-control" placeholder="Enter locality">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="ward" class="form-label required">Ward</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-map-pin"></i>
                                    <input type="text" name="ward" id="ward" required
                                        value="{{ old('ward', $prospectingSite?->ward ?? '') }}" class="form-control" placeholder="Enter ward">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="village" class="form-label">Village</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-home"></i>
                                    <input type="text" name="village" id="village"
                                        value="{{ old('village', $prospectingSite?->village ?? '') }}" class="form-control" placeholder="Enter village name">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="usage" class="form-label">Usage</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-tasks"></i>
                                    <input type="text" name="usage" id="usage"
                                        value="{{ old('usage') }}" class="form-control" placeholder="Enter usage (optional)">
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Additional Information -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dth_bit_serial_no" class="form-label">DTH Bit Serial No</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-cog"></i>
                                    <input type="text" name="dth_bit_serial_no" id="dth_bit_serial_no"
                                        value="{{ old('dth_bit_serial_no') }}" class="form-control" placeholder="Enter DTH bit serial no (optional)">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="hammer_serial_no" class="form-label">Hammer Serial No</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-hammer"></i>
                                    <input type="text" name="hammer_serial_no" id="hammer_serial_no"
                                        value="{{ old('hammer_serial_no') }}" class="form-control" placeholder="Enter hammer serial no (optional)">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="rig_number" class="form-label">RIG NUMBER</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-hashtag"></i>
                                    <input type="text" name="rig_number" id="rig_number"
                                        value="{{ old('rig_number') }}" class="form-control" placeholder="Enter rig number">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="rig_type" class="form-label">RIG TYPE</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-cogs"></i>
                                    <input type="text" name="rig_type" id="rig_type"
                                        value="{{ old('rig_type') }}" class="form-control" placeholder="Enter rig type">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="rdd_depth" class="form-label">Drilled Depth (m)</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-ruler-vertical"></i>
                                    <input type="number" step="0.01" name="rdd_depth" id="rdd_depth"
                                        value="{{ old('rdd_depth') }}" class="form-control" placeholder="Enter RDD depth in meters">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="rig_chasis_number" class="form-label">RIG CHASIS NUMBER</label>
                                <div class="input-group-icon">
                                    <i class="fas fa-truck"></i>
                                    <input type="text" name="rig_chasis_number" id="rig_chasis_number"
                                        value="{{ old('rig_chasis_number') }}" class="form-control" placeholder="Enter rig chasis number">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="siting_agency" class="form-label">Siting Agency</label>
                                <select name="siting_agency" id="siting_agency" class="form-control">
                                    <option value="">Select Agency</option>
                                    <option value="Government" {{ old('siting_agency') == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="PISP" {{ old('siting_agency') == 'PISP' ? 'selected' : '' }}>PISP</option>
                                </select>
                            </div>

                            <div class="mb-3" id="pisp_specification_field" style="display: none;">
                                <label for="pisp_specification" class="form-label">PISP Specification</label>
                                <input type="text" name="pisp_specification" id="pisp_specification"
                                    value="{{ old('pisp_specification') }}" class="form-control" placeholder="Specify PISP">
                            </div>

                            <div class="mb-3">
                                <label for="drilling_date_from" class="form-label">Drilling Dates - FROM</label>
                                <input type="date" name="drilling_date_from" id="drilling_date_from"
                                    value="{{ old('drilling_date_from') }}" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="drilling_date_to" class="form-label">Drilling Dates - TO</label>
                                <input type="date" name="drilling_date_to" id="drilling_date_to"
                                    value="{{ old('drilling_date_to') }}" class="form-control">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <div></div>
                        <button type="button" class="btn btn-primary next-btn" data-next="1">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Drilling Details -->
                <div class="form-section" id="section-1">
                    <h4 class="section-title"><i class="fas fa-tools text-primary"></i> Drilling Details</h4>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label" for="bit-size-table">Bit sizes and depth intervals</label>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="bit-size-table">
                                    <thead><tr><th>Bit size (mm)</th><th>From depth (m)</th><th>To depth (m)</th><th></th></tr></thead>
                                    <tbody id="bit-size-intervals-container">
                                        <tr class="bit-size-interval">
                                            <td><input type="number" min="1" step="1" name="bit_size_intervals[0][bit_size_mm]" class="form-control" placeholder="Bit size"></td>
                                            <td><input type="number" min="0" step="0.01" name="bit_size_intervals[0][depth_from_m]" class="form-control" placeholder="From"></td>
                                            <td><input type="number" min="0" step="0.01" name="bit_size_intervals[0][depth_to_m]" class="form-control" placeholder="To"></td>
                                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-bit-interval" aria-label="Remove bit interval" style="display:none"><i class="fas fa-times" aria-hidden="true"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="add-bit-size-interval" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus me-1" aria-hidden="true"></i>Add interval</button>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="final_borehole_depth_m" class="form-label">Final Depth (m)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-ruler-vertical"></i>
                                <input type="number" step="0.01" name="final_borehole_depth_m" id="final_borehole_depth_m"
                                    value="{{ old('final_borehole_depth_m') }}" class="form-control" placeholder="Enter depth in meters">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="blowing_yield" class="form-label">Blowing Yield (l/s)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-tint"></i>
                                <input type="number" step="0.01" name="blowing_yield" id="blowing_yield"
                                    value="{{ old('blowing_yield') }}" class="form-control" placeholder="Enter blowing yield in liters per second">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="development_hours" class="form-label">Development Hours</label>
                            <div class="input-group-icon">
                                <i class="fas fa-clock"></i>
                                <input type="number" name="development_hours" id="development_hours"
                                    value="{{ old('development_hours') }}" class="form-control" placeholder="Enter hours">
                            </div>
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
                
                <!-- GEOLOGY INFORMATION -->
                <div class="form-section" id="section-2">
                    <h4 class="section-title"><i class="fas fa-mountain text-primary"></i> GEOLOGY INFORMATION</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="geology-table">
                            <thead class="table-primary">
                                <tr>
                                    <th>GEOLOGY SAMPLE</th>
                                    <th colspan="2">DEPTH RANGE</th>
                                    <th colspan="2">PENETRATION RATES</th>
                                    <th>WATER STRIKE</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>FROM (m)</th>
                                    <th>TO (m)</th>
                                    <th>TO (m)</th>
                                    <th>RATE (min/rod)</th>
                                    <th>at ______ m</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="geology-layers-container">
                                <tr class="geology-layer">
                                    <td>
                                        <input type="text" name="geology_layers[0][sample_description]" class="form-control" placeholder="e.g. hard rock, sand">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="geology_layers[0][description_layer_depth_from]" 
                                            class="form-control" placeholder="From" value="0">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="geology_layers[0][description_layer_depth_to]" 
                                            class="form-control" placeholder="To">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="geology_layers[0][penetration_rates_to]" 
                                            class="form-control" placeholder="To">
                                    </td>
                                    <td>
                                        <input type="text" name="geology_layers[0][penetration_rates_rate]" 
                                            class="form-control" placeholder="Rate">
                                    </td>
                                    <td>
                                        <input type="text" name="geology_layers[0][water_strike]" 
                                            class="form-control" placeholder="at ______ m">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-geology" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button type="button" id="add-geology-layer" class="btn btn-add-field">
                            <i class="fas fa-plus me-1"></i> Add Another Geology Row
                        </button>
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

                <!-- CASING DETAILS -->
                <div class="form-section" id="section-3">
                    <h4 class="section-title"><i class="fas fa-layer-group text-primary"></i> CASING DETAILS</h4>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="casing-table">
                            <thead class="table-primary">
                                <tr>
                                    <th>DESCRIPTION</th>
                                    <th>DIAMETER (mm)</th>
                                    <th>CASING TYPE</th>
                                    <th>CLASS</th>
                                    <th colspan="2">DEPTHS</th>
                                    <th>GRAVEL PACKED</th>
                                    <th>CEMENT SEALED</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>FROM (m)</th>
                                    <th>TO (m)</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="casing-details-container">
                                <tr class="casing-detail">
                                    <td>
                                        <select name="casing_details[0][description]" class="form-control">
                                            <option value="Plain">Plain</option>
                                            <option value="Slotted">Slotted</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="casing_details[0][diameter]" 
                                            class="form-control" placeholder="Diameter mm">
                                        <input type="hidden" name="casing_details[0][position]" value="1">
                                    </td>
                                    <td>
                                        <select name="casing_details[0][casing_type]" class="form-control">
                                            <option value="">Select</option>
                                            <option value="PVC">PVC</option>
                                            <option value="Steel">Steel</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="casing_details[0][casing_class]" class="form-control" placeholder="e.g. 9/10"></td>
                                    <td>
                                        <input type="number" step="0.01" name="casing_details[0][depth_from]" 
                                            class="form-control" placeholder="From" value="0">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="casing_details[0][depth_to]" 
                                            class="form-control" placeholder="To">
                                    </td>
                                    <td class="text-center"><input type="checkbox" name="casing_details[0][gravel_packed]" value="1" aria-label="Gravel packed"></td>
                                    <td class="text-center"><input type="checkbox" name="casing_details[0][cement_sealed]" value="1" aria-label="Cement sealed"></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-casing" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Additional Casing Fields -->
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <label for="final_cased_depth" class="form-label">Final Cased Depth (m)</label>
                            <input type="number" step="0.01" name="final_cased_depth" id="final_cased_depth"
                                value="{{ old('final_cased_depth') }}" class="form-control" placeholder="Enter final cased depth">
                        </div>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button type="button" id="add-casing-detail" class="btn btn-add-field">
                            <i class="fas fa-plus me-1"></i> Add Another Casing Row
                        </button>
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

                <!-- Water Quality -->
                <div class="form-section" id="section-4">
                    <h4 class="section-title"><i class="fas fa-tint text-primary"></i> Water Quality</h4>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tds_mg_l" class="form-label">TDS (mg/l)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-flask"></i>
                                <input type="number" name="tds_mg_l" id="tds_mg_l"
                                    value="{{ old('tds_mg_l') }}" class="form-control" placeholder="Total dissolved solids">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ph" class="form-label">pH</label>
                            <div class="input-group-icon">
                                <i class="fas fa-vial"></i>
                                <input type="number" name="ph" id="ph" step="0.1" min="0" max="14"
                                    value="{{ old('ph') }}" class="form-control" placeholder="pH value (0-14)">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="turbidity_ntu" class="form-label">Turbidity (NTU)</label>
                            <input type="number" step="0.01" name="turbidity_ntu" id="turbidity_ntu"
                                value="{{ old('turbidity_ntu') }}" class="form-control" placeholder="Enter turbidity">
                            </div>

                        <div class="col-md-6 mb-3">
                            <label for="electrical_conductivity" class="form-label">Electrical Conductivity</label>
                            <input type="number" step="0.01" name="electrical_conductivity" id="electrical_conductivity"
                                value="{{ old('electrical_conductivity') }}" class="form-control" placeholder="Enter conductivity">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="water_color" class="form-label">Water Color</label>
                            <input type="text" name="water_color" id="water_color"
                                value="{{ old('water_color') }}" class="form-control" placeholder="Enter water color">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="water_appearance" class="form-label">Water Appearance</label>
                            <input type="text" name="water_appearance" id="water_appearance"
                                value="{{ old('water_appearance') }}" class="form-control" placeholder="Describe water appearance">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="odor" class="form-label">Odor</label>
                            <input type="text" name="odor" id="odor"
                                value="{{ old('odor') }}" class="form-control" placeholder="Enter odor">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="taste" class="form-label">Taste</label>
                            <input type="text" name="taste" id="taste"
                                value="{{ old('taste') }}" class="form-control" placeholder="Enter taste">
                        </div>

                        <div class="col-12 mb-3">
                            <label for="water_quality_remarks" class="form-label">Water Quality Remarks</label>
                            <textarea name="water_quality_remarks" id="water_quality_remarks" class="form-control" rows="3" 
                                placeholder="Enter water quality remarks">{{ old('water_quality_remarks') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <button type="button" class="btn btn-outline-primary prev-btn" data-prev="3">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-btn" data-next="5">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- PUMP INSTALLATION -->
                <div class="form-section" id="section-5">
                    <h4 class="section-title"><i class="fas fa-pump-soap text-primary"></i> PUMP INSTALLATION</h4>
                    
                    <!-- Pump Technical Details -->
                    <h5 class="mt-4 mb-3"><i class="fas fa-cogs text-secondary"></i> Technical Details</h5>
                    <div class="row" data-pump-technical>
                        <div class="col-md-6 mb-3">
                            <label for="pump_type" class="form-label">Type of Pump</label>
                            <div class="input-group-icon">
                                <i class="fas fa-pump-soap"></i>
                                <input type="text" name="pump_type" id="pump_type"
                                    value="{{ old('pump_type') }}" class="form-control" list="pump-types" placeholder="Enter pump type">
                            </div>
                            <datalist id="pump-types"><option value="Handpump"><option value="Submersible"><option value="Solar"><option value="Electric"><option value="Diesel"><option value="Other"></datalist>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pump_model" class="form-label">Pump Model</label>
                            <input type="text" name="pump_model" id="pump_model"
                                value="{{ old('pump_model') }}" class="form-control" placeholder="Enter pump model">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pipe_length_m" class="form-label">Length of pipes and rods (m)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-ruler"></i>
                                <input type="number" step="0.01" name="pipe_length_m" id="pipe_length_m"
                                    value="{{ old('pipe_length_m') }}" class="form-control" placeholder="Enter length in meters">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3" data-pump-field="handpump">
                            <label for="cylinder_size_m" class="form-label">Size of cylinder (m)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-expand"></i>
                                <input type="number" step="0.01" name="cylinder_size_m" id="cylinder_size_m"
                                    value="{{ old('cylinder_size_m') }}" class="form-control" placeholder="Enter size in meters">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="swl_m" class="form-label">Static Water Level - SWL (m)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-tachometer-alt"></i>
                                <input type="number" step="0.01" name="swl_m" id="swl_m"
                                    value="{{ old('swl_m') }}" class="form-control" placeholder="Enter water level in meters">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="pump_depth_m" class="form-label">Pump Depth (m)</label>
                            <input type="number" step="0.01" name="pump_depth_m" id="pump_depth_m"
                                    value="{{ old('pump_depth_m') }}" class="form-control" placeholder="Enter pump depth in meters">
                        </div>
                    </div>
                    
                    <!-- Installation Dates -->
                    <h5 class="mt-4 mb-3"><i class="fas fa-calendar-alt text-secondary"></i> Installation Dates</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pump_installation_date" class="form-label">Date of Installation</label>
                            <div class="input-group-icon">
                                <i class="fas fa-calendar"></i>
                                <input type="date" name="pump_installation_date" id="pump_installation_date"
                                    value="{{ old('pump_installation_date') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="record_date" class="form-label">Record Date</label>
                            <div class="input-group-icon">
                                <i class="fas fa-calendar-check"></i>
                                <input type="date" name="record_date" id="record_date"
                                    value="{{ old('record_date') }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="installed_by_name" class="form-label">Installed By</label>
                            <input type="text" name="installed_by_name" id="installed_by_name" value="{{ old('installed_by_name') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="installed_by_organisation" class="form-label">Installer Organisation</label>
                            <input type="text" name="installed_by_organisation" id="installed_by_organisation" value="{{ old('installed_by_organisation') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pump_installation_status" class="form-label">Pump installation outcome</label>
                            <select name="pump_installation_status" id="pump_installation_status" class="form-select">
                                <option value="">Not recorded</option>
                                <option value="successful" @selected(old('pump_installation_status') === 'successful')>Successful</option>
                                <option value="unsuccessful" @selected(old('pump_installation_status') === 'unsuccessful')>Unsuccessful</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Personnel Information -->
                    <h5 class="mt-4 mb-3"><i class="fas fa-users text-secondary"></i> Personnel Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="operator" class="form-label">Operator</label>
                            <div class="input-group-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="operator" id="operator"
                                    value="{{ old('operator') }}" class="form-control" placeholder="Operator name">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hydrogeologist" class="form-label">Hydrogeologist</label>
                            <div class="input-group-icon">
                                <i class="fas fa-user-graduate"></i>
                                <input type="text" name="hydrogeologist" id="hydrogeologist"
                                    value="{{ old('hydrogeologist') }}" class="form-control" placeholder="Hydrogeologist name">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="drilling_supervisor" class="form-label">Drilling Supervisor</label>
                            <input type="text" name="drilling_supervisor" id="drilling_supervisor"
                                value="{{ old('drilling_supervisor') }}" class="form-control" placeholder="Drilling supervisor name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="technician" class="form-label">Technician</label>
                            <input type="text" name="technician" id="technician"
                                value="{{ old('technician') }}" class="form-control" placeholder="Technician name">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="assistant_operator" class="form-label">Assistant Operator</label>
                            <input type="text" name="assistant_operator" id="assistant_operator"
                                value="{{ old('assistant_operator') }}" class="form-control" placeholder="Assistant operator name">
                        </div>
                    </div>
                    
                    <!-- Dates Section -->
                    <h5 class="mt-4 mb-3"><i class="fas fa-calendar text-secondary"></i> Drilling Dates</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="drilling_start_date" class="form-label">Drilling Start Date</label>
                            <input type="date" name="drilling_start_date" id="drilling_start_date"
                                value="{{ old('drilling_start_date') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="drilling_end_date" class="form-label">Drilling End Date</label>
                            <input type="date" name="drilling_end_date" id="drilling_end_date"
                                value="{{ old('drilling_end_date') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="development_date" class="form-label">Development Date</label>
                            <input type="date" name="development_date" id="development_date"
                                value="{{ old('development_date') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="test_pumping_date" class="form-label">Test Pumping Date</label>
                            <input type="date" name="test_pumping_date" id="test_pumping_date"
                                value="{{ old('test_pumping_date') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="total_drilling_days" class="form-label">Total Drilling Days</label>
                            <input type="number" name="total_drilling_days" id="total_drilling_days"
                                value="{{ old('total_drilling_days') }}" class="form-control" placeholder="Enter total days">
                        </div>
                    </div>
                    
                    <!-- Remarks Section -->
                    <h5 class="mt-4 mb-3"><i class="fas fa-comments text-secondary"></i> Remarks & References</h5>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="rig_supervisor_remarks" class="form-label">Rig Supervisor Remarks</label>
                            <textarea name="rig_supervisor_remarks" id="rig_supervisor_remarks" class="form-control" rows="3" 
                                placeholder="Enter rig supervisor remarks">{{ old('rig_supervisor_remarks') }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="supervisor_remarks" class="form-label">General Supervisor Remarks</label>
                            <textarea name="supervisor_remarks" id="supervisor_remarks" class="form-control" rows="3" 
                                placeholder="Enter general supervisor remarks">{{ old('supervisor_remarks') }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="client_remarks" class="form-label">Client Remarks</label>
                            <textarea name="client_remarks" id="client_remarks" class="form-control" rows="3" 
                                placeholder="Enter client remarks">{{ old('client_remarks') }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="technical_remarks" class="form-label">Technical Remarks</label>
                            <textarea name="technical_remarks" id="technical_remarks" class="form-control" rows="3" 
                                placeholder="Enter technical remarks">{{ old('technical_remarks') }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="challenges_encountered" class="form-label">Challenges Encountered</label>
                            <textarea name="challenges_encountered" id="challenges_encountered" class="form-control" rows="3" 
                                placeholder="Enter challenges encountered">{{ old('challenges_encountered') }}</textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="recommendations" class="form-label">Recommendations</label>
                            <textarea name="recommendations" id="recommendations" class="form-control" rows="3" 
                                placeholder="Enter recommendations">{{ old('recommendations') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="references" class="form-label">References</label>
                            <div class="input-group-icon">
                                <i class="fas fa-book"></i>
                                <input type="text" name="references" id="references"
                                    value="{{ old('references') }}" class="form-control" placeholder="References">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="research" class="form-label">Research</label>
                            <div class="input-group-icon">
                                <i class="fas fa-search"></i>
                                <input type="text" name="research" id="research"
                                    value="{{ old('research') }}" class="form-control" placeholder="Research details">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <button type="button" class="btn btn-outline-primary prev-btn" data-prev="4">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-btn" data-next="6">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- CONSUMABLES SECTION -->
                <div class="form-section" id="section-6">
                    <h4 class="section-title"><i class="fas fa-gas-pump text-primary"></i> CONSUMABLES</h4>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fuel_type" class="form-label">Fuel Type</label>
                            <select name="fuel_type" id="fuel_type" class="form-control">
                                <option value="">Select Fuel Type</option>
                                <option value="DIESEL" {{ old('fuel_type') == 'DIESEL' ? 'selected' : '' }}>DIESEL</option>
                                <option value="PETROL" {{ old('fuel_type') == 'PETROL' ? 'selected' : '' }}>PETROL</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="electrodes" class="form-label">Electrodes</label>
                            <input type="text" name="electrodes" id="electrodes"
                                value="{{ old('electrodes') }}" class="form-control" placeholder="Enter electrodes">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prospectors" class="form-label">Prospectors</label>
                            <input type="text" name="prospectors" id="prospectors"
                                value="{{ old('prospectors') }}" class="form-control" placeholder="Enter prospectors">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hydraulic_oil" class="form-label">Hydraulic Oil</label>
                            <input type="text" name="hydraulic_oil" id="hydraulic_oil"
                                value="{{ old('hydraulic_oil') }}" class="form-control" placeholder="Enter hydraulic oil">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fuel_liters" class="form-label">Fuel (Liters)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-gas-pump"></i>
                                <input type="number" step="0.01" name="fuel_liters" id="fuel_liters"
                                    value="{{ old('fuel_liters') }}" class="form-control" placeholder="Fuel in liters">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="hydraulic_oil_liters" class="form-label">Hydraulic Oil (Liters)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-oil-can"></i>
                                <input type="number" step="0.01" name="hydraulic_oil_liters" id="hydraulic_oil_liters"
                                    value="{{ old('hydraulic_oil_liters') }}" class="form-control" placeholder="Hydraulic oil in liters">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gear_oil_liters" class="form-label">Gear Oil (Liters)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-oil-can"></i>
                                <input type="number" step="0.01" name="gear_oil_liters" id="gear_oil_liters"
                                    value="{{ old('gear_oil_liters') }}" class="form-control" placeholder="Gear oil in liters">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="grease_kg" class="form-label">Grease (Kg)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-weight"></i>
                                <input type="number" step="0.01" name="grease_kg" id="grease_kg"
                                    value="{{ old('grease_kg') }}" class="form-control" placeholder="Grease in kilograms">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="drilling_mileage_km" class="form-label">Drilling Mileage (Km)</label>
                            <div class="input-group-icon">
                                <i class="fas fa-road"></i>
                                <input type="number" step="0.01" name="drilling_mileage_km" id="drilling_mileage_km"
                                    value="{{ old('drilling_mileage_km') }}" class="form-control" placeholder="Mileage in kilometers">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label" for="consumables-table">Bit and hammer serial usage</label>
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle" id="consumables-table">
                                    <thead><tr><th>Equipment</th><th>Serial number</th><th>Quantity used</th><th></th></tr></thead>
                                    <tbody id="consumables-usage-container">
                                        <tr class="consumable-usage-row">
                                            <td>
                                                <select name="consumables[0][equipment_type]" class="form-control">
                                                    <option value="">Select equipment</option>
                                                    <option value="bit">Drilling bit</option>
                                                    <option value="hammer">Hammer / ring</option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="consumables[0][serial_number]" class="form-control" placeholder="Equipment serial number"></td>
                                            <td><input type="number" min="1" step="1" name="consumables[0][quantity_used]" class="form-control" placeholder="Quantity"></td>
                                            <td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-consumable" aria-label="Remove equipment row" style="display:none"><i class="fas fa-times" aria-hidden="true"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="add-consumable-usage" class="btn btn-outline-primary btn-sm"><i class="fas fa-plus me-1" aria-hidden="true"></i>Add serial</button>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="consumables_remarks" class="form-label">Consumables Remarks</label>
                            <textarea name="consumables_remarks" id="consumables_remarks" class="form-control" rows="3" placeholder="Enter consumables remarks">{{ old('consumables_remarks') }}</textarea>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <button type="button" class="btn btn-outline-primary prev-btn" data-prev="5">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-btn" data-next="7">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Location Coordinates -->
                <div class="form-section" id="section-7">
                    <h4 class="section-title"><i class="fas fa-map-marker-alt text-primary"></i> Location Coordinates</h4>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <div class="input-group-icon">
                                <i class="fas fa-globe-americas"></i>
                                <input type="number" step="0.000001" name="latitude" id="latitude"
                                    value="{{ old('latitude', $prospectingSite?->latitude ?? '') }}" class="form-control" placeholder="e.g., -15.123456">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <div class="input-group-icon">
                                <i class="fas fa-globe-americas"></i>
                                <input type="number" step="0.000001" name="longitude" id="longitude"
                                    value="{{ old('longitude', $prospectingSite?->longitude ?? '') }}" class="form-control" placeholder="e.g., 28.123456">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="grid_reference" class="form-label">Grid Reference</label>
                            <div class="input-group-icon">
                                <i class="fas fa-map"></i>
                                <input type="text" name="grid_reference" id="grid_reference"
                                    value="{{ old('grid_reference') }}" class="form-control" placeholder="Enter grid reference">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="elevation" class="form-label">Elevation</label>
                            <div class="input-group-icon">
                                <i class="fas fa-mountain"></i>
                                <input type="text" name="elevation" id="elevation"
                                    value="{{ old('elevation') }}" class="form-control" placeholder="Enter elevation">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <button type="button" class="btn btn-outline-primary prev-btn" data-prev="6">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="button" class="btn btn-primary next-btn" data-next="8">
                            Next <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Project Information -->
                <div class="form-section" id="section-8">
                    <h4 class="section-title"><i class="fas fa-project-diagram text-primary"></i> Project Information</h4>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="funding_agency" class="form-label">Funding Agency</label>
                            <div class="input-group-icon">
                                <i class="fas fa-hand-holding-usd"></i>
                                <input type="text" name="funding_agency" id="funding_agency"
                                    value="{{ old('funding_agency') }}" class="form-control" placeholder="Funding agency name">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="project_name" class="form-label">Project Name</label>
                            <div class="input-group-icon">
                                <i class="fas fa-project-diagram"></i>
                                <input type="text" name="project_name" id="project_name"
                                    value="{{ old('project_name', $prospectingSite?->project_name ?? '') }}" class="form-control" placeholder="Project name">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contractor" class="form-label">Contractor</label>
                            <div class="input-group-icon">
                                <i class="fas fa-hard-hat"></i>
                                <input type="text" name="contractor" id="contractor"
                                    value="{{ old('contractor') }}" class="form-control" placeholder="Contractor name">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="number_of_beneficiaries" class="form-label">Number of Beneficiaries</label>
                            <div class="input-group-icon">
                                <i class="fas fa-users"></i>
                                <input type="number" name="number_of_beneficiaries" id="number_of_beneficiaries"
                                    value="{{ old('number_of_beneficiaries') }}" class="form-control" placeholder="Number of beneficiaries">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="total_cost" class="form-label">Total Cost</label>
                            <div class="input-group-icon">
                                <i class="fas fa-money-bill-wave"></i>
                                <input type="number" step="0.01" name="total_cost" id="total_cost"
                                    value="{{ old('total_cost') }}" class="form-control" placeholder="Total cost">
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="cost_currency" class="form-label">Currency</label>
                            <select name="cost_currency" id="cost_currency" class="form-select">
                                <option value="USD" {{ old('cost_currency', 'USD') == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="ZMW" {{ old('cost_currency') == 'ZMW' ? 'selected' : '' }}>ZMW</option>
                                <option value="ZAR" {{ old('cost_currency') == 'ZAR' ? 'selected' : '' }}>ZAR</option>
                                <option value="GBP" {{ old('cost_currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="EUR" {{ old('cost_currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-footer">
                        <button type="button" class="btn btn-outline-primary prev-btn" data-prev="7">
                            <i class="fas fa-arrow-left me-1"></i> Previous
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i> Save Borehole Record
                        </button>
                    </div>
                </div>
            </form>
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

* {
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
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

.btn-danger {
    background: linear-gradient(145deg, var(--danger-color), #c82333);
    color: white;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.btn-danger:hover {
    background: linear-gradient(145deg, #c82333, var(--danger-color));
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(220, 53, 69, 0.4);
    color: white;
}

/* Button Loading State */
.btn.loading {
    position: relative;
    color: transparent !important;
    pointer-events: none;
}

.btn.loading::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    top: 50%;
    left: 50%;
    margin: -10px 0 0 -10px;
    border: 2px solid transparent;
    border-top-color: currentColor;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.btn.loading i:not(.fa-spinner) {
    opacity: 0;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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

/* Tables */
.table {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 1.5rem;
}

.table th {
    font-weight: 600;
    border: none;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 0.75rem;
    vertical-align: middle;
    border-color: #f1f3f4;
}

.table-primary {
    background: var(--gradient-primary);
    color: white;
}

.table-primary th {
    border-color: rgba(255, 255, 255, 0.2);
    font-weight: 600;
}

/* Add Field Buttons */
.btn-add-field {
    background: linear-gradient(145deg, rgba(127, 205, 187, 0.1), rgba(127, 205, 187, 0.05));
    color: var(--primary-color);
    border: 2px dashed var(--secondary-color);
    border-radius: 8px;
    padding: 0.875rem 1.5rem;
    font-weight: 600;
    transition: var(--transition);
    width: auto;
    margin: 1rem auto;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-add-field:hover {
    background: linear-gradient(145deg, rgba(127, 205, 187, 0.2), rgba(127, 205, 187, 0.1));
    border-style: solid;
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(127, 205, 187, 0.3);
    color: var(--primary-color);
}

/* Validation States */
.is-valid {
    border-color: var(--success-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.15) !important;
}

.is-invalid {
    border-color: var(--danger-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.15) !important;
    animation: shake 0.5s ease-in-out;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: var(--danger-color);
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
}

/* Custom Notifications */
.custom-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    border-radius: 8px;
    border: none;
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Alert Styles */
.alert {
    border: none;
    border-radius: 8px;
    padding: 1rem 1.5rem;
    font-weight: 500;
}

.alert-success {
    background: linear-gradient(135deg, var(--success-color), #1e7e34);
    color: white;
}

.alert-danger {
    background: linear-gradient(135deg, var(--danger-color), #c82333);
    color: white;
}

.alert-info {
    background: linear-gradient(135deg, var(--info-color), #138496);
    color: white;
}

/* Grid System Enhancements */
.row {
    margin-left: -0.75rem;
    margin-right: -0.75rem;
}

.col-md-6, .col-md-4, .col-md-12, .col-12 {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
}

.mb-3 {
    margin-bottom: 1.5rem !important;
}

.mt-4 {
    margin-top: 2rem !important;
}

/* Textarea Styles */
textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

/* Select Dropdown Enhancements */
.form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 16px 12px;
    padding-right: 2.5rem;
}

/* Table Action Buttons */
.btn-remove-geology,
.btn-remove-casing {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
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
    
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
    
    .custom-notification {
        left: 1rem;
        right: 1rem;
        max-width: none;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1rem;
    }
    
    .form-section {
        padding: 1rem;
    }
    
    .nav-pill {
        min-width: 80px;
        padding: 0.5rem 0.75rem;
    }
    
    .nav-pill i {
        font-size: 1rem;
    }
    
    .progress {
        margin-bottom: 1.5rem;
    }
}

/* Print Styles */
@media print {
    .nav-pills-custom,
    .progress,
    .form-footer .btn:not(.btn-success) {
        display: none !important;
    }
    
    .form-section {
        display: block !important;
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        page-break-inside: avoid;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}

/* High Contrast Mode Support */
@media (prefers-contrast: high) {
    .form-control,
    .form-select {
        border-width: 2px;
    }
    
    .btn {
        border-width: 2px;
    }
}

/* Reduced Motion Support */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .form-section {
        animation: none;
    }
    
    .btn::before {
        display: none;
    }
}

/* Focus Visible for Accessibility */
.btn:focus-visible,
.form-control:focus-visible,
.form-select:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
    .card {
        background: #2d3748;
        color: #e2e8f0;
    }
    
    .form-control,
    .form-select {
        background: #4a5568;
        border-color: #718096;
        color: #e2e8f0;
    }
    
    .form-control:focus,
    .form-select:focus {
        background: #4a5568;
        color: #e2e8f0;
    }
}

/* Utility Classes */
.text-center {
    text-align: center;
}

.text-danger {
    color: var(--danger-color) !important;
}

.text-success {
    color: var(--success-color) !important;
}

.small {
    font-size: 0.875em;
}

.mt-3 {
    margin-top: 1rem !important;
}

.mb-4 {
    margin-bottom: 2rem !important;
}

/* Ensure form elements are properly stacked */
.form-section .row {
    display: flex;
    flex-wrap: wrap;
}

.form-section .col-md-6 {
    flex: 0 0 auto;
    width: 50%;
}

@media (max-width: 768px) {
    .form-section .col-md-6 {
        width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form navigation variables
    const sections = document.querySelectorAll('.form-section');
    const navPills = document.querySelectorAll('.nav-pill');
    const progressBar = document.getElementById('form-progress');
    const form = document.getElementById('boreholeForm');
    let currentSection = 0;
    
    // Debug logging
    console.log('Form initialized');
    console.log('Sections found:', sections.length);
    console.log('Form element:', form);

    // Initialize form sections
    function initializeFormSections() {
        sections.forEach((section, index) => {
            section.setAttribute('data-section-id', index);
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
    }
    
    // Initialize form
    initializeFormSections();
    updateProgress(0);
    updateNavigation(0);
    
    // Navigation pill click handlers
    navPills.forEach((pill, index) => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Navigation pill clicked:', index);
            navigateToSection(index);
        });
    });
    
    // Next/Previous button handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.next-btn')) {
            const nextSection = parseInt(e.target.closest('.next-btn').dataset.next);
            console.log('Next button clicked, moving to section:', nextSection);
            navigateToSection(nextSection);
        }
        
        if (e.target.closest('.prev-btn')) {
            const prevSection = parseInt(e.target.closest('.prev-btn').dataset.prev);
            console.log('Previous button clicked, moving to section:', prevSection);
            navigateToSection(prevSection);
        }
    });
    
    // Enhanced navigation function
    function navigateToSection(targetSection) {
        console.log('Navigating from', currentSection, 'to', targetSection);
        
        // Validate current section only when moving forward
        if (targetSection > currentSection && !validateCurrentSection()) {
            console.log('Validation failed, staying on current section');
            showSection(currentSection);
            return false;
        }
        
        currentSection = targetSection;
        
        setTimeout(() => {
            showSection(targetSection);
            updateProgress(targetSection);
            updateNavigation(targetSection);
            
            // Smooth scroll to top of form
            document.querySelector('.card-body').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }, 50);
        
        return true;
    }
    
    // Update progress bar
    function updateProgress(index) {
        const progressPercentage = ((index + 1) / sections.length) * 100;
        progressBar.style.width = `${progressPercentage}%`;
        progressBar.setAttribute('aria-valuenow', progressPercentage);
        console.log('Progress updated:', progressPercentage + '%');
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
    
    // Validation function for current section
    function validateCurrentSection() {
        const currentSectionEl = sections[currentSection];
        const requiredFields = currentSectionEl.querySelectorAll('[required]');
        let isValid = true;
        
        console.log('Validating section', currentSection, 'with', requiredFields.length, 'required fields');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
                showFieldError(field, 'This field is required');
                console.log('Field validation failed:', field.name);
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                hideFieldError(field);
            }
        });
        
        if (!isValid) {
            // Focus first invalid field
            const firstInvalid = currentSectionEl.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            
            showNotification('Please fill in all required fields before proceeding.', 'error');
        }
        
        return isValid;
    }
    
    // Field validation helpers
    function showFieldError(field, message) {
        hideFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback d-block';
        errorDiv.textContent = message;
        errorDiv.style.color = '#dc3545';
        errorDiv.style.fontSize = '0.875em';
        errorDiv.style.marginTop = '0.25rem';
        
        field.parentNode.appendChild(errorDiv);
    }
    
    function hideFieldError(field) {
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
    }
    
    // Real-time validation
    document.querySelectorAll('[required]').forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                hideFieldError(this);
            }
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.value.trim()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                hideFieldError(this);
            }
        });
    });
    
    // Notification system
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existing = document.querySelector('.custom-notification');
        if (existing) existing.remove();
        
        const notification = document.createElement('div');
        notification.className = `custom-notification alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
        `;
        
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${type === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle'} me-2"></i>
                <span>${message}</span>
            </div>
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Auto-calculate geology layer FROM fields
    document.addEventListener('input', function(e) {
        // Auto-calculate geology layer FROM based on previous layer's TO
        if (e.target.name.includes('description_layer_depth_to')) {
            const row = e.target.closest('tr');
            const toValue = parseFloat(e.target.value) || 0;
            const nextRow = row.nextElementSibling;
            
            if (nextRow && nextRow.classList.contains('geology-layer')) {
                const nextFromInput = nextRow.querySelector('input[name*="description_layer_depth_from"]');
                if (nextFromInput) {
                    nextFromInput.value = toValue;
                }
            }
        }
        
        // Auto-calculate casing FROM based on previous casing's TO
        if (e.target.name.includes('depth_to')) {
            const row = e.target.closest('tr');
            const toValue = parseFloat(e.target.value) || 0;
            const nextRow = row.nextElementSibling;
            
            if (nextRow && nextRow.classList.contains('casing-detail')) {
                const nextFromInput = nextRow.querySelector('input[name*="depth_from"]');
                if (nextFromInput) {
                    nextFromInput.value = toValue;
                }
            }
        }
    });

    // Dynamic table management
    let geologyCounter = document.querySelectorAll('#geology-layers-container tr').length;
    let casingCounter = document.querySelectorAll('#casing-details-container tr').length;
    let consumableCounter = document.querySelectorAll('#consumables-usage-container tr').length;
    let bitIntervalCounter = document.querySelectorAll('#bit-size-intervals-container tr').length;
    
    // Geology layers
    document.getElementById('add-geology-layer')?.addEventListener('click', function() {
        const container = document.getElementById('geology-layers-container');
        const template = container.querySelector('tr').cloneNode(true);
        
        // Update input names and clear values
        template.querySelectorAll('input').forEach(input => {
            input.name = input.name.replace('[0]', `[${geologyCounter}]`);
            if (!input.hasAttribute('data-keep-value')) {
                input.value = '';
            }
        });
        
        // Show remove button
        const removeBtn = template.querySelector('.btn-remove-geology');
        removeBtn.style.display = 'block';
        removeBtn.addEventListener('click', function() {
            if (document.querySelectorAll('#geology-layers-container tr').length > 1) {
                template.remove();
            }
        });
        
        container.appendChild(template);
        geologyCounter++;
        console.log('Added geology layer, counter:', geologyCounter);
    });
    
    // Casing details
    document.getElementById('add-casing-detail')?.addEventListener('click', function() {
        const container = document.getElementById('casing-details-container');
        const template = container.querySelector('tr').cloneNode(true);
        
        // Update input names and clear values
        template.querySelectorAll('input, select').forEach(input => {
            if (input.name) {
                input.name = input.name.replace('[0]', `[${casingCounter}]`);
                if (input.type === 'checkbox') {
                    input.checked = false;
                    input.value = '1';
                } else if (input.name.includes('position')) {
                    input.value = casingCounter + 1;
                } else if (!input.hasAttribute('data-keep-value')) {
                    input.value = '';
                    if (input.tagName === 'SELECT') {
                        input.selectedIndex = 0;
                    }
                }
            }
        });
        
        // Show remove button
        const removeBtn = template.querySelector('.btn-remove-casing');
        removeBtn.style.display = 'block';
        removeBtn.addEventListener('click', function() {
            if (document.querySelectorAll('#casing-details-container tr').length > 1) {
                template.remove();
            }
        });
        
        container.appendChild(template);
        casingCounter++;
        console.log('Added casing detail, counter:', casingCounter);
    });

    document.getElementById('add-consumable-usage')?.addEventListener('click', function() {
        const container = document.getElementById('consumables-usage-container');
        const template = container.querySelector('tr').cloneNode(true);
        template.querySelectorAll('input, select').forEach(field => {
            field.name = field.name.replace(/consumables\[\d+\]/, `consumables[${consumableCounter}]`);
            field.value = '';
            if (field.tagName === 'SELECT') field.selectedIndex = 0;
        });
        template.querySelector('.btn-remove-consumable').style.display = 'inline-block';
        container.appendChild(template);
        consumableCounter++;
    });

    document.getElementById('add-bit-size-interval')?.addEventListener('click', function() {
        const container = document.getElementById('bit-size-intervals-container');
        const template = container.querySelector('tr').cloneNode(true);
        template.querySelectorAll('input').forEach(field => {
            field.name = field.name.replace(/bit_size_intervals\[\d+\]/, `bit_size_intervals[${bitIntervalCounter}]`);
            field.value = '';
        });
        template.querySelector('.btn-remove-bit-interval').style.display = 'inline-block';
        container.appendChild(template);
        bitIntervalCounter++;
    });
    
    // Remove button handlers
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-remove-geology')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#geology-layers-container tr').length > 1) {
                row.remove();
            }
        }
        
        if (e.target.closest('.btn-remove-casing')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#casing-details-container tr').length > 1) {
                row.remove();
            }
        }

        if (e.target.closest('.btn-remove-consumable')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#consumables-usage-container tr').length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input').forEach(field => field.value = '');
                row.querySelector('select').selectedIndex = 0;
            }
        }

        if (e.target.closest('.btn-remove-bit-interval')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#bit-size-intervals-container tr').length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input').forEach(field => field.value = '');
            }
        }
    });
    
    // PISP specification toggle
    const sitingAgency = document.getElementById('siting_agency');
    if (sitingAgency) {
        sitingAgency.addEventListener('change', function() {
            const pispField = document.getElementById('pisp_specification_field');
            if (pispField) {
                pispField.style.display = this.value === 'PISP' ? 'block' : 'none';
            }
        });
        
        // Initialize on page load
        if (sitingAgency.value === 'PISP') {
            document.getElementById('pisp_specification_field').style.display = 'block';
        }
    }
    
    // ENHANCED FORM SUBMISSION HANDLER
form?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('=== FORM SUBMISSION TRIGGERED ===');
    
    // Validate all sections before submission
    let allValid = true;
    const invalidSections = [];
    
    sections.forEach((section, index) => {
        const sectionValid = validateSection(index);
        if (!sectionValid) {
            allValid = false;
            invalidSections.push(index + 1);
        }
    });
    
    if (!allValid) {
        console.log('Form validation failed in sections:', invalidSections);
        showNotification(`Please fix errors in sections: ${invalidSections.join(', ')}`, 'error');
        
        // Navigate to first invalid section
        navigateToSection(invalidSections[0] - 1);
        return;
    }
    
    console.log('Form validation passed, proceeding with submission');
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving Borehole Record...';
    
    showNotification('Saving borehole record, please wait...', 'info');
    
    // Prepare form data
    const formData = new FormData(form);
    
    // Log form data for debugging
    console.log('=== FORM DATA ===');
    for (let [key, value] of formData.entries()) {
        console.log(key + ': ' + value);
    }
    
    // Submit via AJAX with proper error handling
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                           document.querySelector('input[name="_token"]')?.value
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response content-type:', response.headers.get('content-type'));
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // If not JSON, get the text to see what's wrong
            return response.text().then(text => {
                console.log('Non-JSON response:', text.substring(0, 500));
                throw new Error('Server returned HTML instead of JSON. Possible authentication issue or server error.');
            });
        }
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showNotification('✅ Borehole record saved successfully! Redirecting...', 'success');
            
            // Clear any saved draft
            localStorage.removeItem('borehole_form_draft');
            
            // Redirect after delay
            setTimeout(() => {
                window.location.href = data.redirect_url || '/boreholes';
            }, 2000);
            
        } else {
            throw new Error(data.message || 'Server returned error');
        }
    })
    .catch(error => {
        console.error('Submission error:', error);
        
        let errorMessage = 'Error saving borehole record. ';
        
        if (error.message.includes('Failed to fetch')) {
            errorMessage += 'Network error. Please check your connection.';
        } else if (error.message.includes('HTML instead of JSON')) {
            errorMessage += 'Server error. Please check if you are logged in and try again.';
        } else if (error.message.includes('HTTP error')) {
            errorMessage += 'Server error. Please try again.';
        } else {
            errorMessage += error.message;
        }
        
        showNotification(errorMessage, 'error');
        
        // Reset submit button
        submitBtn.classList.remove('loading');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});
    
    // Validate specific section
    function validateSection(index) {
        const section = sections[index];
        const requiredFields = section.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
                showFieldError(field, 'This field is required');
            } else {
                field.classList.remove('is-invalid');
                field.classList.add('is-valid');
                hideFieldError(field);
            }
        });
        
        return isValid;
    }
    
    function resetSubmitButton(btn, originalText) {
        if (btn) {
            btn.classList.remove('loading');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case 'ArrowRight':
                    e.preventDefault();
                    if (currentSection < sections.length - 1) {
                        navigateToSection(currentSection + 1);
                    }
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    if (currentSection > 0) {
                        navigateToSection(currentSection - 1);
                    }
                    break;
            }
        }
        
        // Enter key to move to next section (except in textareas)
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && !e.target.closest('.btn')) {
            e.preventDefault();
            if (currentSection < sections.length - 1) {
                navigateToSection(currentSection + 1);
            }
        }
    });
    
    // Auto-save functionality
    let autoSaveTimeout;
    form?.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            localStorage.setItem('borehole_form_draft', JSON.stringify(data));
            console.log('Form auto-saved');
        }, 3000);
    });
    
    // Load auto-saved data
    const savedData = localStorage.getItem('borehole_form_draft');
    if (savedData) {
        try {
            const data = JSON.parse(savedData);
            let loadedFields = 0;
            
            Object.entries(data).forEach(([name, value]) => {
                const field = form?.querySelector(`[name="${name}"]`);
                if (field && !field.value) {
                    field.value = value;
                    loadedFields++;
                }
            });
            
            if (loadedFields > 0) {
                showNotification(`Loaded ${loadedFields} saved fields from draft`, 'info');
            }
            
        } catch (e) {
            console.warn('Could not load saved form data:', e);
        }
    }
    
    // Form reset handler
    form?.addEventListener('reset', function() {
        localStorage.removeItem('borehole_form_draft');
        showNotification('Form reset successfully', 'info');
    });
    
    // Test function for debugging
    window.testFormSubmission = function() {
        console.log('=== MANUAL FORM TEST ===');
        console.log('Form valid:', form.checkValidity());
        console.log('Current section:', currentSection);
        
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.click();
        }
    };
    
    console.log('Form JavaScript initialized successfully');
});

// Global function to manually trigger form submission (for debugging)
function manualSubmit() {
    document.getElementById('boreholeForm').dispatchEvent(new Event('submit'));
}
</script>

@endsection