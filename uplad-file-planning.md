# Planning Fitur Upload File Saat Membuat Ticket

## Tujuan

Membuat input **Lampiran (Opsional)** pada halaman pembuatan ticket benar-benar menyimpan file, mencatat metadata file ke database, dan menampilkan/mengunduh lampiran dari detail ticket.

## Ringkasan Kondisi Saat Ini

### Alur ticket

- Route pembuatan ticket sudah ada:
  - `GET /tickets/create` -> `TicketController@create`
  - `POST /tickets` -> `TicketController@store`
- Form create ticket sudah memiliki input:
  - `name="attachments[]"`
  - `multiple`
  - `accept=".jpg,.jpeg,.png,.pdf,.doc,.docx"`
- Form belum memiliki `enctype="multipart/form-data"`, sehingga file tidak akan ikut terkirim ke request.
- `TicketController@store` saat ini hanya memvalidasi dan menyimpan:
  - `subject`
  - `description`
  - `category_id`
  - `priority`
  - metadata ticket seperti `user_id`, `ticket_number`, `status`, `sla_due_date`
- Belum ada proses validasi `attachments`, penyimpanan file ke storage, atau pencatatan metadata file.

### Struktur database aktual

Database yang digunakan dari `.env`:

- `DB_CONNECTION=mysql`
- `DB_DATABASE=db_ticketing`
- `FILESYSTEM_DISK=local`

Tabel `tickets` sudah berjalan dan memiliki kolom utama:

- `id`
- `ticket_number`
- `subject`
- `description`
- `user_id`
- `category_id`
- `assigned_to`
- `status`
- `priority`
- `sla_due_date`
- `resolved_at`
- `closed_at`
- `assignment_note`
- `assigned_at`
- timestamps
- `deleted_at`

Belum ada tabel untuk lampiran ticket.

Catatan terkait tabel lain:

- `ticket_comments` dipakai oleh relasi `Ticket::messages()` dan fitur diskusi ticket.
- `ticket_messages` ada di database tetapi migration-nya hanya berisi `id` dan timestamps, sehingga tidak relevan untuk fitur upload file saat ini.

### Model dan view terkait

- `App\Models\Ticket` belum memiliki relasi attachment.
- `resources/views/tickets/show.blade.php` sudah memiliki blok tampilan lampiran, tetapi masih dikomentari dan mengasumsikan relasi `$ticket->attachments`.
- `resources/views/tickets/create.blade.php` sudah memiliki preview file di sisi browser, tetapi tombol hapus pada preview hanya menghapus tampilan, bukan mengubah `FileList` input file. Ini perlu diperbaiki atau dibuat jelas sebagai preview saja.

## Keputusan Desain

Gunakan tabel terpisah `ticket_attachments`, bukan kolom JSON di `tickets`.

Alasannya:

- Satu ticket dapat memiliki banyak file.
- Metadata tiap file perlu disimpan terstruktur.
- Mudah membuat relasi Eloquent `Ticket hasMany TicketAttachment`.
- Mudah menghapus file terkait saat ticket dihapus.
- Lebih mudah dikembangkan nanti untuk lampiran komentar/diskusi.

Gunakan disk `public` atau `local` dengan download route terproteksi.

Rekomendasi untuk aplikasi ticket internal: gunakan disk `local`/private dan sediakan route download yang mengecek hak akses. Karena `.env` saat ini `FILESYSTEM_DISK=local`, pendekatan ini paling sesuai dengan konfigurasi sekarang.

## Struktur Database Baru

Buat migration baru:

`create_ticket_attachments_table`

Kolom yang disarankan:

- `id`
- `ticket_id` foreign key ke `tickets.id`, cascade on delete
- `user_id` foreign key ke `users.id`, cascade on delete atau null on delete sesuai kebutuhan audit
- `disk` string, default `local`
- `path` string
- `original_name` string
- `stored_name` string, nullable
- `mime_type` string, nullable
- `extension` string, nullable
- `size` unsigned big integer
- timestamps

Index:

- `ticket_id`
- `user_id`

Contoh rule:

- Jika ticket dihapus permanen, metadata attachment ikut terhapus karena `cascade`.
- Karena `tickets` memakai soft delete, file tidak otomatis hilang saat soft delete. Penghapusan file fisik bisa dilakukan lewat model event jika nanti dibutuhkan.

## Perubahan Kode yang Dibutuhkan

### 1. Model baru `TicketAttachment`

Buat `app/Models/TicketAttachment.php`.

Isi utama:

- `$fillable` untuk kolom metadata.
- Relasi:
  - `ticket(): BelongsTo`
  - `user(): BelongsTo`
- Helper opsional:
  - `getSizeKbAttribute()`
  - `getDownloadNameAttribute()`

### 2. Relasi pada `Ticket`

Tambahkan pada `App\Models\Ticket`:

- `attachments(): HasMany`

Update query eager loading:

- `TicketController@show` perlu memuat `attachments.user`.
- Jika ingin indikator lampiran di daftar ticket, `index()` dan `my()` bisa memakai `withCount('attachments')`.

### 3. Form create ticket

Update `resources/views/tickets/create.blade.php`:

- Tambahkan `enctype="multipart/form-data"` pada `<form>`.
- Pertahankan input `attachments[]`.
- Tambahkan tampilan error validasi untuk:
  - `attachments`
  - `attachments.*`
- Tambahkan validasi client-side ringan:
  - maksimal 5 file
  - maksimal 5 MB per file
  - tipe file sesuai daftar

