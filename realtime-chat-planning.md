# Planning Implementasi Realtime Chat Ticket Detail

## Tujuan

Membuat chat pada `resources/views/tickets/show.blade.php` otomatis muncul di akun lawan chat ketika ada pesan baru, tanpa perlu reload halaman manual.

Target perilaku:
- User A mengirim pesan dari detail tiket.
- Pesan tersimpan lewat endpoint existing `POST /tickets/{ticket}/messages`.
- User B yang sedang membuka detail tiket yang sama langsung melihat pesan baru di area `#chatMessages`.
- Pengirim juga melihat pesan terkirim tanpa `location.reload()`.
- Pesan hanya diterima oleh user yang berhak melihat tiket tersebut.

## Kondisi Saat Ini

File utama:
- `resources/views/tickets/show.blade.php`
- `app/Http/Controllers/TicketController.php`
- `app/Models/Ticket.php`
- `app/Models/TicketComment.php`
- `routes/web.php`

Temuan:
- Form chat sudah submit AJAX ke route `tickets.messages.store`.
- `TicketController::storeMessage()` membuat record `TicketComment` dan mengembalikan JSON.
- Setelah pesan terkirim, frontend menjalankan `location.reload()`.
- Pesan awal dirender dari `$ticket->messages`.
- Belum ada konfigurasi Broadcasting:
  - `routes/channels.php` belum ada.
  - `config/broadcasting.php` belum ada.
  - `laravel-echo` belum ada di `package.json`.
  - Reverb/Pusher client belum ada.

Catatan teknis:
- Model lama `TicketMessage` masih ada, tetapi chat detail tiket memakai `TicketComment` melalui relasi `Ticket::messages()`.
- Field pesan yang dipakai view adalah `content`, bukan `message`.

## Pendekatan yang Disarankan

Gunakan Laravel Broadcasting dengan private channel per tiket.

Alasan:
- Cocok untuk kebutuhan realtime lintas akun.
- Private channel bisa membatasi subscriber hanya ke pemilik tiket, teknisi yang ditugaskan, admin, atau staff.
- Tidak perlu refresh seluruh halaman; cukup append pesan baru ke DOM.
- Bisa dikembangkan untuk indikator typing, unread count, dan notifikasi.

Stack yang disarankan:
- Laravel Broadcasting
- Laravel Reverb sebagai WebSocket server lokal/self-hosted
- Laravel Echo di frontend
- Queue worker untuk broadcast event bila event memakai `ShouldBroadcast`

Alternatif cepat:
- Polling endpoint `GET /tickets/{ticket}/messages?after_id=...` tiap 3-5 detik.
- Lebih mudah, tapi bukan realtime murni dan lebih boros request.

## Desain Realtime

Channel:
- Nama channel: `tickets.{ticketId}`
- Tipe: private channel

Authorization rule:
- Pemilik tiket boleh subscribe.
- Teknisi yang ditugaskan boleh subscribe.
- Admin/staff boleh subscribe bila role helper tersedia.

Event:
- Nama event PHP: `TicketMessageSent`
- Broadcast alias frontend: `.ticket.message.sent`
- Payload minimal:
  - `id`
  - `ticket_id`
  - `user_id`
  - `user_name`
  - `content`
  - `created_at`
  - `created_at_time`
  - `is_internal`

Frontend:
- Blade menaruh metadata:
  - `ticketId`
  - `authUserId`
  - endpoint submit message
- JS subscribe ke `private-tickets.{ticketId}`.
- Saat event masuk:
  - Abaikan duplicate bila pesan sudah ada di DOM.
  - Render bubble kanan jika `message.user_id === authUserId`.
  - Render bubble kiri jika dari akun lain.
  - Scroll chat ke bawah.

## Rencana Implementasi

### 1. Siapkan Broadcasting

Instal dan konfigurasi Laravel Reverb:

```bash
php artisan install:broadcasting
```

Jika command meminta Reverb, pilih Reverb.

Pastikan file berikut tersedia setelah setup:
- `config/broadcasting.php`
- `routes/channels.php`
- konfigurasi Reverb di `.env`

Tambahkan dependency frontend:

```bash
npm install laravel-echo pusher-js
```

Periksa `.env` minimal:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=local
REVERB_APP_KEY=local
REVERB_APP_SECRET=local
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 2. Tambahkan Channel Authorization

Buat/ubah `routes/channels.php`:

```php
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('tickets.{ticketId}', function (User $user, int $ticketId): bool {
    $ticket = Ticket::find($ticketId);

    if (! $ticket) {
        return false;
    }

    return $ticket->user_id === $user->id
        || (string) $ticket->assigned_to === (string) $user->id
        || $user->isAdmin()
        || $user->isStaff();
});
```

Jika method `isAdmin()` atau `isStaff()` belum konsisten di model `User`, sesuaikan dengan pola role project.

### 3. Buat Event Broadcast

Buat event:

```bash
php artisan make:event TicketMessageSent
```

Isi event:
- Constructor menerima `TicketComment $message`.
- Load relasi `user`.
- Implement `ShouldBroadcast`.
- Broadcast ke `new PrivateChannel('tickets.' . $message->ticket_id)`.
- Gunakan `broadcastAs()` untuk nama event stabil.
- Gunakan `broadcastWith()` agar payload frontend rapi dan tidak tergantung struktur model mentah.

Contoh payload:

