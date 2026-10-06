@extends('layouts.app')

@section('title', 'Borehole Details - ' . $borehole->borehole_serial_no)

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-water me-2"></i>Borehole Details
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('boreholes.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                        <a href="{{ route('boreholes.certificate.download', $borehole) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-file-signature me-1"></i> Certificate
                        </a>
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route($borehole->approved_at ? 'boreholes.unapprove' : 'boreholes.approve', $borehole) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-{{ $borehole->approved_at ? 'outline-light' : 'success' }} btn-sm">
                                    {{ $borehole->approved_at ? 'Remove approval' : 'Approve' }}
                                </button>
                            </form>
                        @endif
                        @if(auth()->user()->isAdmin() || (!$borehole->isApproved() && auth()->user()->isOperator()))
                            <a href="{{ route('boreholes.edit', $borehole) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        @endif
                        @if(auth()->user()->isAdmin())
                            <form action="{{ route('boreholes.destroy', $borehole) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" 
                                    onclick="return confirm('Are you sure you want to delete this borehole record?')">
                                    <i class="fas fa-trash me-1"></i> Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
                    @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
                    <!-- Status Badge -->
                    <div class="row mb-4">
                        <div class="col-12">
                            @if($borehole->isApproved())
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle me-1"></i> Approved
                                </span>
                            @else
                                <span class="badge bg-warning text-dark fs-6">
                                    <i class="fas fa-clock me-1"></i> Pending Approval
                                </span>
                            @endif
                            <span class="badge bg-info fs-6 ms-2">
                                Serial No: {{ $borehole->borehole_serial_no }}
                            </span>
                        </div>
                    </div>

                    <!-- Main Information Tabs -->
                    <ul class="nav nav-tabs" id="boreholeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="fas fa-info-circle me-1"></i> General
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="drilling-tab" data-bs-toggle="tab" data-bs-target="#drilling" type="button" role="tab">
                                <i class="fas fa-tools me-1"></i> Drilling
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="geology-tab" data-bs-toggle="tab" data-bs-target="#geology" type="button" role="tab">
                                <i class="fas fa-mountain me-1"></i> Geology
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="casing-tab" data-bs-toggle="tab" data-bs-target="#casing" type="button" role="tab">
                                <i class="fas fa-layer-group me-1"></i> Casing
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="water-tab" data-bs-toggle="tab" data-bs-target="#water" type="button" role="tab">
                                <i class="fas fa-tint me-1"></i> Water
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pump-tab" data-bs-toggle="tab" data-bs-target="#pump" type="button" role="tab">
                                <i class="fas fa-pump-soap me-1"></i> Pump
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="consumables-tab" data-bs-toggle="tab" data-bs-target="#consumables" type="button" role="tab">
                                <i class="fas fa-gas-pump me-1"></i> Consumables
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="location-tab" data-bs-toggle="tab" data-bs-target="#location" type="button" role="tab">
                                <i class="fas fa-map-marker-alt me-1"></i> Location
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="project-tab" data-bs-toggle="tab" data-bs-target="#project" type="button" role="tab">
                                <i class="fas fa-project-diagram me-1"></i> Project
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="boreholeTabsContent">
                        <!-- General Information Tab -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Basic Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Serial Number:</td>
                                            <td>{{ $borehole->borehole_serial_no }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Date:</td>
                                            <td>{{ $borehole->date ? $borehole->date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Province:</td>
                                            <td>{{ $borehole->province ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Admin District:</td>
                                            <td>{{ $borehole->admin_district ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Locality:</td>
                                            <td>{{ $borehole->locality ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Ward:</td>
                                            <td>{{ $borehole->ward ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Village:</td>
                                            <td>{{ $borehole->village ?? $borehole->vidco ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Usage:</td>
                                            <td>{{ $borehole->usage ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-cogs me-2"></i>Equipment Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">DTH Bit Serial No:</td>
                                            <td>{{ $borehole->dth_bit_serial_no ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Hammer Serial No:</td>
                                            <td>{{ $borehole->hammer_serial_no ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">RIG NUMBER:</td>
                                            <td>{{ $borehole->rig_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">RIG TYPE:</td>
                                            <td>{{ $borehole->rig_type ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Drilled Depth:</td>
                                            <td>{{ $borehole->rdd_depth ? $borehole->rdd_depth . ' m' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">RIG CHASIS NUMBER:</td>
                                            <td>{{ $borehole->rig_chasis_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Siting Agency:</td>
                                            <td>{{ $borehole->siting_agency ?? 'N/A' }}</td>
                                        </tr>
                                        @if($borehole->siting_agency == 'PISP')
                                        <tr>
                                            <td class="fw-bold">PISP Specification:</td>
                                            <td>{{ $borehole->pisp_specification ?? 'N/A' }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td class="fw-bold">Drilling Dates:</td>
                                            <td>
                                                @if($borehole->drilling_date_from && $borehole->drilling_date_to)
                                                    {{ $borehole->drilling_date_from->format('M d, Y') }} - {{ $borehole->drilling_date_to->format('M d, Y') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Drilling Details Tab -->
                        <div class="tab-pane fade" id="drilling" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-ruler-combined me-2"></i>Drilling Details
                                    </h5>
                                    <table class="table table-borderless">
                                        @forelse($borehole->bitSizeIntervals as $interval)
                                            <tr>
                                                <td class="fw-bold" style="width: 40%">Bit size:</td>
                                                <td>{{ $interval->bit_size_mm }} mm, {{ $interval->depth_from_m }} m to {{ $interval->depth_to_m }} m</td>
                                            </tr>
                                        @empty
                                            @foreach([$borehole->first_drilled_diameter_mm, $borehole->second_drilled_diameter_mm, $borehole->third_drilled_diameter_mm] as $legacyBitSize)
                                                @if($legacyBitSize)
                                                    <tr><td class="fw-bold" style="width: 40%">Legacy bit size:</td><td>{{ $legacyBitSize }} mm (depth interval not recorded)</td></tr>
                                                @endif
                                            @endforeach
                                            @if(!$borehole->first_drilled_diameter_mm && !$borehole->second_drilled_diameter_mm && !$borehole->third_drilled_diameter_mm)
                                                <tr><td class="fw-bold" style="width: 40%">Bit size intervals:</td><td>N/A</td></tr>
                                            @endif
                                        @endforelse
                                        <tr>
                                            <td class="fw-bold">Drilled Depth:</td>
                                            <td>{{ $borehole->final_borehole_depth_m ? $borehole->final_borehole_depth_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Blowing Yield:</td>
                                            <td>{{ $borehole->blowing_yield ? $borehole->blowing_yield . ' l/s' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Development Hours:</td>
                                            <td>{{ $borehole->development_hours ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-layer-group me-2"></i>Gravel Packing
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">From:</td>
                                            <td>{{ $borehole->gravel_packing_from_m ? $borehole->gravel_packing_from_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">To:</td>
                                            <td>{{ $borehole->gravel_packing_to_m ? $borehole->gravel_packing_to_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Gravel Type:</td>
                                            <td>{{ $borehole->gravel_type ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Gravel Thickness:</td>
                                            <td>{{ $borehole->gravel_thickness_m ? $borehole->gravel_thickness_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-primary mb-3 mt-4">
                                        <i class="fas fa-layer-group me-2"></i>Cement Sealing
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">From:</td>
                                            <td>{{ $borehole->cement_sealing_from_m ? $borehole->cement_sealing_from_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">To:</td>
                                            <td>{{ $borehole->cement_sealing_to_m ? $borehole->cement_sealing_to_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Cement Type:</td>
                                            <td>{{ $borehole->cement_type ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Cement Volume:</td>
                                            <td>{{ $borehole->cement_volume_bags ? $borehole->cement_volume_bags . ' bags' : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Geology Information Tab -->
                        <div class="tab-pane fade" id="geology" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-mountain me-2"></i>Geology Layers
                                    </h5>
                                    @if(isset($borehole->geologyLayers) && $borehole->geologyLayers->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th>GEOLOGY SAMPLE</th>
                                                        <th colspan="2">DEPTH RANGE</th>
                                                        <th colspan="2">PENETRATION RATES</th>
                                                        <th>WATER STRIKE</th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th>FROM (m)</th>
                                                        <th>TO (m)</th>
                                                        <th>TO (m)</th>
                                                        <th>RATE (min/rod)</th>
                                                        <th>at ______ m</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($borehole->geologyLayers as $layer)
                                                        <tr>
                                                            <td>{{ $layer->sample_description ?? 'N/A' }}</td>
                                                            <td>{{ $layer->description_layer_depth_from ?? 'N/A' }}</td>
                                                            <td>{{ $layer->description_layer_depth_to ?? 'N/A' }}</td>
                                                            <td>{{ $layer->penetration_rates_to ?? 'N/A' }}</td>
                                                            <td>{{ $layer->penetration_rates_rate ?? 'N/A' }}</td>
                                                            <td>{{ $layer->water_strike ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>No geology layers recorded.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Casing Details Tab -->
                        <div class="tab-pane fade" id="casing" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-layer-group me-2"></i>Casing Details
                                    </h5>
                                    @if(isset($borehole->casingDetails) && $borehole->casingDetails->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th>DESCRIPTION</th>
                                                        <th>DIAMETER (mm)</th>
                                                        <th>CASING TYPE</th>
                                                        <th>CLASS</th>
                                                        <th colspan="2">DEPTHS</th>
                                                        <th>GRAVEL PACKED</th>
                                                        <th>CEMENT SEALED</th>
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
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($borehole->casingDetails as $casing)
                                                        <tr>
                                                            <td>{{ $casing->description ?? 'N/A' }}</td>
                                                            <td>{{ $casing->diameter ? $casing->diameter . ' mm' : 'N/A' }}</td>
                                                            <td>{{ $casing->casing_type ?? 'N/A' }}</td>
                                                            <td>{{ $casing->casing_class ?? 'N/A' }}</td>
                                                            <td>{{ $casing->depth_from ?? 'N/A' }}</td>
                                                            <td>{{ $casing->depth_to ?? 'N/A' }}</td>
                                                            <td>{{ $casing->gravel_packed ? 'Yes' : 'No' }}</td>
                                                            <td>{{ $casing->cement_sealed ? 'Yes' : 'No' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>No casing details available.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Additional Casing Information -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Additional Casing Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Final Cased Depth:</td>
                                            <td>{{ $borehole->final_cased_depth ?? $borehole->final_borehole_depth ?? 'N/A' }} m</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Water Quality Tab -->
                        <div class="tab-pane fade" id="water" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-tint me-2"></i>Water Quality
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">TDS:</td>
                                            <td>{{ $borehole->tds_mg_l ? $borehole->tds_mg_l . ' mg/l' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">pH:</td>
                                            <td>{{ $borehole->ph ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Water Appearance:</td>
                                            <td>{{ $borehole->water_appearance ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Pump Installation Tab -->
                        <div class="tab-pane fade" id="pump" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-cogs me-2"></i>Technical Details
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Type of Pump:</td>
                                            <td>{{ $borehole->pump_type ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Pipe Length:</td>
                                            <td>{{ $borehole->pipe_length_m ? $borehole->pipe_length_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Cylinder Size:</td>
                                            <td>{{ $borehole->cylinder_size_m ? $borehole->cylinder_size_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Static Water Level:</td>
                                            <td>{{ $borehole->swl_m ? $borehole->swl_m . ' m' : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>Installation Dates
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Installation Date:</td>
                                            <td>{{ $borehole->pump_installation_date ? $borehole->pump_installation_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Record Date:</td>
                                            <td>{{ $borehole->record_date ? $borehole->record_date->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Installed By:</td>
                                            <td>{{ $borehole->installed_by_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Organisation:</td>
                                            <td>{{ $borehole->installed_by_organisation ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Installation outcome:</td>
                                            <td>{{ $borehole->pump_installation_status ? ucfirst($borehole->pump_installation_status) : 'Not recorded' }}</td>
                                        </tr>
                                    </table>

                                    <h5 class="text-primary mb-3 mt-4">
                                        <i class="fas fa-users me-2"></i>Personnel
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Operator:</td>
                                            <td>{{ $borehole->operator ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Hydrogeologist:</td>
                                            <td>{{ $borehole->hydrogeologist ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Remarks Section -->
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">
                                                <i class="fas fa-clipboard-check me-2"></i>Rig Supervisor Remarks
                                            </h6>
                                            <p class="card-text">{{ $borehole->rig_supervisor_remarks ?? 'No remarks provided' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">
                                                <i class="fas fa-user-tie me-2"></i>General Supervisor Remarks
                                            </h6>
                                            <p class="card-text">{{ $borehole->supervisor_remarks ?? 'No remarks provided' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title text-primary">
                                                <i class="fas fa-user-tie me-2"></i>Client Remarks
                                            </h6>
                                            <p class="card-text">{{ $borehole->client_remarks ?? 'No remarks provided' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- References & Research -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-book me-2"></i>References & Research
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">References:</td>
                                            <td>{{ $borehole->references ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Research:</td>
                                            <td>{{ $borehole->research ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Consumables Tab -->
                        <div class="tab-pane fade" id="consumables" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-gas-pump me-2"></i>Consumables Details
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Fuel Type:</td>
                                            <td>{{ $borehole->fuel_type ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Electrodes:</td>
                                            <td>{{ $borehole->electrodes ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Prospectors:</td>
                                            <td>{{ $borehole->prospectors ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Hydraulic Oil:</td>
                                            <td>{{ $borehole->hydraulic_oil ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Fuel:</td>
                                            <td>{{ $borehole->fuel_liters ? $borehole->fuel_liters . ' liters' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Hydraulic Oil:</td>
                                            <td>{{ $borehole->hydraulic_oil_liters ? $borehole->hydraulic_oil_liters . ' liters' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Gear Oil:</td>
                                            <td>{{ $borehole->gear_oil_liters ? $borehole->gear_oil_liters . ' liters' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Grease:</td>
                                            <td>{{ $borehole->grease_kg ? $borehole->grease_kg . ' kg' : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-tools me-2"></i>Equipment Usage
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Drilling Mileage:</td>
                                            <td>{{ $borehole->drilling_mileage_km ? $borehole->drilling_mileage_km . ' km' : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                    <h6 class="text-primary mt-3">Serial-numbered equipment</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead><tr><th>Type</th><th>Serial number</th><th class="text-end">Quantity</th></tr></thead>
                                            <tbody>
                                                @forelse($borehole->consumableUsages as $usage)
                                                    <tr>
                                                        <td>{{ $usage->equipment_type === 'bit' ? 'Drilling bit' : 'Hammer / ring' }}</td>
                                                        <td>{{ $usage->serial_number }}</td>
                                                        <td class="text-end">{{ number_format($usage->quantity_used) }}</td>
                                                    </tr>
                                                @empty
                                                    @if($borehole->dth_bit_serial_no)
                                                        <tr><td>Drilling bit</td><td>{{ $borehole->dth_bit_serial_no }}</td><td class="text-end">{{ number_format($borehole->drilling_bits_used ?? 0) }}</td></tr>
                                                    @endif
                                                    @if($borehole->hammer_serial_no)
                                                        <tr><td>Hammer / ring</td><td>{{ $borehole->hammer_serial_no }}</td><td class="text-end">{{ number_format($borehole->hammer_rings_used ?? 0) }}</td></tr>
                                                    @endif
                                                    @if(!$borehole->dth_bit_serial_no && !$borehole->hammer_serial_no)
                                                        <tr><td colspan="3" class="text-muted">No serial-numbered equipment recorded.</td></tr>
                                                    @endif
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Consumables Remarks -->
                            @if($borehole->consumables_remarks)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card border-0 bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title text-primary">
                                                    <i class="fas fa-sticky-note me-2"></i>Consumables Remarks
                                                </h6>
                                                <p class="card-text">{{ $borehole->consumables_remarks }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Location Tab -->
                        <div class="tab-pane fade" id="location" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-map me-2"></i>Coordinates
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Latitude:</td>
                                            <td>{{ $borehole->latitude ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Longitude:</td>
                                            <td>{{ $borehole->longitude ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Grid Reference:</td>
                                            <td>{{ $borehole->grid_reference ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Elevation:</td>
                                            <td>{{ $borehole->elevation ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Project Information Tab -->
                        <div class="tab-pane fade" id="project" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-project-diagram me-2"></i>Project Details
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Funding Agency:</td>
                                            <td>{{ $borehole->funding_agency ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Project Name:</td>
                                            <td>{{ $borehole->project_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Contractor:</td>
                                            <td>{{ $borehole->contractor ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Beneficiaries:</td>
                                            <td>{{ $borehole->number_of_beneficiaries ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-money-bill-wave me-2"></i>Cost Information
                                    </h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold" style="width: 40%">Total Cost:</td>
                                            <td>
                                                @if($borehole->total_cost)
                                                    {{ number_format($borehole->total_cost, 2) }} {{ $borehole->cost_currency ?? 'USD' }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Currency:</td>
                                            <td>{{ $borehole->cost_currency ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-calendar-plus me-1"></i>
                                Created: {{ $borehole->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                <i class="fas fa-calendar-check me-1"></i>
                                Last Updated: {{ $borehole->updated_at->format('M d, Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Auto-activate tab from URL hash
    document.addEventListener('DOMContentLoaded', function() {
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('#boreholeTabs button[data-bs-target="' + hash + '"]');
            if (triggerEl) {
                bootstrap.Tab.getInstance(triggerEl).show();
            }
        }
    });
</script>

<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        background-color: #2c7fb8;
        color: white;
        border-color: #2c7fb8;
    }
    
    .table-borderless td {
        padding: 8px 0;
        border: none;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .badge {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }
</style>
@endsection