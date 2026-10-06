@extends('layouts.app')

@section('title', 'New Support Ticket')
@section('page-title', 'Ticketing')
@section('content')
<div class="container-fluid" style="max-width: 900px">
    <div class="mb-4"><a href="{{ route('tickets.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Tickets</a><h1 class="h3 mt-2">New support ticket</h1></div>
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('tickets.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-md-8"><label for="title" class="form-label">Subject *</label><input id="title" name="title" value="{{ old('title') }}" required class="form-control" maxlength="255"></div>
            <div class="col-md-4"><label for="category" class="form-label">Category *</label><select id="category" name="category" class="form-select" required>@foreach(['technical'=>'Technical','maintenance'=>'Maintenance','access'=>'Account access','data'=>'Data issue','other'=>'Other'] as $value=>$label)<option value="{{ $value }}" @selected(old('category','other') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-4"><label for="priority" class="form-label">Priority *</label><select id="priority" name="priority" class="form-select" required>@foreach(['low'=>'Low','normal'=>'Normal','high'=>'High','urgent'=>'Urgent'] as $value=>$label)<option value="{{ $value }}" @selected(old('priority','normal') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-12"><label for="description" class="form-label">Description *</label><textarea id="description" name="description" rows="7" required class="form-control">{{ old('description') }}</textarea></div>
        </div>
        <div class="d-flex justify-content-between mt-4"><a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Cancel</a><button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1" aria-hidden="true"></i>Submit ticket</button></div>
    </form>
</div>
@endsection