@extends('layouts.app')

@section('title', 'Add Consumable')
@section('page-title', 'Consumables')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><a href="{{ route('consumables.inventory.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Inventory</a><h1 class="h3 mt-2 mb-0">Add stock item</h1></div>
    @if($categories->isEmpty())<div class="alert alert-info">Create an inventory category before adding stock. <a href="{{ route('consumables.categories.create') }}">Add category</a></div>@endif
    <form method="POST" action="{{ route('consumables.inventory.store') }}" class="border rounded p-4 bg-white">@csrf
        <div class="row g-3">
            <div class="col-md-6"><label for="name" class="form-label">Item name <span class="text-danger">*</span></label><input id="name" name="name" value="{{ old('name') }}" class="form-control" required maxlength="255">@error('name')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label for="code" class="form-label">Stock code <span class="text-danger">*</span></label><input id="code" name="code" value="{{ old('code') }}" class="form-control" required maxlength="255">@error('code')<div class="text-danger small">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label for="category_id" class="form-label">Category <span class="text-danger">*</span></label><select id="category_id" name="category_id" class="form-select" required><option value="">Choose category</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <div class="col-md-6"><label for="description" class="form-label">Description</label><textarea id="description" name="description" rows="2" class="form-control">{{ old('description') }}</textarea></div>
            <div class="col-md-2"><label for="unit" class="form-label">Unit</label><input id="unit" name="unit" value="{{ old('unit', 'pieces') }}" class="form-control" required maxlength="255"></div>
            <div class="col-md-2"><label for="unit_cost" class="form-label">Unit cost</label><input id="unit_cost" name="unit_cost" type="number" min="0" step="0.01" value="{{ old('unit_cost', '0.00') }}" class="form-control" required></div>
            <div class="col-md-2"><label for="minimum_stock" class="form-label">Reorder level</label><input id="minimum_stock" name="minimum_stock" type="number" min="0" value="{{ old('minimum_stock', '0') }}" class="form-control" required></div>
            <div class="col-md-3"><label for="initial_quantity" class="form-label">Opening quantity</label><input id="initial_quantity" name="initial_quantity" type="number" min="0" value="{{ old('initial_quantity', '0') }}" class="form-control" required></div>
            <div class="col-md-3"><label for="location" class="form-label">Store location</label><input id="location" name="location" value="{{ old('location') }}" class="form-control"></div>
            <div class="col-md-3"><label for="batch_number" class="form-label">Batch number</label><input id="batch_number" name="batch_number" value="{{ old('batch_number') }}" class="form-control"></div>
            <div class="col-md-3"><label for="expiry_date" class="form-label">Expiry date</label><input id="expiry_date" name="expiry_date" type="date" value="{{ old('expiry_date') }}" class="form-control"></div>
            <div class="col-md-6"><label for="supplier_id" class="form-label">Supplier</label><select id="supplier_id" name="supplier_id" class="form-select"><option value="">Not specified</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>@endforeach</select></div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('consumables.inventory.index') }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary" type="submit" @disabled($categories->isEmpty())>Create stock item</button></div>
    </form>
</div>
@endsection
