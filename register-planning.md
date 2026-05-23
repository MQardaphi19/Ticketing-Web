# Planning Implementasi Fitur Register

## Tujuan

Menyelesaikan fitur register agar user baru bisa mendaftar dari halaman login dan otomatis mendapatkan role `pegawai-dinas`.

Target perilaku:
- User membuka halaman login.
- User menekan tombol/link `Daftar sekarang`.
- Aplikasi mengarahkan user ke halaman register existing di route `register`.
- Setelah submit register berhasil, data user tersimpan.
- Field `role` pada tabel `users` otomatis bernilai `pegawai-dinas`.
- Jika sistem Spatie Permission dipakai untuk pengecekan role, user juga mendapatkan role Spatie `pegawai-dinas`.
- User tetap login otomatis setelah register dan diarahkan ke dashboard seperti alur existing.

## Kondisi Saat Ini

File utama:
- `routes/auth.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/login.blade.php`
- `app/Models/User.php`
- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/seeders/RoleSeeder.php`
- `tests/Feature/Auth/RegistrationTest.php`

Temuan:
- Route register sudah tersedia:
  - `GET /register` dengan nama route `register`
  - `POST /register`
- Controller register sudah membuat user dengan `name`, `email`, dan `password`.
- Model `User` sudah memiliki field `role` di `$fillable`.
- Kolom `users.role` saat ini memiliki default `pemohon`.
- Seeder role Spatie sudah memiliki role `pegawai-dinas`.
- Form login sudah menampilkan teks `Belum punya akun? Daftar sekarang`, tetapi link-nya masih `href="#"`.
- Test register existing hanya memastikan user bisa register dan redirect ke dashboard, belum mengecek role.

## Ruang Lingkup Perubahan

Perubahan yang diperlukan:
- Set role user baru menjadi `pegawai-dinas` saat proses register.
- Hubungkan link `Daftar sekarang` di form login ke route register.
- Tambahkan atau update test agar role register tervalidasi.

Perubahan yang tidak termasuk:
- Mendesain ulang total halaman register.
- Mengubah alur verifikasi email.
- Mengubah role user lama di database.
- Mengubah manajemen role admin di halaman user/roles.

## Rencana Implementasi

### 1. Update Proses Register

File:
- `app/Http/Controllers/Auth/RegisteredUserController.php`

Langkah:
- Pada `User::create()`, tambahkan field:
  - `role => 'pegawai-dinas'`
- Pertahankan validasi existing:
  - `name`
  - `email`
  - `password`
  - `password_confirmation`
- Pertahankan event `Registered($user)`.
- Pertahankan `Auth::login($user)`.
- Pertahankan redirect ke `dashboard`.

Catatan teknis:
- Karena model `User` sudah memakai cast `password => hashed`, `Hash::make()` existing masih aman, walaupun secara Laravel modern bisa dibuat lebih sederhana.
- Untuk perubahan minimal, jangan ubah mekanisme hashing existing.

Contoh target logika:

```php
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role' => 'pegawai-dinas',
]);
```

### 2. Sinkronisasi Dengan Spatie Permission

File:
- `app/Http/Controllers/Auth/RegisteredUserController.php`

Langkah:
- Setelah user dibuat, panggil `assignRole('pegawai-dinas')`.
- Pastikan role `pegawai-dinas` sudah ada dari `database/seeders/RoleSeeder.php`.

Catatan:
- Aplikasi saat ini menggunakan dua mekanisme role:
  - kolom `users.role`
  - package Spatie Permission melalui trait `HasRoles`
- Agar konsisten dengan keduanya, register sebaiknya mengisi kolom `role` dan juga assign role Spatie.
- Jika database test belum menjalankan seeder role, test perlu membuat role `pegawai-dinas` terlebih dahulu sebelum register.

Contoh target logika:

```php
$user->assignRole('pegawai-dinas');
```

### 3. Hubungkan Tombol Daftar Di Login

File:
- `resources/views/auth/login.blade.php`

Langkah:
- Ganti link:

```html
<a href="#">
```

menjadi:

```blade
<a href="{{ route('register') }}">
```

Target:
- Klik `Daftar sekarang` dari login akan membuka `/register`.
- Gunakan route name agar tetap aman jika path register berubah di masa depan.

### 4. Validasi Tampilan Register

File:
- `resources/views/auth/register.blade.php`

Langkah:
- Cek apakah form register sudah sesuai kebutuhan minimal.
- Pastikan form action tetap memakai:

```blade
action="{{ route('register') }}"
```

- Tidak perlu menampilkan input role di form register, karena role harus ditentukan otomatis oleh server.

Catatan:
- Jangan menerima role dari request register publik karena user bisa memanipulasi role melalui request manual.

### 5. Update Test Register

File:
- `tests/Feature/Auth/RegistrationTest.php`

Langkah:
- Tambahkan assertion setelah register:
  - user terbuat di database dengan email yang dikirim
  - field `role` bernilai `pegawai-dinas`
- Jika menggunakan Spatie role assignment di controller, buat role `pegawai-dinas` di awal test agar `assignRole()` tidak gagal saat test memakai `RefreshDatabase`.
- Tambahkan assertion bahwa user memiliki role Spatie `pegawai-dinas` bila diperlukan.

Contoh assertion:

```php
$this->assertDatabaseHas('users', [
    'email' => 'test@example.com',
    'role' => 'pegawai-dinas',
]);
```

Jika menguji Spatie:

```php
$this->assertTrue(auth()->user()->hasRole('pegawai-dinas'));
```

### 6. Pengujian Manual

Skenario:
1. Buka halaman `/login`.
2. Klik `Daftar sekarang`.
3. Pastikan browser berpindah ke `/register`.
4. Isi form register dengan data baru.
5. Submit form.
6. Pastikan user login otomatis dan redirect ke dashboard.
7. Cek database tabel `users`:
   - email sesuai input
   - role bernilai `pegawai-dinas`
   - status tetap default `active`
8. Jika memakai Spatie Permission, cek tabel relasi role:
   - user terkait memiliki role `pegawai-dinas`.

### 7. Pengujian Otomatis

Command yang disarankan:

```bash
php artisan test --filter=RegistrationTest
```

Jika ingin memastikan tidak ada regresi auth lain:

```bash
php artisan test tests/Feature/Auth
```

## Risiko Dan Mitigasi

Risiko:
- `assignRole('pegawai-dinas')` gagal jika role belum ada di database.

Mitigasi:
- Pastikan `RoleSeeder` dijalankan di environment aplikasi.
- Pada test, buat role `pegawai-dinas` secara eksplisit sebelum request register.

Risiko:
- Aplikasi memiliki dua sumber role, yaitu kolom `users.role` dan Spatie Permission.

Mitigasi:
- Isi keduanya saat register agar behavior konsisten dengan helper role di `User` dan middleware/permission Spatie.

Risiko:
- Link register di login rusak jika route auth tidak dimuat.

Mitigasi:
- `routes/web.php` sudah memuat `require __DIR__ . '/auth.php';`, jadi `route('register')` tersedia.

## Checklist Implementasi

- [ ] Update `RegisteredUserController::store()` agar user baru memiliki `role = pegawai-dinas`.
- [ ] Assign role Spatie `pegawai-dinas` setelah user dibuat.
- [ ] Update link `Daftar sekarang` di `resources/views/auth/login.blade.php` ke `route('register')`.
- [ ] Pastikan form register tidak menyediakan input role.
- [ ] Update `RegistrationTest` untuk mengecek role user baru.
- [ ] Jalankan `php artisan test --filter=RegistrationTest`.
- [ ] Uji manual dari login ke register.

## Hasil Yang Diharapkan

Setelah implementasi selesai, user publik bisa mendaftar dari halaman login. User yang berhasil register otomatis tercatat sebagai `pegawai-dinas`, tidak bisa memilih role sendiri, dan langsung masuk ke dashboard sesuai alur autentikasi yang sudah ada.
