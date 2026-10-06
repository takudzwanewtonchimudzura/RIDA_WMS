@extends('layouts.app')

@section('title', $ticket->ticket_number)
@section('page-title', 'Ticketing')
@section('content')
<div class="container-fluid" style="max-width: 1050px">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><a href="{{ route('tickets.index') }}" class="small text-muted text-decoration-none"><i class="fas fa-arrow-left me-1" aria-hidden="true"></i>Tickets</a><h1 class="h3 mt-2 mb-0">{{ $ticket->ticket_number }} · {{ $ticket->title }}</h1></div>
        <span class="badge text-bg-{{ ['open'=>'primary','in_progress'=>'warning','resolved'=>'success','closed'=>'secondary'][$ticket->status] }} fs-6">{{ str_replace('_', ' ', ucfirst($ticket->status)) }}</span>
    </div>
    @if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
    <div class="border rounded p-4 mb-4">
        <div class="row g-3">
            <div class="col-md-3"><div class="small text-muted">Category</div><div>{{ ucfirst($ticket->category) }}</div></div>
            <div class="col-md-3"><div class="small text-muted">Priority</div><div>{{ ucfirst($ticket->priority) }}</div></div>
            <div class="col-md-3"><div class="small text-muted">Requester</div><div>{{ $ticket->requester->name }}</div></div>
            <div class="col-md-3"><div class="small text-muted">Assigned to</div><div>{{ $ticket->assignee->name ?? 'Unassigned' }}</div></div>
            <div class="col-12"><div class="small text-muted">Description</div><div class="mt-1" style="white-space: pre-wrap">{{ $ticket->description }}</div></div>
            @if($ticket->resolution)<div class="col-12"><div class="small text-muted">Resolution</div><div class="mt-1" style="white-space: pre-wrap">{{ $ticket->resolution }}</div></div>@endif
        </div>
    </div>
    @if(auth()->user()->isAdmin())
        <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="border rounded p-4">
            @csrf @method('PATCH')
            <h2 class="h5">Update ticket</h2>
            <div class="row g-3">
                <div class="col-md-4"><label for="status" class="form-label">Status</label><select id="status" name="status" class="form-select">@foreach(['open','in_progress','resolved','closed'] as $status)<option value="{{ $status }}" @selected($ticket->status === $status)>{{ str_replace('_',' ',ucfirst($status)) }}</option>@endforeach</select></div>
                <div class="col-md-4"><label for="priority" class="form-label">Priority</label><select id="priority" name="priority" class="form-select">@foreach(['low','normal','high','urgent'] as $priority)<option value="{{ $priority }}" @selected($ticket->priority === $priority)>{{ ucfirst($priority) }}</option>@endforeach</select></div>
                <div class="col-md-4"><label for="assignee_id" class="form-label">Assignee</label><select id="assignee_id" name="assignee_id" class="form-select"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected($ticket->assignee_id === $user->id)>{{ $user->name }}</option>@endforeach</select></div>
                <div class="col-12"><label for="resolution" class="form-label">Resolution / internal note</label><textarea id="resolution" name="resolution" rows="4" class="form-control">{{ old('resolution', $ticket->resolution) }}</textarea></div>
            </div>
            <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1" aria-hidden="true"></i>Save update</button>
        </form>
    @endif
</div>
@endsection