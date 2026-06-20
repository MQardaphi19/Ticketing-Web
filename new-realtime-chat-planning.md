# Planning Implementasi Chat Auto Refresh dengan JavaScript Polling

## Tujuan

Membuat chat pada `resources/views/tickets/show.blade.php` otomatis memperbarui pesan dari akun lawan chat tanpa Laravel Broadcasting, Reverb, Echo, atau WebSocket.

Mekanisme yang dipakai:
- JavaScript memanggil endpoint chat secara berkala.
- Interval polling: 0.5 detik / 500 ms.
- Endpoint mengembalikan JSON.
- Frontend hanya menambahkan pesan baru ke DOM, bukan reload halaman.

## Kondisi Saat Ini

File terkait:
- `resources/views/tickets/show.blade.php`
- `app/Http/Controllers/TicketController.php`
- `app/Models/TicketComment.php`
- `app/Models/Ticket.php`
- `routes/web.php`

Temuan dari controller dan view:
- Route kirim pesan sudah ada:

```php
Route::post('/{ticket}/messages', [TicketController::class, 'storeMessage'])->name('messages.store');
```

- Method `TicketController::storeMessage()` sudah return JSON:

```php
return response()->json(['message' => 'Pesan berhasil dikirim', 'data' => $message->load('user')]);
```

- Masalahnya, response masih mengembalikan model mentah dan belum distandarkan untuk kebutuhan render frontend.
- Belum ada endpoint GET untuk mengambil pesan baru.
- Setelah submit sukses, JS masih menjalankan:

```js
location.reload();
```

- Pesan awal dirender server-side dari:

```blade
@foreach ($ticket->messages as $message)
```

- Relasi pesan ticket memakai `TicketComment` lewat `Ticket::messages()`.
- Field konten pesan yang benar adalah `content`.

## Desain Solusi

Gunakan polling berbasis `lastMessageId`.

Alur:
1. Saat halaman detail tiket dibuka, pesan existing tetap dirender oleh Blade.
2. Setiap elemen pesan diberi `data-message-id`.
3. JavaScript membaca ID pesan terakhir sebagai `lastMessageId`.
4. Setiap 500 ms, JavaScript hit endpoint:

```http
GET /tickets/{ticket}/messages?after_id={lastMessageId}
```

5. Server mengembalikan pesan dengan `id > after_id`.
6. JavaScript append pesan baru ke `#chatMessages`.
7. `lastMessageId` diperbarui ke ID pesan terbaru.
8. Saat user mengirim pesan, POST tetap memakai endpoint existing, lalu hasil JSON langsung di-append.

## Endpoint yang Dibutuhkan

### 1. GET Pesan Baru

Tambahkan route:

```php
Route::get('/{ticket}/messages', [TicketController::class, 'messages'])->name('messages.index');
```

Letakkan sebelum route detail:

```php
Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
```

Endpoint:

```http
GET /tickets/{ticket}/messages?after_id=10
```

Response sukses:

```json
{
  "data": [
    {
      "id": 11,
      "ticket_id": 5,
      "user_id": 2,
      "user_name": "Nama User",
      "content": "Isi pesan",
      "is_internal": false,
      "created_at": "2026-05-22T10:15:00.000000Z",
      "created_at_time": "17:15"
    }
  ],
  "meta": {
    "last_id": 11
  }
}
```

Jika tidak ada pesan baru:

```json
{
  "data": [],
  "meta": {
    "last_id": 10
  }
}
```

### 2. POST Kirim Pesan

Endpoint existing tetap dipakai:

```http
POST /tickets/{ticket}/messages
```

Namun response sebaiknya distandarkan:

```json
{
  "message": "Pesan berhasil dikirim",
  "data": {
    "id": 12,
    "ticket_id": 5,
    "user_id": 1,
    "user_name": "Nama Pengirim",
    "content": "Pesan baru",
    "is_internal": false,
    "created_at": "2026-05-22T10:16:00.000000Z",
    "created_at_time": "17:16"
  }
}
```

## Penyesuaian Controller

### 1. Tambahkan Helper Authorization

Tambahkan method private di `TicketController` agar akses baca/kirim chat konsisten:

```php
private function canAccessTicketChat(Ticket $ticket): bool
{
    $user = Auth::user();

    return $ticket->user_id === $user->id
        || (string) $ticket->assigned_to === (string) $user->id
        || $user->isAdmin()
        || $user->isStaff();
}
```

