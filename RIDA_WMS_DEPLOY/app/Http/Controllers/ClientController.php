<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdmin($request);
        $filters = $request->validate(['search' => 'nullable|string|max:120']);

        $clients = Client::query()
            ->withCount('invoices')
            ->with(['invoices' => fn ($query) => $query->where('status', 'issued')
                ->select('id', 'client_id', 'currency', 'total', 'amount_paid')])
            ->when($filters['search'] ?? null, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('name', 'like', '%' . trim($term) . '%')
                        ->orWhere('contact_person', 'like', '%' . trim($term) . '%')
                        ->orWhere('district', 'like', '%' . trim($term) . '%');
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $clients->getCollection()->each(function (Client $client) {
            $client->balances_by_currency = $client->invoices
                ->groupBy('currency')
                ->map(fn ($invoices) => round($invoices->sum(fn ($invoice) => max(0, (float) $invoice->total - (float) $invoice->amount_paid)), 2));
        });

        return view('clients.index', compact('clients', 'filters'));
    }

    public function create(Request $request)
    {
        $this->ensureAdmin($request);
        return view('clients.form', ['client' => new Client()]);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);
        $data = $this->validatedClient($request);
        $data['created_by'] = $request->user()->id;
        $data['is_active'] = $data['is_active'] ?? true;

        $client = Client::create($data);

        return redirect()->route('clients.show', $client)->with('success', 'Client account created.');
    }

    public function show(Request $request, Client $client)
    {
        $this->ensureAdmin($request);
        $client->load(['invoices' => fn ($query) => $query->latest(), 'invoices.payments']);
        $outstandingBalances = $client->invoices
            ->where('status', 'issued')
            ->groupBy('currency')
            ->map(fn ($invoices) => round($invoices->sum(fn ($invoice) => (float) $invoice->balance_due), 2));

        return view('clients.show', compact('client', 'outstandingBalances'));
    }

    public function edit(Request $request, Client $client)
    {
        $this->ensureAdmin($request);
        return view('clients.form', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->ensureAdmin($request);
        $data = $this->validatedClient($request);
        $data['is_active'] = $data['is_active'] ?? false;
        $client->update($data);

        return redirect()->route('clients.show', $client)->with('success', 'Client account updated.');
    }

    private function validatedClient(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:60',
            'address' => 'nullable|string|max:2000',
            'province' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403);
    }
}