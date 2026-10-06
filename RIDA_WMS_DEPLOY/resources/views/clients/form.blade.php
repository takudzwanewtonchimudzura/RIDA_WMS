@extends('layouts.app')

@php($editing = $client->exists)
@section('title', $editing ? 'Edit Client' : 'New Client')
@section('page-title', 'Clients')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><a href="{{ $editing ? route('clients.show', $client) : route('clients.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Clients</a><h1 class="h3 mt-2 mb-0">{{ $editing ? 'Edit client account' : 'New client account' }}</h1></div>
    <form method="POST" action="{{ $editing ? route('clients.update', $client) : route('clients.store') }}" class="border rounded p-4 bg-white">
        @csrf
        @if($editing)@method('PUT')@endif
        <div class="row g-3">
            <div class="col-md-6"><label for="name" class="form-label">Organisation / client name <span class="text-danger">*</span></label><input id="name" name="name" value="{{ old('name', $client->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="contact_person" class="form-label">Contact person</label><input id="contact_person" name="contact_person" value="{{ old('contact_person', $client->contact_person) }}" class="form-control"></div>
            <div class="col-md-6"><label for="email" class="form-label">Email</label><input id="email" type="email" name="email" value="{{ old('email', $client->email) }}" class="form-control @error('email') is-invalid @enderror">@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-6"><label for="phone" class="form-label">Telephone</label><input id="phone" name="phone" value="{{ old('phone', $client->phone) }}" class="form-control"></div>
            <div class="col-md-6"><label for="province" class="form-label">Province</label><input id="province" name="province" value="{{ old('province', $client->province) }}" class="form-control"></div>
            <div class="col-md-6"><label for="district" class="form-label">District</label><input id="district" name="district" value="{{ old('district', $client->district) }}" class="form-control"></div>
            <div class="col-12"><label for="address" class="form-label">Postal / physical address</label><textarea id="address" name="address" rows="3" class="form-control">{{ old('address', $client->address) }}</textarea></div>
            <div class="col-12"><label class="form-check-label"><input type="checkbox" name="is_active" value="1" class="form-check-input me-1" @checked(old('is_active', $client->exists ? $client->is_active : true))> Active client</label></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ $editing ? route('clients.show', $client) : route('clients.index') }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary" type="submit">{{ $editing ? 'Save changes' : 'Create client' }}</button></div>
    </form>
</div>
@endsection