Jika `isAdmin()` atau `isStaff()` belum cocok dengan model `User`, sesuaikan dengan pola role project.

### 2. Tambahkan Helper Format Payload

Agar response GET dan POST sama, buat formatter:

```php
private function formatMessage(TicketComment $message): array
{
    return [
        'id' => $message->id,
        'ticket_id' => $message->ticket_id,
        'user_id' => $message->user_id,
        'user_name' => $message->user?->name,
        'content' => $message->content,
        'is_internal' => $message->is_internal,
        'created_at' => $message->created_at?->toISOString(),
        'created_at_time' => $message->created_at?->format('H:i'),
    ];
}
```

### 3. Tambahkan Method GET Messages

Tambahkan method baru:

```php
public function messages(Request $request, Ticket $ticket): JsonResponse
{
    abort_unless($this->canAccessTicketChat($ticket), 403);

    $afterId = max((int) $request->query('after_id', 0), 0);

    $messages = $ticket->messages()
        ->with('user')
        ->when($afterId > 0, fn ($query) => $query->where('id', '>', $afterId))
        ->orderBy('id')
        ->limit(50)
        ->get();

    return response()->json([
        'data' => $messages->map(fn (TicketComment $message) => $this->formatMessage($message))->values(),
        'meta' => [
            'last_id' => $messages->last()?->id ?? $afterId,
        ],
    ]);
}
```

Catatan:
- `limit(50)` mencegah response terlalu besar jika browser lama tidak polling.
- Query pakai `id > after_id`, bukan timestamp, agar lebih stabil dan sederhana.

### 4. Sesuaikan Method Store Message

Ubah signature agar memakai route model binding:

```php
public function storeMessage(Request $request, Ticket $ticket): JsonResponse
```

Lalu:
- Cek akses ticket.
- Validasi `content`.
- Simpan pesan via relasi `$ticket->messages()->create(...)`.
- Load `user`.
- Return payload dari `formatMessage()`.

Rencana bentuk akhir:

```php
public function storeMessage(Request $request, Ticket $ticket): JsonResponse
{
    abort_unless($this->canAccessTicketChat($ticket), 403);

    $validated = $request->validate([
        'content' => 'required|string|max:2000',
        'is_internal' => 'nullable|boolean',
    ]);

    $message = $ticket->messages()->create([
        'user_id' => Auth::id(),
        'content' => $validated['content'],
        'is_internal' => $request->boolean('is_internal', false),
    ])->load('user');

    return response()->json([
        'message' => 'Pesan berhasil dikirim',
        'data' => $this->formatMessage($message),
    ], 201);
}
```

## Penyesuaian Route

Di `routes/web.php`, ubah urutan route ticket menjadi:

```php
Route::get('/{ticket}/attachments/{attachment}/download', [TicketController::class, 'downloadAttachment'])->name('attachments.download');
Route::get('/{ticket}/messages', [TicketController::class, 'messages'])->name('messages.index');
Route::post('/{ticket}/messages', [TicketController::class, 'storeMessage'])->name('messages.store');
Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
```

Route GET messages harus ada sebelum `/{ticket}` untuk menjaga route matching tetap jelas.

## Penyesuaian Blade dan JavaScript

### 1. Tambahkan Data Message ID

Pada wrapper pesan existing, tambahkan:

```blade
data-message-id="{{ $message->id }}"
```

Contoh:

```blade
<div class="d-flex mb-3 justify-content-end" style="width: 100%;" data-message-id="{{ $message->id }}">
```

### 2. Tambahkan Konfigurasi JS dari Blade

Di script `show.blade.php`:

```js
const ticketId = @json($ticket->id);
const authUserId = @json(auth()->id());
const fetchMessagesUrl = @json(route('tickets.messages.index', $ticket->id));
const storeMessageUrl = @json(route('tickets.messages.store', $ticket->id));
```

### 3. Buat Fungsi Render Pesan

Fungsi yang dibutuhkan:
- `escapeHtml(value)`
- `getLastMessageId()`
- `renderMessage(message)`
- `appendMessage(message)`
- `scrollChatToBottom()`

Aturan render:
- Jika `message.user_id === authUserId`, bubble kanan `bg-primary`.
- Jika bukan pengirim, bubble kiri `bg-warning`.
- Pakai `data-message-id` untuk mencegah duplikasi.
- Escape `content` saat render dari JavaScript.

