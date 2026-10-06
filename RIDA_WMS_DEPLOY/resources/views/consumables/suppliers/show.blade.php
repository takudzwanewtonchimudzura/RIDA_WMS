@extends('layouts.app')
@section('title', $supplier->name)
@section('page-title', 'Consumables')
@section('content')
<div class="container-fluid"><div class="d-flex justify-content-between mb-4"><div><a href="{{ route('consumables.suppliers.index') }}" class="small text-muted text-decoration-none">Suppliers</a><h1 class="h3 mt-2">{{ $supplier->name }}</h1></div><a href="{{ route('consumables.suppliers.edit', $supplier) }}" class="btn btn-outline-primary">Edit</a></div><div class="border rounded p-4 bg-white"><dl class="row mb-0"><dt class="col-sm-3">Contact</dt><dd class="col-sm-9">{{ $supplier->contact_person }}</dd><dt class="col-sm-3">Email</dt><dd class="col-sm-9">{{ $supplier->email ?: '—' }}</dd><dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $supplier->phone ?: '—' }}</dd><dt class="col-sm-3">Address</dt><dd class="col-sm-9">{{ $supplier->address ?: '—' }}</dd><dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ ucfirst($supplier->status) }}</dd></dl></div></div>
@endsection
