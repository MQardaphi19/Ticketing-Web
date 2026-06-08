# Planning Implementasi Email Notification Tiket Resolved

## Tujuan

Menambahkan fitur notifikasi email ketika tiket yang diajukan oleh pegawai dinas berubah status menjadi `resolved`. Email dikirim ke alamat email akun pegawai dinas pemilik tiket tersebut.

Implementasi wajib memakai konfigurasi email dari environment variable, dengan target provider Gmail SMTP, dan tidak boleh menambah atau mengubah struktur database.

## Kondisi Sistem Saat Ini

- Project menggunakan Laravel 12.
- Konfigurasi email sudah tersedia di `config/mail.php` dan membaca env standar Laravel seperti `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`, dan `MAIL_FROM_NAME`.
- Model `User` sudah memakai trait `Notifiable`, sehingga bisa menerima notifikasi Laravel via email tanpa perubahan database.
- Model `Ticket` sudah memiliki relasi `user()` ke pemilik tiket.
- Perubahan status tiket individu terjadi di `app/Http/Controllers/TicketController.php` pada method `update`.
- Perubahan status tiket massal terjadi di method `bulkStatus`, tetapi saat ini memakai query update langsung, sehingga perlu penanganan khusus agar email tetap terkirim.
- Kolom `resolved_at` sudah tersedia di tabel `tickets`, sehingga tidak perlu migration baru.

## Batasan

- Tidak membuat migration baru.
- Tidak menambah kolom baru pada tabel apa pun.
- Tidak mengubah relasi database.
- Tidak menyimpan status pengiriman email di database.
- Kredensial Gmail hanya dimasukkan melalui `.env`, bukan hardcoded di source code.

## Rencana File yang Akan Ditambahkan atau Diubah

### File Baru

- `app/Notifications/TicketResolvedNotification.php`
  - Notification Laravel untuk isi email tiket resolved.
  - Menggunakan channel `mail`.
  - Menerima objek `Ticket`.
  - Memuat informasi minimal:
    - nomor tiket,
    - subject tiket,
    - status resolved,
    - tanggal/waktu resolved,
    - link detail tiket jika tersedia.

- `tests/Feature/TicketResolvedEmailNotificationTest.php`
  - Menguji email terkirim ketika tiket berubah dari status selain `resolved` menjadi `resolved`.
  - Menguji email tidak terkirim ulang ketika tiket sudah `resolved` lalu disimpan ulang sebagai `resolved`.
  - Menguji email dikirim ke pemilik tiket pegawai dinas.
  - Menguji jalur bulk status jika method `bulkStatus` ikut diperbaiki.

### File yang Akan Diubah

- `app/Http/Controllers/TicketController.php`
  - Menambahkan import notification atau facade yang dibutuhkan.
  - Pada method `update`, simpan status lama sebelum update.
  - Setelah update berhasil, kirim notifikasi hanya jika:
    - status lama bukan `resolved`,
    - status baru adalah `resolved`,
    - tiket memiliki user pemilik,
    - user pemilik memiliki email,
    - user pemilik adalah `pegawai-dinas`.
  - Pada method `bulkStatus`, hindari query update massal langsung untuk kasus status `resolved` karena query massal tidak memberi kesempatan mengirim notifikasi per tiket.
  - Untuk bulk status `resolved`, ambil tiket beserta `user`, update satu per satu, lalu kirim notifikasi sesuai aturan di atas.
  - Untuk bulk status selain `resolved`, tetap bisa memakai mekanisme yang ada atau diseragamkan dengan loop sesuai kebutuhan.

- `.env.example`
  - Menambahkan contoh konfigurasi Gmail SMTP agar pengguna tahu env apa yang harus diisi.
  - Nilai credential tetap placeholder.

Opsional:

- `.env`
  - Bisa diisi secara lokal dengan kredensial Gmail milik pengguna, tetapi tidak wajib di-commit.
  - Jika diminta saat implementasi, nilai yang diisi tetap harus berasal dari pengguna.

## Env Variable Gmail yang Disiapkan

Contoh konfigurasi yang akan ditambahkan ke `.env.example`:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-gmail-address@gmail.com
MAIL_PASSWORD=your-gmail-app-password
MAIL_FROM_ADDRESS=your-gmail-address@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

Catatan:

- Gmail biasanya membutuhkan App Password, bukan password akun utama.
- Akun Gmail harus mengaktifkan 2-Step Verification sebelum App Password bisa dibuat.
- `MAIL_PASSWORD` sebaiknya diisi App Password tanpa spasi.
- Setelah mengubah `.env`, jalankan `php artisan config:clear` agar konfigurasi terbaru terbaca.

## Desain Trigger Pengiriman Email

### Method `update`

Alur yang direncanakan:

1. Ambil tiket dari database.
2. Simpan nilai status lama, misalnya `$previousStatus = $ticket->status`.
3. Validasi input seperti saat ini.
4. Jika status baru `resolved` dan `resolved_at` masih kosong, isi `resolved_at = now()`.
5. Update tiket.
6. Reload relasi `user` bila diperlukan.
7. Kirim notifikasi hanya jika transisi status benar-benar dari non-`resolved` ke `resolved`.

