# Planning: Confidence Validator (< 20%) untuk Chatbot

## Overview
Menambahkan validasi confidence score pada fitur chatbot. Ketika hasil prediksi dari API Python memiliki confidence **kurang dari 20%**, chatbot akan menampilkan pesan gagal dan menginformasikan user untuk mencoba kembali dengan deskripsi yang lebih jelas.

---

## Analisis Flow Saat Ini

```
User Input → Laravel Controller → Python API (/predict) → Response {category, confidence} → Laravel logs & returns → Frontend displays
```

**Titik integrasi yang ada:**
- Confidence dihitung di `api/model.py:76` → `max(probabilities) * 100`
- Confidence dikembalikan dari Python API di `api/app.py:78`
- Laravel menerima & menyimpan confidence di `ChatbotController.php:79, 86`
- Frontend menampilkan confidence di `tickets/create.blade.php:315-316`

**Masalah saat ini:** Tidak ada logic threshold yang menghentikan prediksi ketika confidence terlalu rendah. Prediksi selalu ditampilkan apapun nilai confidencenya.

---

## Perubahan yang Diperlukan

### 1. Backend - ChatbotController.php

**File:** `app/Http/Controllers/ChatbotController.php`

**Perubahan:**
- Tambahkan constant/threshold `MIN_CONFIDENCE = 20`
- Setelah menerima response dari Python API, cek apakah `confidence_score < MIN_CONFIDENCE`
- Jika YA, return response dengan status `low_confidence` dan pesan error
- Tetap log prediksi ke database untuk tracking (dengan flag atau confidence rendah)

**Kode baru (estimasi di sekitar line 70-90):**
```php
const MIN_CONFIDENCE = 20;

// Setelah $data = $response->json();
$confidence = $data['confidence_score'] ?? 0;

if ($confidence < self::MIN_CONFIDENCE) {
    // Tetap log untuk tracking
    ChatbotLog::create([
        'user_id' => auth()->id(),
        'user_query' => $query,
        'predicted_category_id' => null, // Atau tetap simpan predicted category
        'confidence_score' => $confidence,
        'is_correct' => false, // Otomatis mark sebagai tidak akurat
    ]);

    return response()->json([
        'success' => false,
        'low_confidence' => true,
        'confidence_score' => $confidence,
        'message' => 'Maaf, sistem tidak dapat menentukan kategori dengan yakin. Silakan coba kembali dengan deskripsi yang lebih jelas dan detail.',
    ]);
}
```

### 2. Frontend - tickets/create.blade.php

**File:** `resources/views/tickets/create.blade.php`

**Perubahan di function `processChatbotPrediction()` (line ~279-346):**

Tambahkan handling untuk response `low_confidence`:

```javascript
// Setelah menerima response
if (data.low_confidence) {
    // Tampilkan pesan gagal
    addBotMessage(data.message);

    // Sembunyikan prediction panel jika ada
    const predictionDiv = document.getElementById('chatbotPrediction');
    if (predictionDiv) {
        predictionDiv.style.display = 'none';
    }

    // Tampilkan indikator visual error (opsional)
    return;
}

// Lanjutkan logic normal jika confidence cukup
```

**Perubahan UI (opsional tapi direkomendasikan):**
- Tambahkan elemen untuk pesan error di dekat chatbot area
- Gunakan styling alert-danger (merah) untuk pesan gagal, berbeda dari alert-success (hijau) untuk prediksi berhasil

**Contoh elemen HTML baru (di sekitar line 135-146, dekat chatbotPrediction):**
```html
<div class="mt-3" id="chatbotError" style="display: none;">
    <div class="alert alert-danger">
        <div class="d-flex align-items-start gap-2">
            <i class="bi bi-exclamation-triangle"></i>
            <div>
                <strong>Prediksi Gagal</strong>
                <p class="mb-0" id="errorMessage">-</p>
                <small class="text-muted">Confidence: <span id="errorConfidenceScore">-</span>% (di bawah 20%)</small>
            </div>
        </div>
    </div>
</div>
```

### 3. Database - ChatbotLog (Opsional)

**File:** `app/Models/ChatbotLog.php`

**Pertimbangan:**
- Cast `confidence_score` saat ini `integer`, mengubah ke `float` untuk presisi (opsional)
- Tidak perlu perubahan schema karena confidence rendah tetap valid disimpan

### 4. Logs Page - chatbot/logs.blade.php

**File:** `resources/views/chatbot/logs.blade.php`

**Perubahan (opsional tapi direkomendasikan):**
- Tambahkan filter/indicator untuk log dengan confidence < 20%
- Update visual threshold progress bar untuk include range 0-20%
- Saat ini: `>= 80` green, `>= 60` yellow, `< 60` red
- Bisa ditambahkan: `< 20` = dark red/danger dengan label "Sangat Rendah"

### 5. Python API (TIDAK PERLU PERUBAHAN)

API Python sudah mengembalikan confidence score dengan benar. Tidak ada perubahan yang diperlukan di sisi Python.

---

## File yang Dimodifikasi

| File | Tipe Perubahan | Priority |
|------|----------------|----------|
| `app/Http/Controllers/ChatbotController.php` | Tambah validasi threshold | HIGH |
| `resources/views/tickets/create.blade.php` | Tambah error handling & UI | HIGH |
| `resources/views/chatbot/logs.blade.php` | Update visual threshold (opsional) | MEDIUM |
| `app/Models/ChatbotLog.php` | Update cast type (opsional) | LOW |

---

## Testing Plan

1. **Test Case 1:** Input dengan deskripsi jelas → Confidence > 20% → Prediksi ditampilkan (normal)
2. **Test Case 2:** Input random/tidak jelas → Confidence < 20% → Pesan error ditampilkan
3. **Test Case 3:** Input kosong/minimal → Validation Laravel (min:10) tetap berjalan
4. **Test Case 4:** Verifikasi log tetap tersimpan untuk confidence rendah
5. **Test Case 5:** Verifikasi UI error message tampil dengan benar

---

## Edge Cases

1. **Confidence tepat 20%:** Tidak termasuk gagal (threshold < 20%, bukan <= 20%)
2. **API tidak return confidence_score:** Default ke 0, akan trigger low confidence
3. **User spam input random:** Semua akan di-log untuk analisis admin
4. **Confidence 0%:** Tetap di-log, pesan error ditampilkan

---

## Acceptance Criteria

- [ ] Ketika confidence < 20%, user melihat pesan error yang jelas
- [ ] User diinformasikan untuk mencoba kembali dengan deskripsi lebih detail
- [ ] Prediksi TIDAK ditampilkan ketika confidence < 20%
- [ ] Log tetap tersimpan untuk tracking dan analisis
- [ ] UI error message berbeda visual dari prediksi sukses (merah vs hijau)
- [ ] Normal flow (confidence >= 20%) tidak terpengaruh
