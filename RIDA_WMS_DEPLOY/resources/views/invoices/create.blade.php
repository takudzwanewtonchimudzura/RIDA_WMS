@extends('layouts.app')

@section('title', 'New Invoice')
@section('page-title', 'Invoices')
@section('content')
<div class="container-fluid">
    <div class="mb-4"><a href="{{ route('invoices.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Invoices</a><h1 class="h3 mt-2 mb-0">Create invoice</h1></div>
    @if($clients->isEmpty())<div class="alert alert-info">Create an active client account before issuing an invoice. <a href="{{ route('clients.create') }}">Add client</a></div>@endif
    <form method="POST" action="{{ route('invoices.store') }}" class="border rounded p-4 bg-white">
        @csrf
        <div class="row g-3 mb-4">
            <div class="col-md-6"><label for="client_id" class="form-label">Client <span class="text-danger">*</span></label><select id="client_id" name="client_id" class="form-select @error('client_id') is-invalid @enderror" required><option value="">Choose a client</option>@foreach($clients as $client)<option value="{{ $client->id }}" @selected(old('client_id', request('client_id')) == $client->id)>{{ $client->name }}</option>@endforeach</select>@error('client_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label for="issue_date" class="form-label">Issue date</label><input id="issue_date" name="issue_date" type="date" value="{{ old('issue_date', today()->toDateString()) }}" class="form-control" required></div>
            <div class="col-md-3"><label for="due_date" class="form-label">Due date</label><input id="due_date" name="due_date" type="date" value="{{ old('due_date', today()->addDays(30)->toDateString()) }}" class="form-control">@error('due_date')<div class="text-danger small mt-1">{{ $message }}</div>@enderror</div>
            <div class="col-md-3"><label for="currency" class="form-label">Currency (ISO 4217)</label><input id="currency" name="currency" value="{{ old('currency', 'USD') }}" maxlength="3" pattern="[A-Za-z]{3}" class="form-control text-uppercase" required></div>
            <div class="col-md-3"><label for="tax_amount" class="form-label">Tax amount</label><input id="tax_amount" name="tax_amount" type="number" min="0" step="0.01" value="{{ old('tax_amount', '0.00') }}" class="form-control"></div>
            <div class="col-md-3"><label for="status" class="form-label">Initial status</label><select id="status" name="status" class="form-select"><option value="draft" @selected(old('status', 'draft') === 'draft')>Draft</option><option value="issued" @selected(old('status') === 'issued')>Issued</option></select></div>
            <div class="col-12"><label for="notes" class="form-label">Notes / payment terms</label><textarea id="notes" name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea></div>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h5 mb-0">Line items</h2><button type="button" id="add-invoice-item" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>Add line</button></div>
        <div class="table-responsive"><table class="table align-middle"><thead><tr><th>Description</th><th style="width: 150px">Quantity</th><th style="width: 190px">Unit price</th><th style="width: 50px"></th></tr></thead><tbody id="invoice-items">
            @foreach(old('items', [['description' => '', 'quantity' => '1', 'unit_price' => '']]) as $index => $item)
                <tr data-invoice-item><td><input name="items[{{ $index }}][description]" value="{{ $item['description'] ?? '' }}" class="form-control" maxlength="255" placeholder="Service or item" required></td><td><input name="items[{{ $index }}][quantity]" value="{{ $item['quantity'] ?? '1' }}" type="number" min="0.001" step="0.001" class="form-control" required></td><td><input name="items[{{ $index }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" type="number" min="0" step="0.01" class="form-control" required></td><td><button type="button" class="btn btn-sm btn-outline-danger remove-invoice-item" aria-label="Remove line"><i class="fas fa-trash" aria-hidden="true"></i></button></td></tr>
            @endforeach
        </tbody></table></div>
        @error('items')<div class="text-danger small">{{ $message }}</div>@enderror
        <div class="d-flex justify-content-end gap-2 mt-4"><a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Cancel</a><button class="btn btn-primary" type="submit" @disabled($clients->isEmpty())>Create invoice</button></div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const invoiceItems = document.getElementById('invoice-items');
    document.getElementById('add-invoice-item')?.addEventListener('click', () => {
        const index = Date.now();
        invoiceItems.insertAdjacentHTML('beforeend', `<tr data-invoice-item><td><input name="items[${index}][description]" class="form-control" maxlength="255" placeholder="Service or item" required></td><td><input name="items[${index}][quantity]" type="number" min="0.001" step="0.001" value="1" class="form-control" required></td><td><input name="items[${index}][unit_price]" type="number" min="0" step="0.01" class="form-control" required></td><td><button type="button" class="btn btn-sm btn-outline-danger remove-invoice-item" aria-label="Remove line"><i class="fas fa-trash" aria-hidden="true"></i></button></td></tr>`);
    });
    invoiceItems?.addEventListener('click', (event) => {
        if (event.target.closest('.remove-invoice-item') && invoiceItems.querySelectorAll('[data-invoice-item]').length > 1) {
            event.target.closest('[data-invoice-item]').remove();
        }
    });
</script>
@endpush