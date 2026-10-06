@extends('layouts.app')

@section('title', $client->name)
@section('page-title', 'Clients')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><a href="{{ route('clients.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Clients</a><h1 class="h3 mt-2 mb-1">{{ $client->name }}</h1><span class="badge text-bg-{{ $client->is_active ? 'success' : 'secondary' }}">{{ $client->is_active ? 'Active' : 'Inactive' }}</span></div>
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-outline-primary"><i class="fas fa-pen me-1" aria-hidden="true"></i>Edit client</a>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <div class="row g-3 mb-4">
        <div class="col-xl-4"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small">Contact</div><div class="fw-semibold">{{ $client->contact_person ?: '—' }}</div><div>{{ $client->email ?: '—' }}</div><div>{{ $client->phone ?: '—' }}</div></div></div>
        <div class="col-xl-4"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small">Location</div><div>{{ collect([$client->district, $client->province])->filter()->join(', ') ?: '—' }}</div><div class="text-muted mt-1">{{ $client->address ?: 'No address recorded' }}</div></div></div>
        <div class="col-xl-4"><div class="border rounded p-3 h-100 bg-white"><div class="text-muted small">Outstanding balance</div>@forelse($outstandingBalances as $currency => $balance)<div class="h5 mb-0">{{ $currency }} {{ number_format($balance, 2) }}</div>@empty<div class="h5 mb-0">—</div>@endforelse</div></div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h5 mb-0">Invoices</h2><a class="btn btn-sm btn-primary" href="{{ route('invoices.create', ['client_id' => $client->id]) }}"><i class="fas fa-plus me-1" aria-hidden="true"></i>New invoice</a></div>
    <div class="table-responsive border rounded bg-white">
        <table class="table table-hover align-middle mb-0"><thead><tr><th>Invoice</th><th>Issue date</th><th>Due date</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Balance</th><th></th></tr></thead><tbody>
            @forelse($client->invoices as $invoice)
                <tr><td class="fw-semibold">{{ $invoice->invoice_number }}</td><td>{{ $invoice->issue_date->format('d M Y') }}</td><td>{{ $invoice->due_date?->format('d M Y') ?: '—' }}</td><td>{{ str_replace('_', ' ', ucfirst($invoice->payment_status)) }}</td><td class="text-end">{{ $invoice->currency }} {{ number_format((float) $invoice->total, 2) }}</td><td class="text-end">{{ $invoice->currency }} {{ number_format((float) $invoice->balance_due, 2) }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('invoices.show', $invoice) }}">Open</a></td></tr>
            @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No invoices have been recorded for this client.</td></tr>
            @endforelse
        </tbody></table>
    </div>
</div>
@endsection