Pseudocode:

```php
$previousStatus = $ticket->status;

$ticket->update($validated);

if ($previousStatus !== 'resolved' && $ticket->status === 'resolved') {
    $ticket->loadMissing('user');

    if ($ticket->user?->isPegawaiDinas() && filled($ticket->user->email)) {
        $ticket->user->notify(new TicketResolvedNotification($ticket));
    }
}
```

### Method `bulkStatus`

Masalah saat ini:

- `Ticket::where(...)->update(...)` melakukan update langsung di database.
- Karena tidak ada object model per tiket yang diproses, email per pemilik tiket tidak bisa dikirim dengan aman.

Rencana:

1. Validasi `ticket_ids`, `assigned_to`, dan `status`.
2. Jika status bulk adalah `resolved`, ambil tiket dengan relasi `user`.
3. Loop setiap tiket:
   - simpan status lama,
   - update status,
   - set `resolved_at` jika belum ada,
   - kirim email bila memenuhi aturan transisi.
4. Jika status bulk bukan `resolved`, update seperti biasa atau loop tanpa kirim email.

## Isi Email yang Direncanakan

Subject:

```text
Tiket {ticket_number} Sudah Diselesaikan
```

Konten utama:

- Sapaan ke nama pegawai dinas.
- Informasi bahwa tiket sudah berstatus resolved.
- Nomor tiket.
- Judul tiket.
- Waktu penyelesaian jika tersedia.
- Tombol/link untuk melihat detail tiket.

Contoh isi ringkas:

```text
Halo {nama},

Tiket Anda dengan nomor {ticket_number} dan judul "{subject}" sudah diselesaikan.

Silakan buka detail tiket untuk melihat informasi terbaru.
```

## Strategi Error Handling

- Pengiriman email tidak boleh menggagalkan update status tiket.
- Jika pengiriman email gagal, error dicatat melalui `report($exception)` atau log Laravel.
- User tetap menerima response sukses untuk update tiket selama update database berhasil.
- Tidak ada retry persistence di database karena ada batasan tidak boleh menambah database.

Opsi implementasi:

- Kirim langsung dengan `notify()` dan bungkus bagian pengiriman dengan `try/catch`.
- Jika queue sudah aktif dan stabil, notification bisa dibuat `ShouldQueue`, tetapi ini memakai tabel `jobs` yang sudah ada dari Laravel. Karena user meminta tidak ada perubahan database, opsi queue hanya dipakai jika infrastruktur queue existing memang sudah dipakai dan tidak membutuhkan migration baru.

Rekomendasi awal:

- Implementasi sinkron terlebih dahulu dengan `try/catch`, karena scope lebih kecil dan tidak membutuhkan perubahan database atau worker queue tambahan.

## Test Plan

### Automated Test

Gunakan `Notification::fake()` untuk menghindari pengiriman email asli.

Skenario:

1. Pegawai dinas membuat atau memiliki tiket berstatus `open`.
2. Admin/teknisi mengubah status tiket menjadi `resolved`.
3. Assert `TicketResolvedNotification` dikirim ke user pemilik tiket.
4. Assert email tidak dikirim bila status lama sudah `resolved`.
5. Assert email tidak dikirim bila pemilik tiket bukan role `pegawai-dinas`.
6. Assert bulk status `resolved` mengirim email untuk setiap tiket yang memenuhi syarat.

### Manual Test Lokal

1. Isi `.env` dengan konfigurasi Gmail SMTP.
2. Jalankan:

```bash
php artisan config:clear
```

3. Login sebagai admin/teknisi.
4. Ubah tiket milik pegawai dinas dari `open` atau `in_progress` ke `resolved`.
5. Pastikan email diterima oleh alamat email pegawai dinas.
6. Ulangi update status ke `resolved` pada tiket yang sama dan pastikan email tidak terkirim dua kali.

## Acceptance Criteria

- Email terkirim saat tiket pegawai dinas berubah status menjadi `resolved`.
- Email dikirim ke email user pemilik tiket.
- Email tidak dikirim ulang saat tiket yang sudah `resolved` diproses ulang sebagai `resolved`.
- Kredensial Gmail dapat diatur lewat `.env`.
- `.env.example` menyediakan contoh konfigurasi Gmail SMTP.
- Tidak ada migration baru.
- Tidak ada perubahan schema database.
- Test otomatis untuk notifikasi tersedia dan lulus.

## Risiko dan Catatan

- Gmail dapat menolak login SMTP jika memakai password akun biasa; perlu App Password.
- Pengiriman email sinkron bisa menambah waktu response saat update tiket. Jika nanti terasa lambat, bisa dipindah ke queue menggunakan infrastruktur queue Laravel yang sudah ada tanpa perubahan schema baru.
- Bulk update perlu dibuat lebih eksplisit agar setiap tiket dapat diproses dan dikirimi email sesuai aturan.
- Karena tidak menyimpan log pengiriman di database, audit pengiriman email hanya mengandalkan log aplikasi atau provider email.