```php
public function broadcastWith(): array
{
    return [
        'id' => $this->message->id,
        'ticket_id' => $this->message->ticket_id,
        'user_id' => $this->message->user_id,
        'user_name' => $this->message->user?->name,
        'content' => $this->message->content,
        'is_internal' => $this->message->is_internal,
        'created_at' => $this->message->created_at?->toISOString(),
        'created_at_time' => $this->message->created_at?->format('H:i'),
    ];
}
```

### 4. Broadcast Setelah Pesan Tersimpan

Ubah `TicketController::storeMessage()`:
- Validasi user berhak mengirim pesan pada tiket.
- Simpan `TicketComment`.
- Load relasi `user`.
- Dispatch event `TicketMessageSent`.
- Return JSON payload yang sama seperti event.

Flow yang diinginkan:

```php
$message = TicketComment::create($validated)->load('user');

TicketMessageSent::dispatch($message);

return response()->json([
    'message' => 'Pesan berhasil dikirim',
    'data' => [
        ...
    ],
]);
```

Jika ingin pengirim tidak menerima event duplikat dari socket yang sama, gunakan `InteractsWithSockets` di event dan panggil:

```php
broadcast(new TicketMessageSent($message))->toOthers();
```

Dalam kasus ini, frontend pengirim langsung append dari response AJAX, sedangkan lawan chat menerima dari broadcast.

### 5. Konfigurasi Echo Frontend

Ubah `resources/js/bootstrap.js`:

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

Pastikan layout utama memakai asset Vite:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Jika layout belum memakai Vite untuk JS, tambahkan sesuai struktur project.

### 6. Refactor JS Chat di `show.blade.php`

Ganti perilaku `location.reload()` dengan append pesan:

Fungsi yang perlu dibuat:
- `escapeHtml(value)` untuk mencegah XSS.
- `renderMessage(message)` untuk membuat HTML bubble chat.
- `appendMessage(message)` untuk memasukkan pesan jika belum ada.
- `scrollChatToBottom()`.

Tambahkan `data-message-id` pada item pesan existing:

```blade
<div class="d-flex mb-3 justify-content-end" data-message-id="{{ $message->id }}">
```

Submit form:
- Prevent default.
- Disable tombol saat request berjalan.
- `fetch()` ke endpoint existing.
- Saat sukses, append pesan dari response.
- Reset input.
- Jangan reload halaman.

Subscribe realtime:

```js
window.Echo.private(`tickets.${ticketId}`)
    .listen('.ticket.message.sent', (message) => {
        appendMessage(message);
    });
```

Jika controller memakai `toOthers()`, response AJAX menangani append untuk pengirim dan broadcast menangani append untuk lawan chat.

### 7. Jalankan Service yang Dibutuhkan

Untuk development, jalankan proses berikut:

```bash
php artisan serve
php artisan reverb:start
php artisan queue:work
npm run dev
```

Jika event dibuat `ShouldBroadcastNow`, queue worker tidak wajib untuk broadcast, tapi tetap lebih baik mengikuti queue untuk produksi.

### 8. Testing Manual

Skenario uji:
- Login akun pemohon di browser A.
- Login akun teknisi/admin di browser B atau incognito.
- Buka ticket detail yang sama.
- Kirim pesan dari A.
- Pastikan B menerima pesan tanpa reload.
- Kirim pesan dari B.
- Pastikan A menerima pesan tanpa reload.
- Pastikan bubble pengirim kanan dan lawan chat kiri.
- Pastikan pesan tidak double di sisi pengirim.
- Pastikan user yang tidak punya akses tiket gagal subscribe ke private channel.

### 9. Testing Otomatis yang Disarankan

Feature test:
- User pemilik tiket bisa store message.
- Teknisi assigned bisa store message.
- User tidak berhak tidak bisa store message.
- Response JSON berisi payload yang dibutuhkan frontend.

Broadcast/channel test:
- Pemilik tiket authorized ke `tickets.{id}`.
- Teknisi assigned authorized.
- User lain rejected.

## File yang Kemungkinan Diubah

- `app/Events/TicketMessageSent.php`
- `app/Http/Controllers/TicketController.php`
- `routes/channels.php`
- `resources/js/bootstrap.js`
- `resources/views/tickets/show.blade.php`
- `.env.example`
- `package.json`
- `package-lock.json`
- `config/broadcasting.php`

## Risiko dan Hal yang Perlu Diperhatikan

- Dependency Reverb/Echo perlu install package dan rebuild asset.
- Private channel butuh endpoint auth Laravel Broadcasting aktif.
- Jika Vite belum dimuat di layout, Echo tidak akan tersedia di Blade.
- Jika menggunakan queue, pesan realtime tidak muncul kalau `queue:work` belum berjalan.
- Jangan broadcast data model mentah yang berisi field tidak perlu.
- Tetap escape konten pesan saat render dari JavaScript untuk mencegah XSS.

## Fallback Polling Jika Tidak Ingin WebSocket

Jika implementasi WebSocket belum memungkinkan, buat endpoint:

```http
GET /tickets/{ticket}/messages?after_id={lastMessageId}
```

Frontend:
- Simpan `lastMessageId`.
- Poll setiap 3-5 detik.
- Append pesan baru yang dikembalikan server.

Kelebihan:
- Lebih cepat dibuat.
- Tidak perlu Reverb/Echo.

Kekurangan:
- Tidak realtime murni.
- Ada delay.
- Lebih banyak request ke server.

## Rekomendasi Eksekusi

Implementasi utama sebaiknya memakai Broadcasting + Reverb karena kebutuhan yang diminta adalah realtime antar akun. Polling hanya dipakai jika environment belum siap menjalankan WebSocket server.