### 4. Ubah Submit Form

Ganti:

```js
location.reload();
```

Dengan:

```js
appendMessage(data.data);
form.reset();
```

Tambahkan:
- Disable tombol submit saat request berjalan.
- Jika error validasi, tampilkan error sederhana di console atau alert.
- Pastikan header `X-CSRF-TOKEN` atau FormData berisi `_token` dari Blade.

### 5. Tambahkan Polling 0.5 Detik

Rencana JS:

```js
let lastMessageId = getLastMessageId();
let isFetchingMessages = false;

async function fetchNewMessages() {
    if (isFetchingMessages) {
        return;
    }

    isFetchingMessages = true;

    try {
        const response = await fetch(`${fetchMessagesUrl}?after_id=${lastMessageId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            return;
        }

        const payload = await response.json();

        payload.data.forEach((message) => {
            appendMessage(message);
            lastMessageId = Math.max(lastMessageId, message.id);
        });
    } finally {
        isFetchingMessages = false;
    }
}

setInterval(fetchNewMessages, 500);
```

Tambahan yang disarankan:
- Pause polling saat tab tidak aktif:

```js
if (document.hidden) return;
```

- Jalankan `fetchNewMessages()` sekali setelah halaman load.
- Setelah submit pesan sukses, update `lastMessageId`.

## Pertimbangan Interval 0.5 Detik

Polling 500 ms berarti 2 request per detik per halaman detail tiket yang terbuka.

Contoh beban:
- 10 user membuka detail tiket = sekitar 20 request/detik.
- 50 user membuka detail tiket = sekitar 100 request/detik.

Karena user meminta 0.5 detik, implementasi tetap mengikuti angka tersebut, tetapi perlu guard:
- Jangan mulai request baru jika request sebelumnya belum selesai.
- Gunakan `after_id` agar query ringan.
- Limit hasil maksimal 50 pesan per request.
- Tambahkan index database bila diperlukan pada `ticket_comments.ticket_id` dan `id`.
- Pause saat `document.hidden`.

Jika nanti beban server terasa tinggi, interval bisa dinaikkan ke 1-3 detik tanpa mengubah desain endpoint.

## File yang Akan Diubah Saat Implementasi

- `routes/web.php`
  - Tambah route GET `tickets.messages.index`.
  - Rapikan urutan route messages sebelum detail ticket.

- `app/Http/Controllers/TicketController.php`
  - Tambah method `messages()`.
  - Sesuaikan `storeMessage()` agar route model binding dan response JSON konsisten.
  - Tambah helper akses chat.
  - Tambah helper format payload message.

- `resources/views/tickets/show.blade.php`
  - Tambah `data-message-id` pada pesan existing.
  - Hapus `location.reload()`.
  - Tambah fungsi render dan append chat.
  - Tambah polling `setInterval(fetchNewMessages, 500)`.

Opsional:
- migration index tambahan untuk optimasi query jika tabel pesan sudah besar.

## Testing Manual

Skenario:
1. Login akun A dan buka detail ticket.
2. Login akun B di browser berbeda/incognito dan buka ticket yang sama.
3. Kirim pesan dari akun A.
4. Pastikan akun A langsung melihat pesan tanpa reload.
5. Pastikan akun B menerima pesan maksimal sekitar 0.5 detik.
6. Kirim pesan dari akun B.
7. Pastikan akun A menerima pesan maksimal sekitar 0.5 detik.
8. Pastikan pesan tidak dobel.
9. Pastikan scroll chat turun otomatis.
10. Pastikan user yang tidak berhak tidak bisa GET atau POST pesan ticket.

## Testing Otomatis yang Disarankan

Feature test:
- Pemilik ticket bisa GET messages.
- Teknisi assigned bisa GET messages.
- User tidak berhak mendapat 403.
- `after_id` hanya mengembalikan pesan baru.
- POST message return HTTP 201 dan payload JSON standar.
- POST message menolak `content` kosong.

## Urutan Implementasi yang Direkomendasikan

1. Tambah route GET messages.
2. Tambah helper access check dan formatter di `TicketController`.
3. Implement `messages()` untuk polling.
4. Refactor `storeMessage()` agar JSON-nya konsisten.
5. Tambahkan `data-message-id` di Blade.
6. Ganti JS submit agar append pesan, bukan reload.
7. Tambahkan polling 500 ms dengan in-flight guard.
8. Uji manual dua akun dalam dua browser.
