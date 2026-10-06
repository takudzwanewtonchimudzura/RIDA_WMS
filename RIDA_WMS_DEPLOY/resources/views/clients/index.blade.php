@extends('layouts.app')

@section('title', 'Clients')
@section('page-title', 'Clients')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><p class="text-uppercase text-muted small fw-semibold mb-1">Accounts receivable</p><h1 class="h3 mb-0">Clients</h1></div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>New client</a>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <form method="GET" action="{{ route('clients.index') }}" class="row g-2 mb-3">
        <div class="col-md-6"><label class="visually-hidden" for="client-search">Search clients</label><input id="client-search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Name, contact or district"></div>
        <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="fas fa-search me-1" aria-hidden="true"></i>Search</button></div>
    </form>
    <div class="table-responsive border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Client</th><th>Contact</th><th>Location</th><th>Invoices</th><th class="text-end">Outstanding</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td class="fw-semibold">{{ $client->name }}</td>
                        <td>{{ $client->contact_person ?: '—' }}<div class="small text-muted">{{ $client->email ?: $client->phone }}</div></td>
                        <td>{{ collect([$client->district, $client->province])->filter()->join(', ') ?: '—' }}</td>
                        <td>{{ $client->invoices_count }}</td>
                        <td class="text-end fw-semibold">@forelse($client->balances_by_currency as $currency => $balance){{ $currency }} {{ number_format($balance, 2) }}@if(!$loop->last)<br>@endif @empty—@endforelse</td>
                        <td><span class="badge text-bg-{{ $client->is_active ? 'success' : 'secondary' }}">{{ $client->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('clients.show', $client) }}">Open</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No client accounts match this search.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $clients->links() }}</div>
</div>
@endsection