@extends('layouts.app')

@section('title', 'Buat Tiket Baru - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Buat Tiket Baru')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="mb-4 fw-semibold">Formulir Permohonan</h5>

                    <form method="POST" action="{{ route('admin.tickets.store') }}" id="ticketForm">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Subjek Masalah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="subject" id="subject"
                                placeholder="Contoh: Laptop tidak bisa booting" required value="{{ old('subject') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi Masalah <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="description" id="description" rows="5"
                                placeholder="Jelaskan detail masalah Anda secara lengkap..." required>{{ old('description') }}</textarea>
                            <div class="form-text">Semakin detail deskripsi Anda, semakin cepat teknisi dapat membantu.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-select" name="category_id" id="category_id" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }} (SLA: {{ $category->sla_hours }} jam)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Prioritas <span class="text-danger">*</span></label>
                                    <select class="form-select" name="priority" id="priority" required>
                                        <option value="">Pilih Prioritas</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low - Tidak
                                            Urgent</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium -
                                            Urgent</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High -
                                            Sangat Urgent</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lampiran (Opsional)</label>
                            <input type="file" class="form-control" name="attachments[]" id="attachments" multiple
                                accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                            <div class="form-text">Maksimal 5 file, ukuran masing-masing maksimal 5MB. Format: JPG, PNG,
                                PDF, DOC, DOCX</div>
                            <div id="attachmentPreview" class="mt-2"></div>
                        </div>

                        <div class="alert alert-info d-flex align-items-start gap-2">
                            <iconify-icon icon="solar:info-circle-linear" class="fs-5"></iconify-icon>
                            <div>
                                <strong>Catatan:</strong>
                                <ul class="mb-0 small">
                                    <li>Sistem akan otomatis mengklasifikasikan tiket Anda berdasarkan deskripsi</li>
                                    <li>Anda akan menerima notifikasi via email saat tiket diproses</li>
                                    <li>SLA akan dihitung berdasarkan kategori yang dipilih</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <iconify-icon icon="solar:send-linear" class="me-2"></iconify-icon>Kirim Permohonan
                            </button>
                            <a href="{{ route('admin.tickets.my') }}" class="btn btn-light">
                                <iconify-icon icon="solar:close-circle-linear" class="me-2"></iconify-icon>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0 fw-semibold">Asisten Chatbot</h5>
                        <span class="badge bg-success-subtle text-success rounded-pill px-2">Online</span>
                    </div>

                    <div id="chatContainer" class="chat-container"
                        style="height: 400px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; background: #f8f9fa;">
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <img src="{{ asset('assets/images/profile/user2.jpg') }}" class="rounded-circle"
                                    width="35" height="35" alt="Chatbot">
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <div class="bg-white rounded p-3 shadow-sm">
                                    <p class="mb-0 small">Halo! Saya adalah asisten chatbot AI. Silakan jelaskan masalah
                                        Anda dan saya akan membantu mengklasifikasikan ke kategori yang tepat.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="chatInput" placeholder="Ketik masalah Anda...">
                            <button type="button" class="btn btn-primary" onclick="sendMessage()">
                                <iconify-icon icon="mdi:send"></iconify-icon>
                            </button>
                        </div>
                    </div>

                    <div class="mt-3" id="chatbotPrediction" style="display: none;">
                        <div class="alert alert-success">
                            <div class="d-flex align-items-start gap-2">
                                <iconify-icon icon="solar:check-circle-linear" class="fs-5"></iconify-icon>
                                <div>
                                    <strong>Rekomendasi Kategori:</strong>
                                    <p class="mb-0" id="predictedCategory">-</p>
                                    <small class="text-muted">Confidence: <span id="confidenceScore">-</span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <h5 class="mb-3 fw-semibold">Tips Pengisian</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2 d-flex gap-2">
                            <iconify-icon icon="solar:check-circle-linear" class="text-success"></iconify-icon>
                            <span>Gunakan bahasa yang jelas dan spesifik</span>
                        </li>
                        <li class="mb-2 d-flex gap-2">
                            <iconify-icon icon="solar:check-circle-linear" class="text-success"></iconify-icon>
                            <span>Sertakan pesan error jika ada</span>
                        </li>
                        <li class="mb-2 d-flex gap-2">
                            <iconify-icon icon="solar:check-circle-linear" class="text-success"></iconify-icon>
                            <span>Lampirkan screenshot jika perlu</span>
                        </li>
                        <li class="mb-2 d-flex gap-2">
                            <iconify-icon icon="solar:check-circle-linear" class="text-success"></iconify-icon>
                            <span>Sebutkan spesifikasi perangkat jika relevan</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const chatContainer = document.getElementById('chatContainer');
        const chatInput = document.getElementById('chatInput');
        const categorySelect = document.getElementById('category_id');

        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });

        document.getElementById('attachments').addEventListener('change', function(e) {
            const files = e.target.files;
            const preview = document.getElementById('attachmentPreview');
            preview.innerHTML = '';

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileSize = (file.size / 1024).toFixed(2);
                preview.innerHTML += `
        <div class="d-flex align-items-center gap-2 mb-2 p-2 bg-light rounded">
          <iconify-icon icon="solar:file-linear" class="fs-5"></iconify-icon>
          <div class="flex-grow-1">
            <small class="fw-medium">${file.name}</small>
            <small class="text-muted d-block">${fileSize} KB</small>
          </div>
          <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="this.parentElement.remove()">
            <iconify-icon icon="solar:trash-bin-linear"></iconify-icon>
          </button>
        </div>
      `;
            }
        });

        function sendMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            addUserMessage(message);
            chatInput.value = '';

            setTimeout(() => {
                processChatbotPrediction(message);
            }, 500);
        }

        function addUserMessage(message) {
            const messageHtml = `
      <div class="d-flex mb-3 justify-content-end">
        <div class="flex-grow-1 me-2">
          <div class="bg-primary text-white rounded p-3">
            <p class="mb-0 small">${message}</p>
          </div>
        </div>
        <div class="flex-shrink-0">
          <img src="{{ asset('assets/images/profile/user1.jpg') }}" class="rounded-circle" width="35" height="35">
        </div>
      </div>
    `;
            chatContainer.insertAdjacentHTML('beforeend', messageHtml);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function addBotMessage(message) {
            const messageHtml = `
      <div class="d-flex mb-3">
        <div class="flex-shrink-0">
          <img src="{{ asset('assets/images/profile/user2.jpg') }}" class="rounded-circle" width="35" height="35" alt="Chatbot">
        </div>
        <div class="flex-grow-1 ms-2">
          <div class="bg-white rounded p-3 shadow-sm">
            <p class="mb-0 small">${message}</p>
          </div>
        </div>
      </div>
    `;
            chatContainer.insertAdjacentHTML('beforeend', messageHtml);
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function processChatbotPrediction(message) {
            fetch('/admin/chatbot/predict', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        query: message
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('AI Response:', data);

                    if (!data.success) {
                        addBotMessage('Maaf, layanan AI tidak tersedia. Silakan pilih kategori secara manual.');
                        return;
                    }

                    addBotMessage(`
    Berdasarkan deskripsi Anda, saya merekomendasikan kategori:
    <strong>${data.category_name}</strong>
  `);

                    const predictionDiv = document.getElementById('chatbotPrediction');
                    predictionDiv.style.display = 'block';

                    document.getElementById('predictedCategory').textContent =
                        data.category_name;

                    document.getElementById('confidenceScore').textContent =
                        data.confidence_score + '%';

                    // Auto select kategori
                    const selectedOption = categorySelect.querySelector(
                        `option[value="${data.category_id}"]`
                    );

                    if (selectedOption) {
                        categorySelect.value = String(data.category_id);

                        // trigger change event
                        categorySelect.dispatchEvent(new Event('change'));
                    } else {
                        console.error('Category ID tidak ditemukan:', data.category_id);

                        addBotMessage(
                            'Kategori hasil AI tidak ditemukan di sistem.'
                        );
                    }

                    categorySelect.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                })
                .catch(error => {
                    console.error('Prediction error:', error);
                    addBotMessage(
                        'Maaf, terjadi kesalahan saat memproses prediksi. Silakan pilih kategori secara manual.');
                });
        }
    </script>
@endpush
