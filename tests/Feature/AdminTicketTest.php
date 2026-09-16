<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_area(): void
    {
        $this->get(route('admin.tickets.index'))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_log_in_and_see_tickets(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@example.com',
        ]);

        $ticket = Ticket::factory()->create([
            'name' => 'Nagy Péter',
            'email' => 'peter@example.com',
            'description' => 'A fizetés gomb nem működik.',
        ]);

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.tickets.index'));

        $this->actingAs($admin)
            ->get(route('admin.tickets.index'))
            ->assertOk()
            ->assertSee('Nagy Péter')
            ->assertSee('peter@example.com')
            ->assertSee('A fizetés gomb nem működik.')
            ->assertSee('pending')
            ->assertSee($ticket->created_at->format('Y. m. d. H:i'));
    }

    public function test_admin_can_update_ticket_status(): void
    {
        $admin = User::factory()->admin()->create();
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::Pending,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.tickets.update', $ticket), [
                'status' => TicketStatus::InProgress->value,
            ])
            ->assertRedirect(route('admin.tickets.index'));

        $this->assertSame(TicketStatus::InProgress, $ticket->fresh()->status);
    }

    public function test_non_admin_cannot_access_admin_area(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.tickets.index'))
            ->assertForbidden();
    }
}
