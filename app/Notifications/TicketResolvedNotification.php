<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketResolvedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Ticket $ticket) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resolvedAt = $this->ticket->resolved_at?->format('d M Y, H:i') ?? now()->format('d M Y, H:i');

        return (new MailMessage)
            ->subject("Tiket {$this->ticket->ticket_number} Sudah Diselesaikan")
            ->greeting("Halo {$notifiable->name},")
            ->line("Tiket Anda dengan nomor {$this->ticket->ticket_number} sudah diselesaikan.")
            ->line("Judul tiket: {$this->ticket->subject}")
            ->line("Waktu penyelesaian: {$resolvedAt}")
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
            'status' => $this->ticket->status,
        ];
    }
}
