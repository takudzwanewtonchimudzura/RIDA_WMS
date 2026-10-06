<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureAdmin($request);
        $filters = $request->validate([
            'search' => 'nullable|string|max:120',
            'status' => 'nullable|in:draft,issued,void,paid,partially_paid,overdue',
        ]);

        $invoices = Invoice::query()->with('client')
            ->when($filters['search'] ?? null, function ($query, $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('invoice_number', 'like', '%' . trim($term) . '%')
                        ->orWhereHas('client', fn ($clientQuery) => $clientQuery->where('name', 'like', '%' . trim($term) . '%'));
                });
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                if (in_array($status, ['draft', 'void'], true)) {
                    $query->where('status', $status);
                } elseif ($status === 'issued') {
                    $query->where('status', 'issued')->where('amount_paid', 0);
                } elseif ($status === 'paid') {
                    $query->where('status', 'issued')->whereColumn('amount_paid', '>=', 'total');
                } elseif ($status === 'partially_paid') {
                    $query->where('status', 'issued')->where('amount_paid', '>', 0)->whereColumn('amount_paid', '<', 'total');
                } else {
                    $query->where('status', 'issued')->where('amount_paid', '<', DB::raw('total'))
                        ->whereNotNull('due_date')->whereDate('due_date', '<', today());
                }
            })
            ->latest('issue_date')
            ->paginate(20)
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'filters'));
    }

    public function create(Request $request)
    {
        $this->ensureAdmin($request);
        $clients = Client::where('is_active', true)->orderBy('name')->get();

        return view('invoices.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'currency' => ['required', 'string', 'size:3', 'regex:/^[A-Za-z]{3}$/'],
            'tax_amount' => 'nullable|numeric|min:0|max:999999999999',
            'status' => 'required|in:draft,issued',
            'notes' => 'nullable|string|max:4000',
            'items' => 'required|array|min:1|max:50',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|gt:0|max:999999',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999999999',
        ]);

        if (!Client::whereKey($data['client_id'])->where('is_active', true)->exists()) {
            throw ValidationException::withMessages(['client_id' => 'Select an active client.']);
        }

        $invoice = DB::transaction(function () use ($data, $request) {
            $subtotal = 0;
            $items = [];
            foreach ($data['items'] as $item) {
                $amount = round((float) $item['quantity'] * (float) $item['unit_price'], 2);
                $subtotal = round($subtotal + $amount, 2);
                $items[] = $item + ['amount' => $amount];
            }
            $taxAmount = round((float) ($data['tax_amount'] ?? 0), 2);

            $invoice = Invoice::create([
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'client_id' => $data['client_id'],
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'] ?? null,
                'currency' => Str::upper($data['currency']),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total' => round($subtotal + $taxAmount, 2),
                'status' => $data['status'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);
            $invoice->items()->createMany($items);

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created.');
    }

    public function show(Request $request, Invoice $invoice)
    {
        $this->ensureAdmin($request);
        $invoice->load(['client', 'items', 'payments.recorder']);

        return view('invoices.show', compact('invoice'));
    }

    public function storePayment(Request $request, Invoice $invoice)
    {
        $this->ensureAdmin($request);
        $data = $request->validate([
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|gt:0|max:999999999999',
            'method' => 'required|in:cash,bank_transfer,card,mobile_money,cheque,other',
            'reference' => 'nullable|string|max:120|unique:invoice_payments,reference',
            'notes' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($invoice, $data, $request) {
            $lockedInvoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            $balance = max(0, round((float) $lockedInvoice->total - (float) $lockedInvoice->amount_paid, 2));

            if ($lockedInvoice->status !== 'issued') {
                throw ValidationException::withMessages(['amount' => 'Payments can only be recorded against issued invoices.']);
            }
            if ((float) $data['amount'] > $balance) {
                throw ValidationException::withMessages(['amount' => 'Payment cannot exceed the outstanding balance.']);
            }

            $lockedInvoice->payments()->create([
                'payment_date' => $data['payment_date'],
                'amount' => round((float) $data['amount'], 2),
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'recorded_by' => $request->user()->id,
            ]);
            $lockedInvoice->amount_paid = $lockedInvoice->payments()->sum('amount');
            $lockedInvoice->save();
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Payment recorded.');
    }

    public function issue(Request $request, Invoice $invoice)
    {
        $this->ensureAdmin($request);
        abort_unless($invoice->status === 'draft', 422, 'Only draft invoices can be issued.');
        $invoice->update(['status' => 'issued']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice issued.');
    }

    public function void(Request $request, Invoice $invoice)
    {
        $this->ensureAdmin($request);
        abort_unless($invoice->status !== 'void' && (float) $invoice->amount_paid === 0.0, 422, 'An invoice with payments cannot be voided.');
        $invoice->update(['status' => 'void']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice voided.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403);
    }
}