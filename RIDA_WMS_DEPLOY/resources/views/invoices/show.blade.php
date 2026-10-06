@extends('layouts.app')

@section('title', $invoice->invoice_number)
@section('page-title', 'Invoices')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
        <div><a href="{{ route('invoices.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Invoices</a><h1 class="h3 mt-2 mb-1">{{ $invoice->invoice_number }}</h1><span class="badge text-bg-{{ $invoice->payment_status === 'paid' ? 'success' : (in_array($invoice->payment_status, ['overdue', 'void'], true) ? 'danger' : 'primary') }}">{{ str_replace('_', ' ', ucfirst($invoice->payment_status)) }}</span></div>
        <div class="d-flex gap-2"><button type="button" class="btn btn-outline-secondary" onclick="window.print()" title="Print invoice"><i class="fas fa-print" aria-hidden="true"></i><span class="visually-hidden">Print invoice</span></button><a href="{{ route('clients.show', $invoice->client) }}" class="btn btn-outline-primary">Client account</a></div>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>@endif
    <div class="row g-3 mb-4">
        <div class="col-lg-8"><section class="border rounded p-4 h-100 bg-white"><div class="row g-3"><div class="col-sm-6"><div class="text-muted small">Bill to</div><div class="fw-semibold">{{ $invoice->client->name }}</div><div>{{ $invoice->client->contact_person }}</div><div>{{ $invoice->client->email }}</div><div>{{ $invoice->client->address }}</div></div><div class="col-sm-3"><div class="text-muted small">Issue date</div><div>{{ $invoice->issue_date->format('d M Y') }}</div></div><div class="col-sm-3"><div class="text-muted small">Due date</div><div>{{ $invoice->due_date?->format('d M Y') ?: '—' }}</div></div></div></section></div>
        <div class="col-lg-4"><section class="border rounded p-4 h-100 bg-white"><div class="text-muted small">Outstanding balance</div><div class="h3 mb-1">{{ $invoice->currency }} {{ number_format((float) $invoice->balance_due, 2) }}</div><div class="small text-muted">Total {{ $invoice->currency }} {{ number_format((float) $invoice->total, 2) }} · Paid {{ $invoice->currency }} {{ number_format((float) $invoice->amount_paid, 2) }}</div></section></div>
    </div>
    <div class="row g-3">
        <div class="col-xl-8">
            <section class="border rounded bg-white mb-3"><div class="p-3 border-bottom"><h2 class="h5 mb-0">Invoice details</h2></div><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Description</th><th class="text-end">Quantity</th><th class="text-end">Unit price</th><th class="text-end">Amount</th></tr></thead><tbody>
                @foreach($invoice->items as $item)<tr><td>{{ $item->description }}</td><td class="text-end">{{ number_format((float) $item->quantity, 3) }}</td><td class="text-end">{{ $invoice->currency }} {{ number_format((float) $item->unit_price, 2) }}</td><td class="text-end">{{ $invoice->currency }} {{ number_format((float) $item->amount, 2) }}</td></tr>@endforeach
            </tbody><tfoot><tr><th colspan="3" class="text-end">Subtotal</th><td class="text-end">{{ $invoice->currency }} {{ number_format((float) $invoice->subtotal, 2) }}</td></tr><tr><th colspan="3" class="text-end">Tax</th><td class="text-end">{{ $invoice->currency }} {{ number_format((float) $invoice->tax_amount, 2) }}</td></tr><tr><th colspan="3" class="text-end">Total</th><th class="text-end">{{ $invoice->currency }} {{ number_format((float) $invoice->total, 2) }}</th></tr></tfoot></table></div></section>
            @if($invoice->notes)<section class="border rounded p-3 bg-white"><h2 class="h6">Notes / terms</h2><p class="mb-0">{{ $invoice->notes }}</p></section>@endif
        </div>
        <div class="col-xl-4">
            @if($invoice->status === 'draft')<form method="POST" action="{{ route('invoices.issue', $invoice) }}" class="border rounded p-3 bg-white mb-3">@csrf @method('PATCH')<h2 class="h6">Issue invoice</h2><p class="small text-muted">Issuing makes this invoice payable and includes it in the client balance.</p><button class="btn btn-primary w-100" type="submit">Issue invoice</button></form>@endif
            @if($invoice->status === 'issued' && (float) $invoice->balance_due > 0)
                <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}" class="border rounded p-3 bg-white mb-3">@csrf<h2 class="h6">Record payment</h2><div class="mb-2"><label for="payment_date" class="form-label">Payment date</label><input id="payment_date" type="date" name="payment_date" value="{{ old('payment_date', today()->toDateString()) }}" class="form-control" required></div><div class="mb-2"><label for="amount" class="form-label">Amount ({{ $invoice->currency }})</label><input id="amount" type="number" name="amount" min="0.01" max="{{ $invoice->balance_due }}" step="0.01" value="{{ old('amount') }}" class="form-control" required></div><div class="mb-2"><label for="method" class="form-label">Method</label><select id="method" name="method" class="form-select" required>@foreach(['bank_transfer' => 'Bank transfer', 'cash' => 'Cash', 'card' => 'Card', 'mobile_money' => 'Mobile money', 'cheque' => 'Cheque', 'other' => 'Other'] as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div><div class="mb-3"><label for="reference" class="form-label">Reference</label><input id="reference" name="reference" value="{{ old('reference') }}" class="form-control"></div><button class="btn btn-success w-100" type="submit">Save payment</button></form>
            @endif
            @if($invoice->payments->isNotEmpty())
                <section class="border rounded bg-white"><div class="p-3 border-bottom"><h2 class="h6 mb-0">Payment history</h2></div><div class="list-group list-group-flush">@foreach($invoice->payments as $payment)<div class="list-group-item"><div class="d-flex justify-content-between"><strong>{{ $invoice->currency }} {{ number_format((float) $payment->amount, 2) }}</strong><span>{{ $payment->payment_date->format('d M Y') }}</span></div><div class="small text-muted">{{ str_replace('_', ' ', ucfirst($payment->method)) }}{{ $payment->reference ? ' · ' . $payment->reference : '' }}</div></div>@endforeach</div></section>
            @endif
            @if($invoice->status !== 'void' && (float) $invoice->amount_paid === 0.0)<form method="POST" action="{{ route('invoices.void', $invoice) }}" class="mt-3">@csrf @method('PATCH')<button class="btn btn-outline-danger btn-sm" type="submit">Void invoice</button></form>@endif
        </div>
    </div>
</div>
@endsection