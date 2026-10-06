@extends('layouts.app')
@section('title', 'Stock Categories')
@section('page-title', 'Consumables')
@section('content')
<div class="container-fluid"><div class="d-flex justify-content-between align-items-end mb-4"><div><p class="text-uppercase text-muted small fw-semibold mb-1">Catalog setup</p><h1 class="h3 mb-0">Stock categories</h1></div><a href="{{ route('consumables.categories.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>Add category</a></div><div class="table-responsive border rounded bg-white"><table class="table align-middle mb-0"><thead><tr><th>Category</th><th>Description</th><th>Items</th><th></th></tr></thead><tbody>@forelse($categories as $category)<tr><td>{{ $category->name }}</td><td>{{ $category->description ?: '—' }}</td><td>{{ $category->consumables_count }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('consumables.categories.edit', $category) }}">Edit</a></td></tr>@empty<tr><td colspan="4" class="text-center py-4 text-muted">No categories.</td></tr>@endforelse</tbody></table></div><div class="mt-3">{{ $categories->links() }}</div></div>
@endsection
