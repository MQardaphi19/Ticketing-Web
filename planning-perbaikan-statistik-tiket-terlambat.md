# Planning Perbaikan: Statistik Tiket Terlambat Tidak Tampil di Grafik

## 1. Identifikasi Masalah

### Lokasi
- **File:** `app/Http/Controllers/DashboardController.php:89-95`
- **View:** `resources/views/dashboard.blade.php` (Chart "Statistik Tiket Bulanan" baris 635-638)
- **Data series:** `Terlambat` menggunakan variabel `$monthlyLate`

### Akar Masalah
Query `$monthlyStatsLate` (baris 89-95) **hanya** menghitung tiket yang **sudah selesai** (*resolved* / *closed*) dan terlambat:

```php
$monthlyStatsLate = Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as late')
    ->whereYear('created_at', now()->year)
    ->whereNotNull('resolved_at')           // HANYA tiket yang sudah di-resolve
    ->whereColumn('resolved_at', '>', 'sla_due_date') // DAN di-resolve setelah SLA
    ->groupBy('month')
    ->orderBy('month')
    ->get();
```

Kondisi `->whereNotNull('resolved_at')` menyebabkan **tiket yang sedang berjalan (open/in_progress) tetapi sudah melewati SLA (overdue) tidak terhitung**. Akibatnya, jika belum ada tiket yang di-resolve setelah deadline, grafik "Terlambat" tetap kosong meskipun ada tiket yang sudah overdue.

### Model `isOverdue()` (Ticket.php:71-76)
Model sudah memiliki method `isOverdue()` yang mendefinisikan overdue dengan benar:
```php
return $this->sla_due_date
    && now()->gt($this->sla_due_date)
    && in_array($this->status, ['open', 'in_progress']);
```
Namun, logika ini **tidak digunakan** dalam query statistik bulanan.

---

## 2. Solusi

### 2.1. Perbaikan Query `$monthlyStatsLate`
Perluas query untuk mencakup **dua kategori** tiket terlambat:

| Kategori | Kondisi |
|---|---|
| **Resolved Late** | `resolved_at IS NOT NULL AND resolved_at > sla_due_date` |
| **Currently Overdue** | `resolved_at IS NULL AND sla_due_date < NOW() AND status IN ('open', 'in_progress')` |

### 2.2. Query Baru (yang diusulkan)
```php
$monthlyStatsLate = Ticket::selectRaw('MONTH(created_at) as month, COUNT(*) as late')
    ->whereYear('created_at', now()->year)
    ->where(function ($q) {
        $q->where(function ($q) {
            $q->whereNotNull('resolved_at')
              ->whereColumn('resolved_at', '>', 'sla_due_date');
        })->orWhere(function ($q) {
            $q->whereNull('resolved_at')
              ->whereNotNull('sla_due_date')
              ->where('sla_due_date', '<', now())
              ->whereIn('status', ['open', 'in_progress']);
        });
    })
    ->groupBy('month')
    ->orderBy('month')
    ->get();
```

### 2.3. Penjelasan
- **Resolved Late:** Tiket yang sudah selesai tetapi melewati batas SLA (query lama, tetap dipertahankan).
- **Currently Overdue:** Tiket yang masih open/in_progress tetapi sudah melewati `sla_due_date` (tiket yang sedang terlambat).
- Kedua kelompok digabungkan dengan `OR` di dalam `where()` closure.
- Pengelompokan tetap menggunakan `MONTH(created_at)` untuk konsistensi dengan series "Dibuat" dan "Selesai".

---

## 3. Dampak Perubahan

### Positif
- Grafik "Terlambat" akan menampilkan tiket yang sedang overdue, bukan hanya yang sudah di-resolve.
- Statistik menjadi akurat secara real-time.
- Konsisten dengan method `isOverdue()` di model Ticket.

### Negatif / Risiko
- Tidak ada risiko signifikan. Query hanya mengubah logika penghitungan, tidak ada perubahan struktur database atau relasi.
- Performa: penambahan `OR` clause dapat sedikit memperlambat query pada dataset sangat besar. Jika perlu optimasi, tambahkan index pada kolom `sla_due_date` dan `resolved_at`.

---

## 4. File yang Perlu Diubah

| File | Perubahan |
|---|---|
| `app/Http/Controllers/DashboardController.php:89-95` | Ganti query `$monthlyStatsLate` dengan query baru yang mencakup overdue tickets |
| (Tidak ada perubahan pada view) | View `dashboard.blade.php` sudah siap menampilkan data dari `$monthlyLate` |

---

## 5. Pengujian

1. Buat tiket dengan `sla_due_date` di masa lalu dan `status = 'open'`
2. Buka halaman dashboard
3. Verifikasi bahwa grafik "Statistik Tiket Bulanan" pada series "Terlambat" menampilkan nilai > 0 untuk bulan yang relevan
4. Buat tiket lain yang di-resolve setelah SLA (resolved_at > sla_due_date)
5. Verifikasi bahwa tiket tersebut juga terhitung di grafik (bulan yang sama)
