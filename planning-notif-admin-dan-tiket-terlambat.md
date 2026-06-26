# Planning: Notifikasi Email Admin & Pemblokiran Tiket Terlambat

## 1. Ringkasan

Dua fitur:
1. **Notifikasi email ke admin** ketika tiket baru dibuat
2. **Pemblokiran pemrosesan tiket yang sudah melewati SLA** untuk admin dan kepala-diskominfo, dengan informasi visual bahwa tiket terlambat

---

## 2. File yang Akan Diubah/Dibuat

| # | File | Perubahan |
|---|------|-----------|
| 1 | `app/Notifications/TicketCreatedNotification.php` | **BARU** — Notifikasi email tiket baru ke admin |
| 2 | `app/Http/Controllers/TicketController.php` | Kirim notifikasi di `store()` + validasi overdue di `update()`, `bulkAssign()`, `bulkStatus()` |
| 3 | `app/Models/Ticket.php` | Tambah helper `isOverdue()` untuk reusable check |
| 5 | `resources/views/tickets/show.blade.php` | Tambah banner overdue & tombol proses (dengan kondisional disabled) |
| 6 | `resources/views/tickets/index.blade.php` | Tambah indikator overdue & sembunyikan aksi untuk tiket terlambat |

---

## 3. Detail Perubahan

### 3.1. Feature 1: Notifikasi Email Tiket Baru ke Admin

> **Catatan:** Email hanya dikirim ke **admin** saja, tidak ke kepala-diskominfo.

#### 3.1.1. Notifikasi Baru — `TicketCreatedNotification.php`

- **Namespace:** `App\Notifications`
- **Channel:** `mail`
- **Dikirim ke:** Semua user dengan role `admin` atau `super-admin`
- **Subject:** `"Tiket Baru: {ticket_number} - {subject}"`
- **Konten email:**
  - Greeting: `"Halo {nama_admin}, terdapat tiket baru yang diajukan."`
  - Informasi: nomor tiket, subjek, deskripsi (truncated), prioritas, kategori, nama pemohon
  - Action button: "Lihat Detail Tiket" → `route('tickets.show', $ticket)`
- **Tidak perlu queue** (synchronous, mengikuti pattern existing)

#### 3.1.2. Kirim di `TicketController@store()`

**Lokasi:** setelah `DB::transaction` berhasil (line ~159), sebelum redirect sukses (line ~172).

**Flow:**
```
// Setelah tiket berhasil dibuat dalam transaction
$admins = User::whereIn('role', ['admin', 'super-admin'])->get();
Notification::send($admins, new TicketCreatedNotification($ticket));
```

- Gunakan `Illuminate\Support\Facades\Notification` untuk mengirim ke banyak user
- Bungkus dalam try-catch agar gagal kirim email tidak menggagalkan pembuatan tiket
- Pattern: `try { Notification::send(...); } catch (\Throwable $e) { report($e); }`

Gunakan langsung `User::whereIn('role', ['admin', 'super-admin'])` di controller (tidak perlu helper terpisah).

---

### 3.2. Feature 2: Pemblokiran Tiket Terlambat

#### 3.2.1. Helper di `Ticket.php`

Tambahkan method:
```php
public function isOverdue(): bool
{
    return $this->sla_due_date && now()->gt($this->sla_due_date)
        && in_array($this->status, ['open', 'in_progress']);
}
```

Ini reusable untuk validasi backend dan frontend.

#### 3.2.2. Validasi Backend di `TicketController`

##### a. `update()` — Single ticket update (assign/status)

**Lokasi:** Setelah `$ticket = Ticket::with('user')->findOrFail($id)` (line ~193), sebelum validasi & update.

**Logic:**
```
$user = Auth::user();
if ($ticket->isOverdue() && ($user->isAdmin() || $user->isKepalaDiskominfo())) {
    return back()->with('error', 'Tiket ini sudah melewati batas waktu SLA ({$ticket->sla_due_date->format('d M Y, H:i')}) dan tidak dapat diproses lagi.');
}
```

##### b. `bulkAssign()` — Bulk assign

**Lokasi:** Setelah `$tickets = Ticket::whereIn('id', ...)->get()` (line ~309), sebelum loop update.

**Logic:**
```
$overdueTickets = $tickets->filter(fn ($t) => $t->isOverdue());
if ($overdueTickets->isNotEmpty()) {
    return back()->with('error', 'Beberapa tiket sudah melewati SLA dan tidak dapat ditugaskan: ' . $overdueTickets->pluck('ticket_number')->implode(', '));
}
```

##### c. `bulkStatus()` — Bulk status change

**Lokasi:** Sama seperti bulkAssign, setelah fetch tickets, sebelum update loop.

**Logic:** Sama dengan bulkAssign.

#### 3.2.3. UI di `show.blade.php`

