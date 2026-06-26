# Planning: Pembatasan Pembuatan Tiket Berdasarkan Jam Kerja

## 1. Ringkasan

Membatasi pembuatan tiket hanya pada jam kerja (08:00 - 16:00) dengan validasi di sisi backend dan frontend, serta menampilkan pesan informatif jika pengguna mencoba membuat tiket di luar jam kerja.

---

## 2. File yang Akan Diubah

| # | File | Perubahan |
|---|------|-----------|
| 1 | `app/Http/Controllers/TicketController.php` | Menambah validasi jam kerja di method `store()` |
| 2 | `resources/views/tickets/create.blade.php` | Menambah banner/alert jam kerja dan validasi JS sebelum submit |

---

## 3. Detail Perubahan

### 3.1. Backend — `TicketController@store` (tambah validasi)

**Lokasi:** `app/Http/Controllers/TicketController.php:113-173`

**Perubahan:**
- Setelah validasi request (line 115-122), sebelum logic pembuatan tiket (line 124+), tambahkan pengecekan jam saat ini.
- Jika jam saat ini < 08:00 ATAU >= 16:00, redirect ke halaman create dengan error flash:
  - `with('error', 'Tiket hanya dapat dibuat pada jam kerja (08:00 - 16:00). Silakan ulangi pembuatan tiket pada jam kerja.')`
- Gunakan `now()` (Laravel) yang mengambil timezone dari `config/app.php` (`timezone`). Default Laravel adalah `UTC`, perlu dipastikan timezone sudah `Asia/Jakarta` (WIB).
- Validasi hanya membandingkan jam (`H`) tanpa menit untuk batas atas (16:00 = 16:00:00 sudah tidak boleh), atau bisa menggunakan `H:i` dengan `16:00` sebagai batas inklusif di awal dan eksklusif di akhir.

**Flow validasi:**
```
$hour = (int) now()->format('H');
if ($hour < 8 || $hour >= 16) {
    return redirect()->route('tickets.create')
        ->with('error', 'Tiket hanya dapat dibuat pada jam kerja (08:00 - 16:00). Silakan ulangi pembuatan tiket pada jam kerja.');
}
```

**Pertimbangan timezone:**
- Cek `config/app.php` → pastikan `'timezone' => 'Asia/Jakarta'` atau `'Asia/Makassar'` / `'Asia/Jayapura'` sesuai kebutuhan.
- Jika belum diset, waktu `now()` akan menggunakan UTC dan validasi akan salah.
- **Rekomendasi:** Set timezone ke `Asia/Jakarta` (WIB) di `.env`: `APP_TIMEZONE=Asia/Jakarta`

### 3.2. Frontend — `create.blade.php` (tambah banner dan validasi sisi klien)

**Lokasi:** `resources/views/tickets/create.blade.php`

**Perubahan:**

#### a. Banner informasi jam kerja
Di bagian atas form, tambahkan alert info yang menampilkan jam kerja (08:00 - 16:00) agar pengguna tahu sejak awal.

#### b. Validasi client-side sebelum submit
Tambahkan JavaScript `beforeSubmit` atau `onSubmit` handler:
- Cek jam lokal browser
- Jika di luar 08:00 - 16:00, tampilkan `alert()` atau modal dengan pesan:
  > "Tiket hanya dapat dibuat pada jam kerja (08:00 - 16:00). Silakan ulangi pembuatan tiket pada jam kerja."
- Cegah form submission (`event.preventDefault()`)

**Catatan:** Jam browser bisa dimanipulasi pengguna, jadi ini hanya lapisan UX tambahan. Validasi utama tetap di backend.

---

## 4. Skenario Alur

### Skenario A: Pengguna mengakses halaman create di luar jam kerja
1. User buka `/tickets/create`
2. Form tetap ditampilkan (tidak diblokir akses halaman)
3. Ada banner info jam kerja di halaman
4. Saat user klik "Kirim Permohonan", validasi JS cek jam → gagal → tampilkan pesan
5. (Lapisan akhir) Backend juga tolak dengan pesan yang sama

### Skenario B: Pengguna mengakses halaman create di jam kerja
1. User buka `/tickets/create`
2. Banner info jam kerja tetap tampil sebagai informasi
3. User isi form dan submit
4. Validasi JS lolos, validasi backend lolos
5. Tiket berhasil dibuat

---

## 5. Daftar Tugas (Task List)

| # | Task | File |
|---|------|------|
| 1 | Set/verifikasi `APP_TIMEZONE=Asia/Jakarta` di `.env` | `.env` |
| 2 | Tambah validasi jam kerja di `store()` | `TicketController.php` |
| 3 | Tambah banner info jam kerja di halaman create | `create.blade.php` |
| 4 | Tambah JS validation sebelum submit form | `create.blade.php` |
| 5 | Update `config/app.php` timezone jika perlu | `config/app.php` |

---

## 6. Catatan Tambahan

- **Tidak perlu migrasi database** — validasi murni di layer aplikasi.
- **Tidak perlu model/event/listener baru** — cukup satu pengecekan kondisional di controller.
- **Waktu server vs waktu client** — Backend pakai `now()` (server time), frontend pakai `new Date()` (browser time). Pastikan server timezone sudah sesuai WIB agar validasi backend akurat.
- **Format jam:** 08:00 = jam 8 pagi (inklusif), 16:00 = jam 4 sore (eksklusif, berarti tiket tidak bisa dibuat jam 16:00 ke atas).
