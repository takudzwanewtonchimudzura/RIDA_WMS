@php
    $wp = $waterPoint ?? null;
@endphp

<div class="row mb-4">
    <div class="col-md-12">
        <h5><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
        <hr>
    </div>

    <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Water Point Name *</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror"
               id="name" name="name" value="{{ old('name', $wp->name ?? '') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="village_name" class="form-label">Village Name *</label>
        <input type="text" class="form-control @error('village_name') is-invalid @enderror"
               id="village_name" name="village_name" value="{{ old('village_name', $wp->village_name ?? '') }}" required>
        @error('village_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="ward" class="form-label">Ward *</label>
        <input type="text" class="form-control @error('ward') is-invalid @enderror"
               id="ward" name="ward" value="{{ old('ward', $wp->ward ?? '') }}" required>
        @error('ward')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="latitude" class="form-label">Latitude</label>
        <input type="number" step="0.000001" class="form-control @error('latitude') is-invalid @enderror"
               id="latitude" name="latitude" value="{{ old('latitude', $wp->latitude ?? '') }}">
        @error('latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="longitude" class="form-label">Longitude</label>
        <input type="number" step="0.000001" class="form-control @error('longitude') is-invalid @enderror"
               id="longitude" name="longitude" value="{{ old('longitude', $wp->longitude ?? '') }}">
        @error('longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h5><i class="fas fa-cogs me-2"></i>Technical Details</h5>
        <hr>
    </div>

    @php $sourceOld = old('type_of_water_source', $wp->type_of_water_source ?? ''); @endphp
    <div class="col-md-3 mb-3">
        <label for="type_of_water_source" class="form-label">Water Source Type *</label>
        <select class="form-select @error('type_of_water_source') is-invalid @enderror" id="type_of_water_source" name="type_of_water_source" required>
            <option value="">Select Type</option>
            @foreach(['Borehole', 'Well', 'Spring', 'River'] as $option)
                <option value="{{ $option }}" {{ $sourceOld == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        @error('type_of_water_source')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @php $functionalityOld = old('functionality', $wp->functionality ?? ''); @endphp
    <div class="col-md-3 mb-3">
        <label for="functionality" class="form-label">Functionality Status *</label>
        <select class="form-select @error('functionality') is-invalid @enderror" id="functionality" name="functionality" required>
            <option value="">Select Status</option>
            @foreach(['Functional', 'Partially Functional', 'Nonfunctional'] as $option)
                <option value="{{ $option }}" {{ $functionalityOld == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        @error('functionality')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="year_drilled" class="form-label">Year Drilled</label>
        <input type="number" class="form-control @error('year_drilled') is-invalid @enderror"
               id="year_drilled" name="year_drilled" value="{{ old('year_drilled', $wp->year_drilled ?? '') }}"
               min="1900" max="{{ date('Y') }}">
        @error('year_drilled')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @php $seasonOld = old('seasonality', $wp->seasonality ?? ''); @endphp
    <div class="col-md-3 mb-3">
        <label for="seasonality" class="form-label">Seasonality</label>
        <select class="form-select @error('seasonality') is-invalid @enderror" id="seasonality" name="seasonality">
            <option value="">Select</option>
            @foreach(['Year-round', 'Seasonal', 'Dry season only'] as $option)
                <option value="{{ $option }}" {{ $seasonOld == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        @error('seasonality')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @php $liftOld = old('type_of_lifting_device', $wp->type_of_lifting_device ?? ''); @endphp
    <div class="col-md-3 mb-3">
        <label for="type_of_lifting_device" class="form-label">Type of Lifting Device</label>
        <select class="form-select @error('type_of_lifting_device') is-invalid @enderror" id="type_of_lifting_device" name="type_of_lifting_device">
            <option value="">Select</option>
            @foreach(['Hand Pump', 'Solar Pump', 'Electric Pump', 'Gravity'] as $option)
                <option value="{{ $option }}" {{ $liftOld == $option ? 'selected' : '' }}>{{ $option }}</option>
            @endforeach
        </select>
        @error('type_of_lifting_device')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="number_of_pipes" class="form-label">Number of Pipes</label>
        <input type="number" class="form-control @error('number_of_pipes') is-invalid @enderror"
               id="number_of_pipes" name="number_of_pipes" value="{{ old('number_of_pipes', $wp->number_of_pipes ?? '') }}" min="0">
        @error('number_of_pipes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="strokes_before_water" class="form-label">Strokes Before Water</label>
        <input type="number" class="form-control @error('strokes_before_water') is-invalid @enderror"
               id="strokes_before_water" name="strokes_before_water" value="{{ old('strokes_before_water', $wp->strokes_before_water ?? '') }}" min="0">
        @error('strokes_before_water')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="strokes_to_fill_20l" class="form-label">Strokes to Fill 20L</label>
        <input type="number" class="form-control @error('strokes_to_fill_20l') is-invalid @enderror"
               id="strokes_to_fill_20l" name="strokes_to_fill_20l" value="{{ old('strokes_to_fill_20l', $wp->strokes_to_fill_20l ?? '') }}" min="0">
        @error('strokes_to_fill_20l')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h5><i class="fas fa-tools me-2"></i>Infrastructure Condition</h5>
        <hr>
    </div>

    @foreach(['apron_condition' => 'Apron Condition', 'runoff_channel_condition' => 'Runoff Channel Condition', 'soakaway_condition' => 'Soakaway Condition'] as $field => $label
    )
        @php $condOld = old($field, $wp->$field ?? ''); @endphp
        <div class="col-md-4 mb-3">
            <label for="{{ $field }}" class="form-label">{{ $label }}</label>
            <select class="form-select @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}">
                <option value="">Select</option>
                @foreach(['good', 'bad', 'needs attention'] as $option)
                    <option value="{{ $option }}" {{ $condOld == $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                @endforeach
            </select>
            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h5><i class="fas fa-users me-2"></i>Service Information</h5>
        <hr>
    </div>

    @foreach([
        'population_served' => 'Total Population Served',
        'households_served' => 'Households Served',
        'women_served' => 'Women Served',
        'men_served' => 'Men Served',
        'children_below_18' => 'Children Below 18',
    ] as $field => $label)
        <div class="col-md-3 mb-3">
            <label for="{{ $field }}" class="form-label">{{ $label }}</label>
            <input type="number" class="form-control @error($field) is-invalid @enderror"
                   id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $wp->$field ?? 0) }}" min="0">
            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h5><i class="fas fa-people-group me-2"></i>Water Committee</h5>
        <hr>
    </div>

    <div class="col-md-3 mb-3 form-check ps-4 pt-4">
        <input type="hidden" name="has_water_committee" value="0">
        <input type="checkbox" class="form-check-input" id="has_water_committee" name="has_water_committee" value="1"
               {{ old('has_water_committee', $wp->has_water_committee ?? false) ? 'checked' : '' }}>
        <label for="has_water_committee" class="form-check-label">Has Water Committee</label>
    </div>

    <div class="col-md-3 mb-3 form-check ps-4 pt-4">
        <input type="hidden" name="committee_functional" value="0">
        <input type="checkbox" class="form-check-input" id="committee_functional" name="committee_functional" value="1"
               {{ old('committee_functional', $wp->committee_functional ?? false) ? 'checked' : '' }}>
        <label for="committee_functional" class="form-check-label">Committee Functional</label>
    </div>

    @foreach([
        'committee_total_members' => 'Total Members',
        'committee_women' => 'Women',
        'committee_men' => 'Men',
    ] as $field => $label)
        <div class="col-md-2 mb-3">
            <label for="{{ $field }}" class="form-label">{{ $label }}</label>
            <input type="number" class="form-control @error($field) is-invalid @enderror"
                   id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $wp->$field ?? '') }}" min="0">
            @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <h5><i class="fas fa-wrench me-2"></i>Maintenance History</h5>
        <hr>
    </div>

    <div class="col-md-4 mb-3">
        <label for="last_repaired_by" class="form-label">Last Repaired By</label>
        <input type="text" class="form-control @error('last_repaired_by') is-invalid @enderror"
               id="last_repaired_by" name="last_repaired_by" value="{{ old('last_repaired_by', $wp->last_repaired_by ?? '') }}">
        @error('last_repaired_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="last_spares_fitted" class="form-label">Last Spares Fitted</label>
        <input type="text" class="form-control @error('last_spares_fitted') is-invalid @enderror"
               id="last_spares_fitted" name="last_spares_fitted" value="{{ old('last_spares_fitted', $wp->last_spares_fitted ?? '') }}">
        @error('last_spares_fitted')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="last_repair_date" class="form-label">Last Repair Date</label>
        <input type="date" class="form-control @error('last_repair_date') is-invalid @enderror"
               id="last_repair_date" name="last_repair_date"
               value="{{ old('last_repair_date', optional($wp->last_repair_date ?? null)->format('Y-m-d')) }}">
        @error('last_repair_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