##### a. Banner overdue
Tambahkan setelah info SLA Due (setelah line ~55):
```blade
@if ($ticket->isOverdue())
    <div class="alert alert-danger d-flex align-items-start gap-2">
        <iconify-icon icon="solar:danger-triangle-linear" class="fs-5 mt-1"></iconify-icon>
        <div>
            <strong>Tiket Terlambat!</strong> Tiket ini sudah melewati batas waktu SLA ({{ $ticket->sla_due_date->format('d M Y, H:i') }}). Tidak dapat diproses lagi.
        </div>
    </div>
@endif
```

##### b. Tombol aksi admin/kepala
Tambahkan di sidebar (setelah kartu teknisi/timeline) atau di area baru:
- Tombol "Ubah Status" dan "Tugaskan Teknisi"
- Tampilkan hanya untuk user dengan role admin/kepala
- **Nonaktifkan** (disabled / hidden) jika `$ticket->isOverdue()`

```blade
@can('assign tickets')
    @if ($ticket->isOverdue())
        <button class="btn btn-secondary w-100 mb-2" disabled>
            <iconify-icon icon="solar:clock-circle-linear" class="me-2"></iconify-icon>Tiket Terlambat
        </button>
    @else
        <button class="btn btn-primary w-100 mb-2" onclick="assignTechnician()">
            <iconify-icon icon="solar:user-plus-linear" class="me-2"></iconify-icon>Tugaskan Teknisi
        </button>
        <button class="btn btn-warning w-100" onclick="changeStatus()">
            <iconify-icon icon="solar:refresh-linear" class="me-2"></iconify-icon>Ubah Status
        </button>
    @endif
@endcan
```

#### 3.2.4. UI di `index.blade.php`

##### a. Indikator overdue di baris tabel
Tambahkan badge/label di kolom SLA atau kolom status:
```blade
@if ($ticket->isOverdue())
    <span class="badge bg-danger-subtle text-danger rounded-pill px-2 ms-1">Terlambat</span>
@endif
```

##### b. Nonaktifkan aksi untuk tiket terlambat
- Dropdown "Tugaskan" dan "Ubah Status" disembunyikan atau dinonaktifkan untuk tiket `isOverdue()`
- Checkbox untuk bulk action disembunyikan untuk tiket terlambat

---

## 4. Skenario Alur

### Skenario A: User buat tiket → notifikasi admin
1. User (`pegawai-dinas`) submit form tiket baru
2. Tiket berhasil dibuat di database
3. Sistem kirim email ke semua admin: "Tiket Baru: TIX-202606-001 - Laptop Rusak"
4. User diarahkan ke halaman tiket saya dengan success message

### Skenario B: Admin buka tiket yang masih dalam SLA
1. Admin buka halaman detail tiket
2. Melihat banner "Jam Kerja" + informasi SLA normal
3. Tombol "Tugaskan Teknisi" dan "Ubah Status" aktif
4. Admin bisa memproses tiket

### Skenario C: Admin buka tiket yang sudah lewat SLA
1. Admin buka halaman detail tiket
2. Melihat banner merah "Tiket Terlambat! Tiket ini sudah melewati batas waktu SLA"
3. Tombol aksi disabled / tidak muncul
4. Admin tidak bisa assign atau ubah status

### Skenario D: Admin coba bulk assign tiket yang sudah lewat SLA
1. Admin centang beberapa tiket (termasuk yang overdue) di halaman index
2. Klik "Tugaskan Terpilih"
3. Backend deteksi tiket overdue → error: "Tiket TIX-202606-001 sudah melewati SLA"
4. Admin tidak bisa melanjutkan

---

## 5. Daftar Tugas (Task List)

| # | Task | File |
|---|------|------|
| 1 | Buat `TicketCreatedNotification.php` | `app/Notifications/` |
| 2 | Kirim notifikasi di `store()` setelah tiket sukses dibuat | `TicketController.php` |
| 3 | Tambah method `isOverdue()` di model Ticket | `app/Models/Ticket.php` |
| 4 | Validasi overdue di `update()` — blokir admin/kepala | `TicketController.php` |
| 5 | Validasi overdue di `bulkAssign()` | `TicketController.php` |
| 6 | Validasi overdue di `bulkStatus()` | `TicketController.php` |
| 7 | Tambah banner overdue + tombol aksi (disabled) di `show.blade.php` | `show.blade.php` |
| 8 | Tambah indikator overdue + nonaktifkan aksi di `index.blade.php` | `index.blade.php` |

---

## 6. Catatan Tambahan

- **Email tidak di-queue** (synchronous) mengikuti pattern notifikasi yang sudah ada. Jika perlu diubah ke queue di masa depan, implementasi `ShouldQueue`.
- **Validasi backend adalah lapisan utama.** UI hanya sebagai UX tambahan.
- **Hanya admin & kepala yang diblokir.** Teknisi (staff) dan user lain tetap bisa melihat tiket, tapi tidak disebutkan dalam requirements.
- **Helper `isOverdue()`** di Ticket model mempertimbangkan status (`open`/`in_progress`) — tiket yang sudah resolved/closed tidak dianggap overdue meskipun resolved_at lebih lambat dari SLA.
- **Tidak ada migrasi database** — semua perubahan di layer aplikasi dan view.
