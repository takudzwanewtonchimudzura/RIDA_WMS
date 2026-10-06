@extends('layouts.app')

@section('title', 'Edit Consumable')
@section('page-title', 'Consumables')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><a href="{{ route('consumables.inventory.show', $consumable) }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Stock item</a><h1 class="h3 mt-2 mb-0">Edit {{ $consumable->name }}</h1></div>
    <form method="POST" action="{{ route('consumables.inventory.update', $consumable) }}" class="border rounded p-4 bg-white">@csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-6"><label for="name" class="form-label">Item name</label><input id="name" name="name" value="{{ old('name', $consumable->name) }}" class="form-control" required></div>
            <div class="col-md-3"><label for="code" class="form-label">Stock code</label><input id="code" name="code" value="{{ old('code', $consumable->code) }}" class="form-control" required></div>
            <div class="col-md-3"><label for="category_id" class="form-label">Category</label><select id="category_id" name="category_id" class="form-select" required>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $consumable->category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label for="description" class="form-label">Description</label><textarea id="description" name="description" class="form-control">{{ old('description', $consumable->description) }}</textarea></div>
            <div class="col-md-2"><label for="unit" class="form-label">Unit</label><input id="unit" name="unit" value="{{ old('unit', $consumable->unit) }}" class="form-control" required></div>
            <div class="col-md-2"><label for="unit_cost" class="form-label">Unit cost</label><input id="unit_cost" name="unit_cost" type="number" min="0" step="0.01" value="{{ old('unit_cost', $consumable->unit_cost) }}" class="form-control" required></div>
            <div class="col-md-2"><label for="minimum_stock" class="form-label">Reorder level</label><input id="minimum_stock" name="minimum_stock" type="number" min="0" value="{{ old('minimum_stock', $consumable->minimum_stock) }}" class="form-control" required></div>
            <div class="col-md-3"><label for="status" class="form-label">Status</label><select id="status" name="status" class="form-select"><option value="active" @selected(old('status', $consumable->status) === 'active')>Active</option><option value="inactive" @selected(old('status', $consumable->status) === 'inactive')>Inactive</option></select></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('consumables.inventory.show', $consumable) }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary" type="submit">Save item</button></div>
    </form>
</div>
@endsection
