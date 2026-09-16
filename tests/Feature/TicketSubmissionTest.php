<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_ticket_form_is_accessible_without_login(): void
    {
        $this->get(route('tickets.create'))
            ->assertOk()
            ->assertSee('Ticket beküldése')
            ->assertSee('pending');
    }

    public function test_guest_can_submit_a_ticket_with_pending_status(): void
    {
        $this->post(route('tickets.store'), [
            'name' => 'Kiss Anna',
            'email' => 'anna@example.com',
            'title' => 'Nem tölt be a főoldal',
            'description' => 'A főoldal üresen marad mobilnézetben.',
        ])
            ->assertRedirect(route('tickets.create'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('tickets', [
            'name' => 'Kiss Anna',
            'email' => 'anna@example.com',
            'title' => 'Nem tölt be a főoldal',
            'status' => TicketStatus::Pending->value,
        ]);
    }

    public function test_ticket_submission_requires_all_fields(): void
    {
        $this->from(route('tickets.create'))
            ->post(route('tickets.store'), [])
            ->assertRedirect(route('tickets.create'))
            ->assertSessionHasErrors(['name', 'email', 'title', 'description']);
    }

    public function test_guest_cannot_submit_a_custom_status(): void
    {
        $this->post(route('tickets.store'), [
            'name' => 'Kiss Anna',
            'email' => 'anna@example.com',
            'title' => 'Hiba',
            'description' => 'Leírás',
            'status' => TicketStatus::Done->value,
        ])->assertRedirect(route('tickets.create'));

        $this->assertDatabaseHas('tickets', [
            'email' => 'anna@example.com',
            'status' => TicketStatus::Pending->value,
        ]);
    }
}
