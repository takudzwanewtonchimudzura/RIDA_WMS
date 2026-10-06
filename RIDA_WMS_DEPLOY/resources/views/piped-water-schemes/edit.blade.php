{{-- resources/views/piped_water_scheme_maintenances/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Piped Water Scheme Maintenance</h5>
                    <a href="{{ route('piped-water-schemes.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('piped-water-schemes.update', $maintenance->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Scheme Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Scheme Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="piped_water_scheme_id">Select Scheme *</label>
                                            <select class="form-control @error('piped_water_scheme_id') is-invalid @enderror" 
                                                    id="piped_water_scheme_id" name="piped_water_scheme_id" required>
                                                <option value="">-- Select Scheme --</option>
                                                @foreach($schemes as $scheme)
                                                    <option value="{{ $scheme->id }}" 
                                                        {{ old('piped_water_scheme_id', $maintenance->id) == $scheme->id ? 'selected' : '' }}>
                                                        {{ $scheme->name_of_scheme }} - {{ $scheme->village }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('piped_water_scheme_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="name_of_scheme">Name of Scheme *</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name_of_scheme" name="name" 
                                                   value="{{ old('name', $maintenance->name) }}" required>
                                            @error('name')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="village">Village *</label>
                                                    <input type="text" class="form-control @error('village') is-invalid @enderror" 
                                                           id="village" name="village" 
                                                           value="{{ old('village', $maintenance->village) }}" required>
                                                    @error('village')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="ward">Ward *</label>
                                                    <input type="text" class="form-control @error('ward') is-invalid @enderror" 
                                                           id="ward" name="ward" 
                                                           value="{{ old('ward', $maintenance->ward) }}" required>
                                                    @error('ward')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Technical Details -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Technical Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="type_of_water_source">Type of Water Source *</label>
                                            <select class="form-control @error('water_source') is-invalid @enderror" 
                                                    id="type_of_water_source" name="water_source" required>
                                                <option value="">-- Select Source --</option>
                                                <option value="borehole" {{ old('water_source', $maintenance->water_source) == 'borehole' ? 'selected' : '' }}>Borehole</option>
                                                <option value="deep well" {{ old('water_source', $maintenance->water_source) == 'deep well' ? 'selected' : '' }}>Deep Well</option>
                                                <option value="spring" {{ old('water_source', $maintenance->water_source) == 'spring' ? 'selected' : '' }}>Spring</option>
                                                <option value="river" {{ old('water_source', $maintenance->water_source) == 'river' ? 'selected' : '' }}>River</option>
                                                <option value="dam" {{ old('water_source', $maintenance->water_source) == 'dam' ? 'selected' : '' }}>Dam</option>
                                                <option value="sand abstraction" {{ old('water_source', $maintenance->water_source) == 'sand abstraction' ? 'selected' : '' }}>Sand Abstraction</option>
                                                <option value="other" {{ old('water_source', $maintenance->water_source) == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('water_source')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="year_established">Year Established</label>
                                            <input type="number" class="form-control @error('year_established') is-invalid @enderror" 
                                                   id="year_established" name="year_established" 
                                                   value="{{ old('year_established', $maintenance->year_established) }}" 
                                                   min="1900" max="{{ date('Y') }}">
                                            @error('year_established')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="type_of_pump">Type of Pump</label>
                                                    <input type="text" class="form-control @error('pump_type') is-invalid @enderror" 
                                                           id="type_of_pump" name="pump_type" 
                                                           value="{{ old('pump_type', $maintenance->pump_type) }}">
                                                    @error('pump_type')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="type_of_power">Type of Power</label>
                                                    <select class="form-control @error('power_type') is-invalid @enderror" 
                                                            id="type_of_power" name="power_type">
                                                        <option value="">-- Select Power --</option>
                                                        <option value="solar" {{ old('power_type', $maintenance->power_type) == 'solar' ? 'selected' : '' }}>Solar</option>
                                                        <option value="electricity" {{ old('power_type', $maintenance->power_type) == 'electricity' ? 'selected' : '' }}>Electricity</option>
                                                        <option value="fossil fuel" {{ old('power_type', $maintenance->power_type) == 'fossil fuel' ? 'selected' : '' }}>Fossil Fuel</option>
                                                        <option value="wind" {{ old('power_type', $maintenance->power_type) == 'wind' ? 'selected' : '' }}>Wind</option>
                                                        <option value="gravity" {{ old('power_type', $maintenance->power_type) == 'gravity' ? 'selected' : '' }}>Gravity</option>
                                                    </select>
                                                    @error('power_type')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Infrastructure Details -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">Infrastructure Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="network_length">Network Length (Metres)</label>
                                                    <input type="number" class="form-control @error('network_length') is-invalid @enderror" 
                                                           id="network_length" name="network_length" 
                                                           value="{{ old('network_length', $maintenance->network_length) }}" 
                                                           min="0" step="0.01">
                                                    @error('network_length')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="total_storage_tanks_in_m3">Total Storage Tanks (m³)</label>
                                                    <input type="number" class="form-control @error('storage_tanks') is-invalid @enderror" 
                                                           id="total_storage_tanks_in_m3" name="storage_tanks" 
                                                           value="{{ old('storage_tanks', $maintenance->storage_tanks) }}" 
                                                           min="0" step="0.01">
                                                    @error('storage_tanks')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="number_of_stand_pipes">Number of Stand Pipes</label>
                                                    <input type="number" class="form-control @error('stand_pipes_count') is-invalid @enderror" 
                                                           id="number_of_stand_pipes" name="stand_pipes_count" 
                                                           value="{{ old('stand_pipes_count', $maintenance->stand_pipes_count) }}" 
                                                           min="0">
                                                    @error('stand_pipes_count')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Maintenance Details -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card mb-4">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="mb-0">Maintenance Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="repaired_by">Repaired By</label>
                                                    <input type="text" class="form-control @error('repaired_by') is-invalid @enderror" 
                                                           id="repaired_by" name="repaired_by" 
                                                           value="{{ old('repaired_by', $maintenance->repaired_by) }}">
                                                    @error('repaired_by')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="spares_fitted">Spares Fitted</label>
                                                    <input type="text" class="form-control @error('spares_fitted') is-invalid @enderror" 
                                                           id="spares_fitted" name="spares_fitted" 
                                                           value="{{ old('spares_fitted', $maintenance->spares_fitted) }}">
                                                    @error('spares_fitted')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="spares_received_by">Spares Received By</label>
                                                    <input type="text" class="form-control @error('spares_received_by') is-invalid @enderror" 
                                                           id="spares_received_by" name="spares_received_by" 
                                                           value="{{ old('spares_received_by', $maintenance->spares_received_by) }}">
                                                    @error('spares_received_by')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="contact_detail">Contact Detail</label>
                                                    <input type="text" class="form-control @error('contact_detail') is-invalid @enderror" 
                                                           id="contact_detail" name="contact_detail" 
                                                           value="{{ old('contact_detail', $maintenance->contact_detail) }}">
                                                    @error('contact_detail')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="date_repaired">Date Repaired</label>
                                                    <input type="date" class="form-control @error('date_repaired') is-invalid @enderror" 
                                                           id="date_repaired" name="date_repaired" 
                                                           value="{{ old('date_repaired', $maintenance->date_repaired ? $maintenance->date_repaired->format('Y-m-d') : '') }}">
                                                    @error('date_repaired')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="date_last_repaired">Date Last Repaired</label>
                                                    <input type="date" class="form-control @error('date_last_repaired') is-invalid @enderror" 
                                                           id="date_last_repaired" name="date_last_repaired" 
                                                           value="{{ old('date_last_repaired', $maintenance->date_last_repaired ? $maintenance->date_last_repaired->format('Y-m-d') : '') }}">
                                                    @error('date_last_repaired')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="works_done">Works Done</label>
                                            <textarea class="form-control @error('works_done') is-invalid @enderror" 
                                                      id="works_done" name="works_done" rows="3">{{ old('works_done', $maintenance->works_done) }}</textarea>
                                            @error('works_done')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="remarks">Remarks</label>
                                            <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                                      id="remarks" name="remarks" rows="3">{{ old('remarks', $maintenance->remarks) }}</textarea>
                                            @error('remarks')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <button type="reset" class="btn btn-secondary">
                                        <i class="fas fa-redo"></i> Reset
                                    </button>
                                    <div>
                                        <a href="{{ route('piped-water-schemes.show', $maintenance->id) }}" 
                                           class="btn btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update Maintenance
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-fill scheme details when scheme is selected
    document.getElementById('piped_water_scheme_id').addEventListener('change', function() {
        var schemeId = this.value;
        if (schemeId) {
            // You would typically fetch scheme details via AJAX
            // For now, we'll just show a message
            console.log('Scheme selected:', schemeId);
        }
    });

    // Initialize date pickers with max date as today
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('date_repaired').max = today;
    document.getElementById('date_last_repaired').max = today;
</script>
@endpush