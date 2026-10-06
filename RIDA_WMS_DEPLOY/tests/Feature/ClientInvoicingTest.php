<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientInvoicingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_record_client_invoice_and_partial_payment_balance(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        foreach (range(1, 5) as $index) {
            $this->post(route('clients.store'), [
                'name' => $index === 1 ? 'North District Council' : "North District Council {$index}",
                'contact_person' => 'Tariro Moyo',
                'email' => "finance{$index}@example.test",
                'phone' => '+263 77 123 4567',
                'province' => 'Midlands',
                'district' => 'North District',
                'address' => 'Council offices',
            ])->assertRedirect()->assertSessionHasNoErrors();
        }

        $client = Client::firstOrFail();
        $this->assertDatabaseCount('clients', 5);
        foreach (range(1, 5) as $index) {
            $this->post(route('invoices.store'), [
                'client_id' => $client->id,
                'issue_date' => '2026-09-28',
                'due_date' => '2026-10-28',
                'currency' => 'USD',
                'tax_amount' => '10.00',
                'status' => 'issued',
                'items' => [
                    ['description' => 'Borehole drilling', 'quantity' => '2', 'unit_price' => '50.00'],
                    ['description' => 'Pump installation', 'quantity' => '1', 'unit_price' => '25.00'],
                ],
            ])->assertRedirect()->assertSessionHasNoErrors();
        }
        $this->assertDatabaseCount('invoices', 5);

        $invoice = Invoice::firstOrFail();
        $this->assertSame('135.00', $invoice->total);

        foreach (range(1, 5) as $index) {
            $this->post(route('invoices.payments.store', $invoice), [
                'payment_date' => '2026-09-28',
                'amount' => '8.00',
                'method' => 'bank_transfer',
                'reference' => "PAY-100{$index}",
            ])->assertRedirect(route('invoices.show', $invoice));
        }
        $this->assertDatabaseCount('invoice_payments', 5);

        $this->assertDatabaseHas('invoice_payments', [
            'invoice_id' => $invoice->id,
            'reference' => 'PAY-1001',
            'amount' => '8.00',
        ]);
        $this->assertSame('95.00', $invoice->fresh()->balance_due);
        $this->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertSee('Borehole drilling')
            ->assertSee('95.00');
        $this->get(route('clients.index'))
            ->assertOk()
            ->assertSee('North District Council')
            ->assertSee('635.00');
        $this->get(route('clients.show', $client))
            ->assertOk()
            ->assertSee('635.00');

        $this->from(route('invoices.show', $invoice))
            ->post(route('invoices.payments.store', $invoice), [
                'payment_date' => '2026-09-28',
                'amount' => '100.00',
                'method' => 'bank_transfer',
            ])
            ->assertSessionHasErrors('amount');
        $this->assertSame('40.00', $invoice->fresh()->amount_paid);
    }

    public function test_non_admin_cannot_access_client_or_invoice_records(): void
    {
        $this->actingAs(User::factory()->operator()->create());

        $this->get(route('clients.index'))->assertForbidden();
        $this->get(route('invoices.index'))->assertForbidden();
    }
}