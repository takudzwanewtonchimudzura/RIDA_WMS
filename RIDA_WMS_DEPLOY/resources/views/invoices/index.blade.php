@extends('layouts.app')

@section('title', 'Invoices')
@section('page-title', 'Invoices')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><p class="text-uppercase text-muted small fw-semibold mb-1">Accounts receivable</p><h1 class="h3 mb-0">Invoices</h1></div><a href="{{ route('invoices.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>New invoice</a></div>
    <form method="GET" action="{{ route('invoices.index') }}" class="row g-2 mb-3">
        <div class="col-md-5"><label class="visually-hidden" for="invoice-search">Search invoices</label><input id="invoice-search" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Invoice number or client"></div>
        <div class="col-md-3"><label class="visually-hidden" for="invoice-status">Status</label><select id="invoice-status" name="status" class="form-select"><option value="">All statuses</option>@foreach(['draft', 'issued', 'partially_paid', 'paid', 'overdue', 'void'] as $status)<option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>@endforeach</select></div>
        <div class="col-auto"><button class="btn btn-outline-primary" type="submit"><i class="fas fa-filter me-1" aria-hidden="true"></i>Filter</button></div>
    </form>
    <div class="table-responsive border rounded bg-white"><table class="table table-hover align-middle mb-0"><thead><tr><th>Invoice</th><th>Client</th><th>Issued</th><th>Due</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Balance</th><th></th></tr></thead><tbody>
        @forelse($invoices as $invoice)
            @php($status = $invoice->payment_status)
            <tr><td class="fw-semibold">{{ $invoice->invoice_number }}</td><td>{{ $invoice->client->name }}</td><td>{{ $invoice->issue_date->format('d M Y') }}</td><td>{{ $invoice->due_date?->format('d M Y') ?: '—' }}</td><td><span class="badge text-bg-{{ in_array($status, ['paid'], true) ? 'success' : (in_array($status, ['overdue', 'void'], true) ? 'danger' : (in_array($status, ['draft', 'partially_paid'], true) ? 'warning' : 'primary')) }}">{{ str_replace('_', ' ', ucfirst($status)) }}</span></td><td class="text-end">{{ $invoice->currency }} {{ number_format((float) $invoice->total, 2) }}</td><td class="text-end">{{ $invoice->currency }} {{ number_format((float) $invoice->balance_due, 2) }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('invoices.show', $invoice) }}">Open</a></td></tr>
        @empty
            <tr><td colspan="8" class="text-center py-5 text-muted">No invoices match these filters.</td></tr>
        @endforelse
    </tbody></table></div>
    <div class="mt-3">{{ $invoices->links() }}</div>
</div>
@endsection