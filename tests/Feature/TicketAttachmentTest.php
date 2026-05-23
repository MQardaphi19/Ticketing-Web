<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_ticket_without_attachment(): void
    {
        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Hardware',
            'slug' => 'hardware',
            'sla_hours' => 24,
        ]);

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'subject' => 'Laptop tidak bisa booting',
            'description' => 'Laptop hanya menampilkan layar hitam.',
            'category_id' => $category->id,
            'priority' => 'medium',
        ]);

        $response->assertRedirect(route('tickets.my'));
        $this->assertDatabaseHas('tickets', [
            'subject' => 'Laptop tidak bisa booting',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseCount('ticket_attachments', 0);
    }

    public function test_user_can_create_ticket_with_attachment(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Hardware',
            'slug' => 'hardware',
            'sla_hours' => 24,
        ]);

        $response = $this->actingAs($user)->post(route('tickets.store'), [
            'subject' => 'Printer error',
            'description' => 'Printer menampilkan pesan paper jam.',
            'category_id' => $category->id,
            'priority' => 'high',
            'attachments' => [
                UploadedFile::fake()->image('error.png')->size(300),
            ],
        ]);

        $response->assertRedirect(route('tickets.my'));

        $ticket = Ticket::where('subject', 'Printer error')->firstOrFail();
        $attachment = $ticket->attachments()->firstOrFail();

        $this->assertSame('error.png', $attachment->original_name);
        $this->assertSame('local', $attachment->disk);
        Storage::disk('local')->assertExists($attachment->path);
    }

    public function test_attachment_larger_than_five_mb_is_rejected(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Hardware',
            'slug' => 'hardware',
            'sla_hours' => 24,
        ]);

        $response = $this->actingAs($user)->from(route('tickets.create'))->post(route('tickets.store'), [
            'subject' => 'File terlalu besar',
            'description' => 'Upload harus ditolak.',
            'category_id' => $category->id,
            'priority' => 'low',
            'attachments' => [
                UploadedFile::fake()->create('besar.pdf', 5121, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect(route('tickets.create'));
        $response->assertSessionHasErrors('attachments.0');
        $this->assertDatabaseCount('tickets', 0);
        $this->assertDatabaseCount('ticket_attachments', 0);
    }

    public function test_ticket_owner_can_download_attachment(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $category = Category::create([
            'name' => 'Hardware',
            'slug' => 'hardware',
            'sla_hours' => 24,
        ]);
        $ticket = Ticket::create([
            'ticket_number' => 'TIX-202605-001',
            'subject' => 'Download lampiran',
            'description' => 'Lampiran harus bisa diunduh.',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'priority' => 'medium',
            'status' => 'open',
            'sla_due_date' => now()->addDay(),
        ]);
        $path = 'tickets/'.$ticket->id.'/attachments/test.txt';
        Storage::disk('local')->put($path, 'isi file');
        $attachment = TicketAttachment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'disk' => 'local',
            'path' => $path,
            'original_name' => 'test.txt',
            'stored_name' => 'test.txt',
            'mime_type' => 'text/plain',
            'extension' => 'txt',
            'size' => 8,
        ]);

        $response = $this->actingAs($user)->get(route('tickets.attachments.download', [$ticket, $attachment]));

        $response->assertOk();
    }
}
