<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'client_id',
        'issue_date',
        'due_date',
        'currency',
        'subtotal',
        'tax_amount',
        'total',
        'amount_paid',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    public function getBalanceDueAttribute(): string
    {
        if ($this->status !== 'issued') {
            return '0.00';
        }

        return number_format(max(0, (float) $this->total - (float) $this->amount_paid), 2, '.', '');
    }

    public function getPaymentStatusAttribute(): string
    {
        if ($this->status !== 'issued') {
            return $this->status;
        }
        if ((float) $this->amount_paid >= (float) $this->total) {
            return 'paid';
        }
        if ((float) $this->amount_paid > 0) {
            return 'partially_paid';
        }
        if ($this->due_date && $this->due_date->isBefore(today())) {
            return 'overdue';
        }

        return 'issued';
    }
}