@extends('layouts.app')

@section('title', 'Knowledge Base - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Knowledge Base AI')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-0 fw-semibold">Data Latih Chatbot (Knowledge Base)</h5>
                            <p class="text-muted mb-0 small">Kelola data untuk pelatihan model Naive Bayes</p>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success me-2" onclick="exportDataset()" id="exportBtn">
                                <iconify-icon icon="mdi:download" class="me-2"></iconify-icon>Export Dataset
                            </button>
                            <button type="button" class="btn btn-warning me-2" onclick="openTrainModal()" id="openTrainBtn">
                                <iconify-icon icon="mdi:brain" class="me-2"></iconify-icon>Latih Model
                            </button>
                            <button type="button" class="btn btn-primary" onclick="openCreateModal()" id="addKnowledgeBtn">
                                <iconify-icon icon="mdi:plus-circle" class="me-2"></iconify-icon>Tambah Data
                            </button>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 bg-primary-subtle">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle p-2">
                                        <iconify-icon icon="mdi:database" class="text-primary fs-4"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $totalData }}</h6>
                                        <p class="text-muted mb-0 small">Total Data Latih</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 bg-success-subtle">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle p-2">
                                        <iconify-icon icon="mdi:check-circle" class="text-success fs-4"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $totalCategories }}</h6>
                                        <p class="text-muted mb-0 small">Kategori</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 bg-warning-subtle">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle p-2">
                                        <iconify-icon icon="mdi:clock" class="text-warning fs-4"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $lastTrained }}</h6>
                                        <p class="text-muted mb-0 small">Terakhir Dilatih</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="border rounded-3 p-3 bg-info-subtle">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white rounded-circle p-2">
                                        <iconify-icon icon="mdi:chart-bar" class="text-info fs-4"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $modelAccuracy }}%</h6>
                                        <p class="text-muted mb-0 small">Akurasi Model</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-warning d-flex align-items-start gap-2">
                        <iconify-icon icon="mdi:alert" class="fs-5"></iconify-icon>
                        <div>
                            <strong>Penting:</strong>
                            <ul class="mb-0 small">
                                <li>Data latih ini akan digunakan untuk melatih model Naive Bayes</li>
                                <li>Semakin banyak data latih yang relevan, semakin akurat prediksi chatbot</li>
                                <li>Gunakan bahasa yang natural seperti yang digunakan user saat mengirimkan keluhan</li>
                                <li>Klik tombol "Latih Model" di dashboard setelah menambah data latih baru</li>
                            </ul>
                        </div>
                    </div>

                    <div id="loadingState" style="display: none;">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3 mb-0">Memuat data latih...</p>
                        </div>
                    </div>

                    <div id="knowledgeContent">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Teks Asli</th>
                                        <th>Kategori</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($knowledgeBase as $kb)
                                        <tr class="knowledge-item" data-id="{{ $kb->id }}">
                                            <td>#{{ $kb->id }}</td>
                                            <td>
                                                <p class="mb-0 small">{{ Str::limit($kb->original_text, 100) }}</p>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                                    {{ $kb->category->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-light btn-sm"
                                                        onclick="openEditModal({{ $kb->id }}, '{{ $kb->original_text }}', {{ $kb->category_id }})"
                                                        title="Edit">
                                                        <iconify-icon icon="mdi:pencil"></iconify-icon>
                                                    </button>
                                                    <button type="button" class="btn btn-light btn-sm text-danger"
                                                        onclick="deleteKnowledge({{ $kb->id }})" title="Hapus">
                                                        <iconify-icon icon="mdi:delete"></iconify-icon>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-5">
                                                <div class="text-muted">
                                                    <iconify-icon icon="mdi:book-open-variant"
                                                        class="fs-1 d-block mb-3"></iconify-icon>
                                                    <p>Belum ada data latih</p>
                                                    <button type="button" class="btn btn-primary btn-sm mt-2"
                                                        onclick="openCreateModal()">
                                                        Tambah Data Sekarang
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <p class="text-muted mb-0 small">Menampilkan {{ $knowledgeBase->count() }} data</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="knowledgeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Data Latih</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="knowledgeForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="knowledge_id" id="knowledgeId">
                        <input type="hidden" name="_method" value="POST" id="formMethod">
                        <div class="mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="category_id" id="knowledgeCategory" required>
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teks Asli (Keluhan) <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="original_text" id="knowledgeText" rows="4" required
                                placeholder="Masukkan teks keluhan seperti yang biasa dikirimkan user..."></textarea>
                            <div class="form-text">Gunakan bahasa yang natural seperti yang digunakan user</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal"
                            id="cancelBtn">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <iconify-icon icon="mdi:send" class="me-2" id="submitIcon"></iconify-icon>
                            <span id="submitText">Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="trainModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Latih Model AI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="trainContent">
                        <div class="text-center">
                            <div class="bg-primary-subtle rounded-circle p-4 d-inline-block mb-3">
                                <iconify-icon icon="mdi:cpu-64-bit" class="text-primary fs-1"></iconify-icon>
                            </div>
                            <p class="mb-3">Model akan dilatih menggunakan {{ $totalData }} data latih yang tersedia.
                            </p>
                            <div class="alert alert-info">
                                <strong>Proses Pelatihan:</strong>
                                <ul class="mb-0 small text-start">
                                    <li>Export data ke CSV</li>
                                    <li>Preprocessing (Tokenization, Stopword Removal, Stemming)</li>
                                    <li>Training Naive Bayes dengan Scikit-Learn</li>
                                    <li>Save model.pkl dan vectorizer.pkl</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="trainProgress" style="display: none;">
                        <div class="text-center">
                            <div class="spinner-border text-primary mb-3" role="status"></div>
                            <p class="mb-0">Sedang melatih model, mohon tunggu...</p>
                        </div>
                    </div>
                    <div id="trainResult" style="display: none;">
                        <div class="text-center">
                            <div class="bg-success-subtle rounded-circle p-4 d-inline-block mb-3">
                                <iconify-icon icon="mdi:check-circle" class="text-success fs-1"></iconify-icon>
                            </div>
                            <h6 class="mb-2">Pelatihan Selesai!</h6>
                            <p class="text-muted mb-0" id="trainResultText">Model berhasil dilatih.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="trainButtons">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="trainModel()" id="trainBtn">
                        <iconify-icon icon="mdi:refresh" class="me-2" id="trainIcon"></iconify-icon>
                        <span id="trainText">Mulai Latih</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <div id="toast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function openCreateModal() {
            console.log("test lagi");
            document.getElementById('modalTitle').textContent = 'Tambah Data Latih';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('knowledgeId').value = '';
            document.getElementById('knowledgeCategory').value = '';
            document.getElementById('knowledgeText').value = '';
            new bootstrap.Modal(document.getElementById('knowledgeModal')).show();
        }

        function openEditModal(id, text, categoryId) {
            document.getElementById('modalTitle').textContent = 'Edit Data Latih';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('knowledgeId').value = id;
            document.getElementById('knowledgeCategory').value = categoryId;
            document.getElementById('knowledgeText').value = text;
            new bootstrap.Modal(document.getElementById('knowledgeModal')).show();
        }

        function deleteKnowledge(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data latih ini?')) {
                setLoading(true);

                fetch('{{ route('knowledge.destroy', ':id') }}'.replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json()).then(data => {
                        showToast('success', data.message || 'Data latih berhasil dihapus');

                        const item = document.querySelector(`.knowledge-item[data-id="${id}"]`);

                        if (item) {
                            item.style.opacity = '0';

                            setTimeout(() => {
                                item.remove();
                            }, 300);
                        }
                    })
                    .catch(error => {
                        showToast('error', 'Gagal menghapus data latih. Silakan coba lagi.');
                    })
                    .finally(() => {
                        setLoading(false);
                    });
            }
        }

        function exportDataset() {
            setLoading('export', true);

            fetch('/admin/export-dataset', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    showToast('success', data.message || 'Dataset berhasil diexport');
                })
                .catch(error => {
                    showToast('error', 'Terjadi kesalahan saat export dataset.');
                })
                .finally(() => {
                    setLoading('export', false);
                });
        }

        function openTrainModal() {
            document.getElementById('trainContent').style.display = 'block';
            document.getElementById('trainProgress').style.display = 'none';
            document.getElementById('trainResult').style.display = 'none';
            document.getElementById('trainButtons').style.display = 'block';
            new bootstrap.Modal(document.getElementById('trainModal')).show();
        }

        function trainModel() {
            const trainBtn = document.getElementById('trainBtn');
            const trainText = document.getElementById('trainText');
            const trainIcon = document.getElementById('trainIcon');

            document.getElementById('trainContent').style.display = 'none';
            document.getElementById('trainProgress').style.display = 'block';
            document.getElementById('trainButtons').style.display = 'none';
            // trainText.textContent = 'Melatih...';
            trainIcon.setAttribute('icon', 'mdi:refresh');

            fetch('/admin/train-model', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('trainProgress').style.display = 'none';
                    document.getElementById('trainResult').style.display = 'block';
                    document.getElementById('trainResultText').textContent = data.message || 'Model berhasil dilatih!';
                    document.getElementById('trainButtons').innerHTML = `
        <button type="button" class="btn btn-primary" onclick="location.reload()">Tutup</button>
      `;
                    showToast('success', data.message || 'Model AI berhasil dilatih');
                })
                .catch(error => {
                    document.getElementById('trainProgress').style.display = 'none';
                    document.getElementById('trainContent').style.display = 'block';
                    document.getElementById('trainButtons').style.display = 'block';
                    trainText.textContent = 'Mulai Latih';
                    trainIcon.setAttribute('icon', 'mdi:refresh');
                    showToast('error', 'Terjadi kesalahan saat melatih model.');
                });
        }

        document.getElementById('knowledgeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log("test");

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitIcon = document.getElementById('submitIcon');
            const cancelBtn = document.getElementById('cancelBtn');

            const formData = new FormData(this);
            const knowledgeId = formData.get('knowledge_id');
            const method = formData.get('_method');
            const url = method === 'PUT' ?
                '{{ route('knowledge.update', ':id') }}'.replace(':id', knowledgeId) :
                '{{ route('knowledge.store') }}';

            submitBtn.disabled = true;
            cancelBtn.disabled = true;
            submitText.textContent = method === 'PUT' ? 'Memperbarui...' : 'Menyimpan...';
            submitIcon.setAttribute('icon', 'mdi:refresh');

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': method
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    bootstrap.Modal.getInstance(document.getElementById('knowledgeModal')).hide();
                    showToast('success', data.message || (method === 'PUT' ? 'Data latih berhasil diperbarui' :
                        'Data latih berhasil ditambahkan'));
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(error => {
                    showToast('error', 'Gagal menyimpan data latih. Silakan coba lagi.');
                    submitBtn.disabled = false;
                    cancelBtn.disabled = false;
                    submitText.textContent = method === 'PUT' ? 'Simpan Perubahan' : 'Simpan';
                    submitIcon.setAttribute('icon', 'mdi:send');
                });
        });

        function showToast(type, message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');

            toast.className = `toast align-items-center text-white border-0 bg-${
                type === 'success' ? 'success' : 'danger'
            }`;

            toastMessage.textContent = message;

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        function setLoading(type, loading) {
            if (type === 'export') {
                const btn = document.getElementById('exportBtn');
                if (btn) {
                    btn.disabled = loading;
                    btn.innerHTML = loading ?
                        '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...' :
                        '<iconify-icon icon="mdi:download" class="me-2"></iconify-icon>Export Dataset';
                }
            } else if (type === 'train') {
                const btn = document.getElementById('trainBtn');
                if (btn) {
                    btn.disabled = loading;
                    btn.innerHTML = loading ?
                        '<span class="spinner-border spinner-border-sm me-2"></span>Melatih...' :
                        '<iconify-icon icon="mdi:brain" class="me-2"></iconify-icon>Latih Model';
                }
            } else {
                const btn = document.getElementById('addKnowledgeBtn');
                if (btn) {
                    btn.disabled = loading;
                    btn.innerHTML = loading ?
                        '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...' :
                        '<iconify-icon icon="mdi:plus-circle" class="me-2"></iconify-icon>Tambah Data';
                }
            }
        }
    </script>
@endpush
