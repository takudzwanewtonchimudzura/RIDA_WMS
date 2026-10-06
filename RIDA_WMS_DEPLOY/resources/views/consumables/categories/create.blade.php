@extends('layouts.app')
@section('title', 'New Stock Category')
@section('page-title', 'Consumables')
@section('content')
<div class="container-fluid"><div class="mb-4"><a href="{{ route('consumables.categories.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Categories</a><h1 class="h3 mt-2">New category</h1></div><form method="POST" action="{{ route('consumables.categories.store') }}" class="border rounded p-4 bg-white">@csrf<div class="mb-3"><label for="name" class="form-label">Category name</label><input id="name" name="name" value="{{ old('name') }}" class="form-control" required maxlength="255"></div><div class="mb-3"><label for="description" class="form-label">Description</label><textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea></div><div class="text-end"><button class="btn btn-primary" type="submit">Save category</button></div></form></div>
@endsection
