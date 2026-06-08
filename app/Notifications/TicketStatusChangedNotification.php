<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Ticket $ticket,
        private readonly string $status
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->statusLabel();

        $message = (new MailMessage)
            ->subject("Status Tiket {$this->ticket->ticket_number} Menjadi {$statusLabel}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Status tiket Anda dengan nomor {$this->ticket->ticket_number} berubah menjadi {$statusLabel}.")
            ->line("Judul tiket: {$this->ticket->subject}");

        if ($this->status === 'in_progress') {
            $message->line('Tiket Anda sedang diproses oleh tim terkait.');
        }

        if ($this->status === 'closed') {
            $closedAt = $this->ticket->updated_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i');
            $message->line("Waktu penutupan: {$closedAt}");
        }

        return $message
            ->action('Lihat Detail Tiket', route('tickets.show', $this->ticket))
            ->line('Silakan buka detail tiket untuk melihat informasi terbaru.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'status' => $this->status,
        ];
    }

    private function statusLabel(): string
    {
        return match ($this->status) {
            'in_progress' => 'In Progress',
            'closed' => 'Closed',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