Catatan: validasi browser hanya untuk UX. Validasi utama tetap wajib di backend.

### 4. Validasi backend di `TicketController@store`

Tambahkan rule:

- `attachments` -> `nullable|array|max:5`
- `attachments.*` -> `file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx`

Jika ingin validasi MIME lebih ketat:

- gunakan `mimetypes:image/jpeg,image/png,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document`

### 5. Penyimpanan file

Setelah `Ticket::create($validated)`, simpan attachment:

- direktori: `tickets/{ticket_id}/attachments`
- disk: `local`
- nama file: gunakan `store()`/`storeAs()` agar nama aman dan tidak tabrakan
- simpan metadata ke `ticket_attachments`

Penting:

- Buat ticket dan attachment dalam database transaction.
- Jika salah satu file gagal disimpan, rollback metadata dan hapus file yang sudah sempat tersimpan.
- Jangan memakai nama asli sebagai path storage karena rawan karakter aneh dan collision.

### 6. Download lampiran

Buat route baru yang terproteksi auth:

- `GET /tickets/{ticket}/attachments/{attachment}/download`
- name: `tickets.attachments.download`

Buat method controller:

- Bisa di `TicketController@downloadAttachment`
- Atau controller baru `TicketAttachmentController@download`

Validasi akses minimal:

- pemilik ticket boleh download
- teknisi yang ditugaskan boleh download
- admin/superadmin/staff sesuai role/permission aplikasi boleh download

Validasi integritas:

- Pastikan `attachment.ticket_id === ticket.id`
- Pastikan file masih ada di storage
- Return `Storage::disk($attachment->disk)->download($attachment->path, $attachment->original_name)`

### 7. Tampilan lampiran di detail ticket

Aktifkan kembali blok lampiran di `resources/views/tickets/show.blade.php`.

Tampilkan:

- nama asli file
- ukuran file
- icon berdasarkan tipe file
- tombol download

Gunakan relasi:

- `$ticket->attachments`

### 8. Penghapusan file

Minimal untuk scope fitur create ticket:

- Tidak wajib membuat fitur hapus lampiran.

Jika ingin lengkap:

- Tambahkan route delete attachment.
- Batasi hanya pemilik ticket sebelum diproses atau admin.
- Hapus file fisik dari storage lalu hapus row database.

Untuk jangka panjang:

- Tambahkan model event pada `TicketAttachment::deleted()` untuk menghapus file fisik.
- Pertimbangkan cleanup job untuk file orphan jika transaksi gagal di luar skenario normal.

## Urutan Implementasi

1. Buat migration `ticket_attachments`.
2. Jalankan migration.
3. Buat model `TicketAttachment`.
4. Tambahkan relasi `Ticket::attachments()`.
5. Update `TicketController@store`:
   - validasi attachments
   - transaction
   - simpan file
   - simpan metadata
6. Update `resources/views/tickets/create.blade.php`:
   - `enctype="multipart/form-data"`
   - error validation
   - validasi preview di frontend
7. Update `TicketController@show` agar eager load `attachments.user`.
8. Tambahkan route dan method download attachment.
9. Aktifkan tampilan lampiran di `resources/views/tickets/show.blade.php`.
10. Tambahkan test feature:
    - user bisa membuat ticket tanpa lampiran
    - user bisa membuat ticket dengan 1 lampiran valid
    - user gagal upload file lebih dari 5 MB
    - user gagal upload ekstensi tidak valid
    - user bisa download lampiran milik ticket yang diizinkan
    - user tidak bisa download lampiran ticket milik orang lain jika tidak punya role akses

## File yang Kemungkinan Diubah

- `database/migrations/*_create_ticket_attachments_table.php`
- `app/Models/TicketAttachment.php`
- `app/Models/Ticket.php`
- `app/Http/Controllers/TicketController.php`
- `routes/web.php`
- `resources/views/tickets/create.blade.php`
- `resources/views/tickets/show.blade.php`
- `tests/Feature/TicketAttachmentTest.php`

## Risiko dan Hal yang Perlu Diperhatikan

- Form tanpa `enctype` adalah penyebab langsung file tidak terkirim.
- Validasi backend wajib karena accept attribute di HTML bisa dilewati.
- Disk `local` bersifat private; file tidak bisa diakses langsung lewat URL publik. Ini bagus untuk lampiran ticket, tetapi perlu route download.
- Jika memilih disk `public`, jalankan `php artisan storage:link`, tetapi file akan lebih mudah diakses lewat URL publik. Untuk data ticket internal, opsi private lebih aman.
- `assigned_to` saat ini bertipe `varchar` di database, sementara beberapa query dan relasi memperlakukannya seperti user id. Ini bukan blocker upload file, tetapi bisa memengaruhi logic akses teknisi untuk download attachment.
- `resources/views/tickets/show.blade.php` menggunakan `$ticket->technician`, padahal model memiliki relasi `assignedTechnician()`. Ini bukan bagian utama upload file, tetapi perlu diwaspadai saat menyentuh halaman detail.

## Definition of Done

- User dapat memilih sampai 5 lampiran saat membuat ticket.
- Ticket tetap berhasil dibuat tanpa lampiran.
- Ticket dengan lampiran menyimpan file di storage private.
- Metadata lampiran tersimpan di tabel `ticket_attachments`.
- Detail ticket menampilkan daftar lampiran.
- Lampiran bisa di-download oleh user yang berhak.
- Upload file yang tidak valid ditolak dengan pesan error yang jelas.
- Test fitur upload dan download lampiran berjalan.
