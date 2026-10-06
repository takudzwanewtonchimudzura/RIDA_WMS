<div class="filter-section">
    <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Report Filters</h5>
    <form method="GET" action="{{ request()->url() }}" id="reportFilters">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Province</label>
                <select class="form-select" name="province">
                    <option value="">All Provinces</option>
                    @foreach($provinces ?? [] as $province)
                        <option value="{{ $province }}" {{ request('province') == $province ? 'selected' : '' }}>
                            {{ $province }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Funding Agency</label>
                <select class="form-select" name="funding_agency">
                    <option value="">All Agencies</option>
                    @foreach($agencies ?? [] as $agency)
                        <option value="{{ $agency }}" {{ request('funding_agency') == $agency ? 'selected' : '' }}>
                            {{ $agency }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="report-district">District</label>
                <input id="report-district" class="form-control" name="district" value="{{ request('district') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="report-village">Village</label>
                <input id="report-village" class="form-control" name="village" value="{{ request('village') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label" for="report-siting-agency">Siting agency</label>
                <select id="report-siting-agency" class="form-select" name="siting_agency">
                    <option value="">All agencies</option>
                    <option value="Government" @selected(request('siting_agency') === 'Government')>Government</option>
                    <option value="PISP" @selected(request('siting_agency') === 'PISP')>PISP</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Contractor</label>
                <select class="form-select" name="contractor">
                    <option value="">All Contractors</option>
                    @foreach($contractors ?? [] as $contractor)
                        <option value="{{ $contractor }}" {{ request('contractor') == $contractor ? 'selected' : '' }}>
                            {{ $contractor }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-12">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Apply Filters
                    </button>
                    <a href="{{ request()->url() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>Reset
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>