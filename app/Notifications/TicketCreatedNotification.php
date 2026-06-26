<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket;

        return (new MailMessage)
            ->subject("Tiket Baru: {$ticket->ticket_number} - {$ticket->subject}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Terdapat tiket baru yang diajukan ke Sistem Tiket Layanan Diskominfo.")
            ->line("Nomor Tiket: {$ticket->ticket_number}")
            ->line("Subjek: {$ticket->subject}")
            ->line("Deskripsi: " . str($ticket->description)->limit(200))
            ->line("Prioritas: " . ucfirst($ticket->priority))
            ->line("Kategori: {$ticket->category?->name}")
            ->line("Pemohon: {$ticket->user?->name}")
            ->action('Lihat Detail Tiket', route('tickets.show', $ticket))
            ->line('Silakan buka sistem untuk menindaklanjuti tiket ini.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
        ];
    }
}
