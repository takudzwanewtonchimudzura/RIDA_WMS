<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with(['requester', 'assignee'])->latest();
        if (!$request->user()->isAdmin()) {
            $query->where('requester_id', $request->user()->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $tickets = $query->paginate(20)->withQueryString();
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:technical,maintenance,access,data,other',
            'priority' => 'required|in:low,normal,high,urgent',
            'description' => 'required|string|max:10000',
        ]);
        $data['ticket_number'] = 'TK-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        $data['requester_id'] = Auth::id();
        $ticket = SupportTicket::create($data);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Support ticket submitted.');
    }

    public function show(Request $request, SupportTicket $ticket)
    {
        $this->authorizeTicket($request, $ticket);
        $ticket->load(['requester', 'assignee']);
        $users = $request->user()->isAdmin()
            ? User::where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('tickets.show', compact('ticket', 'users'));
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,normal,high,urgent',
            'assignee_id' => 'nullable|exists:users,id',
            'resolution' => 'nullable|string|max:10000',
        ]);
        $data['resolved_at'] = in_array($data['status'], ['resolved', 'closed'], true) ? now() : null;
        $ticket->update($data);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated.');
    }

    private function authorizeTicket(Request $request, SupportTicket $ticket): void
    {
        abort_unless($request->user()->isAdmin() || (int) $ticket->requester_id === (int) $request->user()->id, 403);
    }
}