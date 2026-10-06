@extends('layouts.app')

@section('title', 'Consumables Inventory')
@section('page-title', 'Consumables')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><p class="text-uppercase text-muted small fw-semibold mb-1">Stores</p><h1 class="h3 mb-0">Consumables inventory</h1></div>
        @if(auth()->user()->isAdmin())<a href="{{ route('consumables.inventory.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>Add stock item</a>@endif
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger" role="alert">{{ session('error') }}</div>@endif
    <form method="GET" action="{{ route('consumables.inventory.index') }}" class="row g-2 mb-3">
        <div class="col-md-5"><label for="search" class="visually-hidden">Search stock</label><input id="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Item name or code"></div>
        <div class="col-md-3"><label for="category" class="visually-hidden">Category</label><select id="category" name="category" class="form-select"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div class="col-auto"><label class="form-check-label mt-2"><input type="checkbox" name="low_stock" value="1" class="form-check-input me-1" @checked(request()->has('low_stock'))>Low stock</label></div>
        <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="fas fa-filter me-1" aria-hidden="true"></i>Filter</button></div>
    </form>
    <div class="table-responsive border rounded bg-white"><table class="table table-hover align-middle mb-0">
        <thead><tr><th>Item</th><th>Code</th><th>Category</th><th class="text-end">On hand</th><th>Minimum</th><th>Status</th><th></th></tr></thead>
        <tbody>@forelse($consumables as $consumable)
            <tr><td class="fw-semibold">{{ $consumable->name }}</td><td>{{ $consumable->code }}</td><td>{{ $consumable->category->name }}</td><td class="text-end">{{ $consumable->current_stock }} {{ $consumable->unit }} @if($consumable->is_low_stock)<span class="badge text-bg-warning">Low</span>@endif</td><td>{{ $consumable->minimum_stock }} {{ $consumable->unit }}</td><td>{{ ucfirst($consumable->status) }}</td><td class="text-end"><a href="{{ route('consumables.inventory.show', $consumable) }}" class="btn btn-sm btn-outline-primary">Open</a></td></tr>
        @empty<tr><td colspan="7" class="text-center py-5 text-muted">No stock items match these filters.</td></tr>@endforelse</tbody>
    </table></div>
    <div class="mt-3">{{ $consumables->links() }}</div>
</div>
@endsection
