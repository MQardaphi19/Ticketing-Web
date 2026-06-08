<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketResolvedNotification;
use App\Notifications\TicketStatusChangedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TicketResolvedEmailNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolved_notification_is_sent_when_ticket_status_changes_to_resolved(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $ticket = $this->createTicket($owner, ['status' => 'in_progress']);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'status' => 'resolved',
        ]);

        $response->assertRedirect();
        $this->assertSame('resolved', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->resolved_at);

        Notification::assertSentTo($owner, TicketResolvedNotification::class);
    }

    public function test_resolved_notification_is_not_sent_again_when_ticket_is_already_resolved(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $ticket = $this->createTicket($owner, [
            'status' => 'resolved',
            'resolved_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'status' => 'resolved',
        ]);

        $response->assertRedirect();

        Notification::assertNothingSent();
    }

    public function test_resolved_notification_is_not_sent_when_owner_is_not_pegawai_dinas(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pemohon']);
        $ticket = $this->createTicket($owner, ['status' => 'open']);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'status' => 'resolved',
        ]);

        $response->assertRedirect();

        Notification::assertNothingSent();
    }

    public function test_bulk_resolved_status_sends_notification_for_each_eligible_ticket(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $firstOwner = User::factory()->create(['role' => 'pegawai-dinas']);
        $secondOwner = User::factory()->create(['role' => 'pegawai-dinas']);
        $firstTicket = $this->createTicket($firstOwner, ['status' => 'open']);
        $secondTicket = $this->createTicket($secondOwner, ['status' => 'in_progress']);

        $response = $this->actingAs($admin)->post(route('tickets.bulk.status'), [
            'ticket_ids' => [$firstTicket->id, $secondTicket->id],
            'status' => 'resolved',
        ]);

        $response->assertRedirect();
        $this->assertSame('resolved', $firstTicket->fresh()->status);
        $this->assertSame('resolved', $secondTicket->fresh()->status);

        Notification::assertSentTo($firstOwner, TicketResolvedNotification::class);
        Notification::assertSentTo($secondOwner, TicketResolvedNotification::class);
    }

    public function test_bulk_resolved_status_accepts_comma_separated_ticket_ids_from_ui_form(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $ticket = $this->createTicket($owner, ['status' => 'open']);

        $response = $this->actingAs($admin)->post(route('tickets.bulk.status'), [
            'ticket_ids' => (string) $ticket->id,
            'status' => 'resolved',
        ]);

        $response->assertRedirect();
        $this->assertSame('resolved', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->resolved_at);

        Notification::assertSentTo($owner, TicketResolvedNotification::class);
    }

    public function test_ticket_can_be_assigned_without_sending_status_in_update_request(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $technician = User::factory()->create(['role' => 'teknisi']);
        $ticket = $this->createTicket($owner, ['status' => 'open']);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'assigned_to' => $technician->id,
        ]);

        $response->assertRedirect();
        $this->assertSame('in_progress', $ticket->fresh()->status);
        $this->assertSame((string) $technician->id, (string) $ticket->fresh()->assigned_to);

        Notification::assertSentTo($owner, TicketStatusChangedNotification::class);
    }

    public function test_in_progress_notification_is_sent_when_ticket_status_changes_to_in_progress(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $ticket = $this->createTicket($owner, ['status' => 'open']);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'status' => 'in_progress',
        ]);

        $response->assertRedirect();
        $this->assertSame('in_progress', $ticket->fresh()->status);

        Notification::assertSentTo($owner, TicketStatusChangedNotification::class);
    }

    public function test_in_progress_notification_is_sent_when_ticket_is_assigned_from_open_status(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $technician = User::factory()->create(['role' => 'teknisi']);
        $ticket = $this->createTicket($owner, ['status' => 'open']);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'assigned_to' => $technician->id,
        ]);

        $response->assertRedirect();
        $this->assertSame('in_progress', $ticket->fresh()->status);

        Notification::assertSentTo($owner, TicketStatusChangedNotification::class);
    }

    public function test_closed_notification_is_sent_when_ticket_status_changes_to_closed(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $ticket = $this->createTicket($owner, ['status' => 'resolved', 'resolved_at' => now()->subHour()]);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'status' => 'closed',
        ]);

        $response->assertRedirect();
        $this->assertSame('closed', $ticket->fresh()->status);

        Notification::assertSentTo($owner, TicketStatusChangedNotification::class);
    }

    public function test_status_changed_notification_is_not_sent_again_when_status_does_not_change(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $ticket = $this->createTicket($owner, ['status' => 'in_progress']);

        $response = $this->actingAs($admin)->put(route('tickets.update', $ticket), [
            'status' => 'in_progress',
        ]);

        $response->assertRedirect();

        Notification::assertNothingSent();
    }

    public function test_bulk_status_sends_notifications_for_in_progress_and_closed(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $firstOwner = User::factory()->create(['role' => 'pegawai-dinas']);
        $secondOwner = User::factory()->create(['role' => 'pegawai-dinas']);
        $firstTicket = $this->createTicket($firstOwner, ['status' => 'open']);
        $secondTicket = $this->createTicket($secondOwner, ['status' => 'resolved', 'resolved_at' => now()->subHour()]);

        $inProgressResponse = $this->actingAs($admin)->post(route('tickets.bulk.status'), [
            'ticket_ids' => [$firstTicket->id],
            'status' => 'in_progress',
        ]);

        $closedResponse = $this->actingAs($admin)->post(route('tickets.bulk.status'), [
            'ticket_ids' => [$secondTicket->id],
            'status' => 'closed',
        ]);

        $inProgressResponse->assertRedirect();
        $closedResponse->assertRedirect();
        $this->assertSame('in_progress', $firstTicket->fresh()->status);
        $this->assertSame('closed', $secondTicket->fresh()->status);

        Notification::assertSentTo($firstOwner, TicketStatusChangedNotification::class);
        Notification::assertSentTo($secondOwner, TicketStatusChangedNotification::class);
    }

    public function test_bulk_assign_sends_in_progress_notification_from_ui_form_payload(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $ticket = $this->createTicket($owner, ['status' => 'open']);

        $response = $this->actingAs($admin)->post(route('tickets.bulk.assign'), [
            'ticket_ids' => (string) $ticket->id,
            'assigned_to' => 'Teknisi Lapangan',
        ]);

        $response->assertRedirect();
        $this->assertSame('in_progress', $ticket->fresh()->status);
        $this->assertSame('Teknisi Lapangan', $ticket->fresh()->assigned_to);

        Notification::assertSentTo($owner, TicketStatusChangedNotification::class);
    }

    public function test_bulk_assign_does_not_send_in_progress_notification_when_ticket_already_in_progress(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pegawai-dinas']);
        $ticket = $this->createTicket($owner, ['status' => 'in_progress']);

        $response = $this->actingAs($admin)->post(route('tickets.bulk.assign'), [
            'ticket_ids' => [$ticket->id],
            'assigned_to' => 'Teknisi Lapangan',
        ]);

        $response->assertRedirect();
        $this->assertSame('in_progress', $ticket->fresh()->status);

        Notification::assertNothingSent();
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createTicket(User $owner, array $overrides = []): Ticket
    {
        $category = Category::create([
            'name' => 'Hardware',
            'slug' => 'hardware-'.uniqid(),
            'sla_hours' => 24,
        ]);

        return Ticket::create(array_merge([
            'ticket_number' => 'TIX-'.uniqid(),
            'subject' => 'Printer bermasalah',
            'description' => 'Printer tidak bisa digunakan.',
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'priority' => 'medium',
            'status' => 'open',
            'sla_due_date' => now()->addDay(),
        ], $overrides));
    }
}
