<?php

namespace Tests\Feature;

use App\Models\ProspectingSite;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProspectingAndTicketWorkflowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_prospecting_site_is_private_until_admin_decision_and_can_be_marked_drilled(): void
    {
        $operator = User::factory()->operator()->create();
        $this->actingAs($operator);

        foreach (range(1, 5) as $index) {
            $this->post(route('prospecting.store'), [
                'project_name' => "North Project {$index}",
                'village' => 'Chigo A',
                'ward' => '4',
                'district' => 'North District',
                'province' => 'Midlands Province',
                'latitude' => -19.2,
                'longitude' => 29.8,
                'survey_date' => '2026-09-20',
                'siting_agency' => 'PISP',
                'prospector_name' => $operator->name,
            ])->assertRedirect()->assertSessionHasNoErrors();
        }

        $site = ProspectingSite::firstOrFail();
        $this->assertDatabaseCount('prospecting_sites', 5);
        $this->assertSame('submitted', $site->status);
        $this->get(route('prospecting.show', $site))->assertOk();
        $this->get(route('prospecting.show', $site))->assertSee('Chigo A');

        $otherOperator = User::factory()->operator()->create();
        $this->actingAs($otherOperator)
            ->get(route('prospecting.show', $site))
            ->assertForbidden();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)
            ->patch(route('prospecting.decide', $site), [
                'status' => 'approved',
                'review_note' => 'Suitable for drilling.',
            ])
            ->assertRedirect(route('prospecting.show', $site));
        $this->assertDatabaseHas('prospecting_sites', [
            'id' => $site->id,
            'status' => 'approved',
            'reviewed_by' => $admin->id,
        ]);

        $this->actingAs($operator)
            ->get(route('boreholes.create', ['prospecting_site' => $site->id]))
            ->assertOk()
            ->assertSee('Starting from approved prospecting');

        $this->postJson(route('boreholes.store'), [
            'prospecting_site_id' => $site->id,
            'borehole_serial_no' => 'BH-FROM-PR-01',
            'province' => 'Midlands Province',
            'admin_district' => 'North District',
            'locality' => 'Chigo A',
            'village' => 'Chigo A',
            'ward' => '4',
            'date' => '2026-09-25',
            'project_name' => 'North Project',
        ])->assertCreated();

        $this->assertDatabaseHas('prospecting_sites', ['id' => $site->id, 'status' => 'drilled']);
        $this->assertDatabaseHas('boreholes', [
            'borehole_serial_no' => 'BH-FROM-PR-01',
            'prospecting_site_id' => $site->id,
        ]);
    }

    public function test_ticket_requesters_are_scoped_and_admin_can_resolve_and_assign(): void
    {
        $requester = User::factory()->operator()->create();
        $this->actingAs($requester);

        foreach (range(1, 5) as $index) {
            $this->post(route('tickets.store'), [
                'title' => "Unable to save field record {$index}",
                'category' => 'technical',
                'priority' => 'high',
                'description' => 'Submitting the form returns an error.',
            ])->assertRedirect()->assertSessionHasNoErrors();
        }

        $ticket = SupportTicket::firstOrFail();
        $this->assertDatabaseCount('support_tickets', 5);
        $this->assertSame('open', $ticket->status);
        $this->get(route('tickets.show', $ticket))->assertOk()->assertSee('Unable to save field record 1');

        $otherUser = User::factory()->viewer()->create();
        $this->actingAs($otherUser)
            ->get(route('tickets.show', $ticket))
            ->assertForbidden();

        $admin = User::factory()->admin()->create();
        $assignee = User::factory()->operator()->create();
        $this->actingAs($admin)
            ->patch(route('tickets.update', $ticket), [
                'status' => 'resolved',
                'priority' => 'high',
                'assignee_id' => $assignee->id,
                'resolution' => 'Configuration corrected.',
            ])
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('support_tickets', [
            'id' => $ticket->id,
            'status' => 'resolved',
            'assignee_id' => $assignee->id,
            'resolution' => 'Configuration corrected.',
        ]);
    }
}