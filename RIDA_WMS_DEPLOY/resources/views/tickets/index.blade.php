@extends('layouts.app')

@section('title', 'Support Tickets')
@section('page-title', 'Ticketing')
@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><p class="text-uppercase text-muted small fw-semibold mb-1">Service desk</p><h1 class="h3 mb-0">{{ auth()->user()->isAdmin() ? 'Support tickets' : 'My tickets' }}</h1></div>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1" aria-hidden="true"></i>New ticket</a>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <form method="GET" action="{{ route('tickets.index') }}" class="row g-3 align-items-end mb-3">
        <div class="col-md-4"><label for="status" class="form-label">Status</label><select id="status" name="status" class="form-select"><option value="">All statuses</option>@foreach(['open', 'in_progress', 'resolved', 'closed'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>@endforeach</select></div>
        <div class="col-md-3 d-flex gap-2"><button type="submit" class="btn btn-outline-primary"><i class="fas fa-filter me-1" aria-hidden="true"></i>Filter</button><a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Clear</a></div>
    </form>
    <div class="table-responsive border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Ticket</th><th>Title</th><th>Requester</th><th>Priority</th><th>Status</th><th>Updated</th><th></th></tr></thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr><td class="fw-semibold">{{ $ticket->ticket_number }}</td><td>{{ $ticket->title }}<div class="small text-muted">{{ ucfirst($ticket->category) }}</div></td><td>{{ $ticket->requester->name }}</td><td><span class="badge text-bg-{{ ['low'=>'secondary','normal'=>'info','high'=>'warning','urgent'=>'danger'][$ticket->priority] }}">{{ ucfirst($ticket->priority) }}</span></td><td>{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</td><td>{{ $ticket->updated_at->format('d M Y H:i') }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('tickets.show', $ticket) }}">Open</a></td></tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5 text-muted">No tickets found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $tickets->links() }}</div>
</div>
@endsection