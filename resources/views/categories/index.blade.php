@extends('layouts.app')

@section('title', 'Manajemen Kategori - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Manajemen Kategori')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-0 fw-semibold">Daftar Kategori</h5>
                            <p class="text-muted mb-0 small">Kelola kategori layanan untuk klasifikasi tiket</p>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="openCreateModal()" id="addCategoryBtn">
                            <iconify-icon icon="mdi:plus-circle" class="me-2"></iconify-icon>Tambah Kategori
                        </button>
                    </div>

                    <div id="loadingState" style="display: none;">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status"></div>
                            <p class="mt-3 mb-0">Memuat data kategori...</p>
                        </div>
                    </div>

                    <div id="categoriesContent">
                        <div class="row g-4" id="categoriesList">
                            @foreach ($categories as $category)
                                <div class="col-lg-4 col-md-6 category-item" data-id="{{ $category->id }}">
                                    <div class="card border h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="bg-primary-subtle rounded-circle p-3">
                                                    <iconify-icon icon="solar:folder-linear"
                                                        class="text-primary fs-3"></iconify-icon>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-light btn-sm dropdown-toggle" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <iconify-icon icon="mdi:dots-circle-horizontal"></iconify-icon>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="#"
                                                                onclick='openEditModal({{ $category->id }}, {!! json_encode($category->name) !!}, {!! json_encode($category->slug) !!}, {!! json_encode($category->description) !!}, {{ $category->sla_hours }}); return false;'>
                                                                <iconify-icon icon="mdi:pencil"
                                                                    class="me-2"></iconify-icon>Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                onclick="deleteCategory({{ $category->id }})">
                                                                <iconify-icon icon="mdi:delete"
                                                                    class="me-2"></iconify-icon>Hapus
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <h6 class="fw-semibold mb-2">{{ $category->name }}</h6>
                                            <p class="text-muted small mb-3">
                                                {{ $category->description ?: 'Tidak ada deskripsi' }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">SLA:</small>
                                                    <span
                                                        class="badge bg-info-subtle text-info rounded-pill px-2">{{ $category->sla_hours }}
                                                        Jam</span>
                                                </div>
                                                <small class="text-muted">{{ $category->tickets_count ?? 0 }} Tiket</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-info mt-4">
                            <div class="d-flex align-items-start gap-2">
                                <iconify-icon icon="mdi:information" class="fs-5"></iconify-icon>
                                <div>
                                    <strong>Informasi:</strong>
                                    <ul class="mb-0 small">
                                        <li>Kategori digunakan untuk mengklasifikasikan tiket dan menentukan SLA (Service
                                            Level Agreement)</li>
                                        <li>SLA dalam jam menentukan batas waktu penyelesaian tiket</li>
                                        <li>Kategori tidak dapat dihapus jika masih ada tiket yang menggunakannya</li>
                                        <li>Perubahan kategori akan mempengaruhi pelatihan model AI Chatbot</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="categoryForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="category_id" id="categoryId">
                        <input type="hidden" name="_method" value="POST" id="formMethod">
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="categoryName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" class="form-control" name="slug" id="categorySlug" readonly>
                            <div class="form-text">Slug akan di-generate otomatis dari nama kategori</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="description" id="categoryDescription" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SLA (Jam) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="sla_hours" id="categorySla" required
                                min="1" max="168">
                            <div class="form-text">Target waktu penyelesaian dalam jam (maksimal 168 jam / 7 hari)</div>
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
        document.getElementById('categoryName').addEventListener('input', function() {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
            document.getElementById('categorySlug').value = slug;
        });

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Kategori';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('categoryId').value = '';
            document.getElementById('categoryName').value = '';
            document.getElementById('categorySlug').value = '';
            document.getElementById('categoryDescription').value = '';
            document.getElementById('categorySla').value = '';
            new bootstrap.Modal(document.getElementById('categoryModal')).show();
        }

        function openEditModal(id, name, slug, description, sla) {
            document.getElementById('modalTitle').textContent = 'Edit Kategori';
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('categoryId').value = id;
            document.getElementById('categoryName').value = name;
            document.getElementById('categorySlug').value = slug;
            document.getElementById('categoryDescription').value = description;
            document.getElementById('categorySla').value = sla;
            new bootstrap.Modal(document.getElementById('categoryModal')).show();
        }

        function deleteCategory(id) {
            if (confirm('Apakah Anda yakin ingin menghapus kategori ini?')) {
                setLoading(true);

                fetch('{{ route('categories.destroy', ':id') }}'.replace(':id', id), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Category delete response:', data);
                        showToast('success', data.message || 'Kategori berhasil dihapus');
                        const item = document.querySelector(`.category-item[data-id="${id}"]`);
                        if (item) {
                            item.style.opacity = '0';
                            setTimeout(() => item.remove(), 300);
                        }
                    })
                    .catch(error => {
                        showToast('error', 'Gagal menghapus kategori. Silakan coba lagi.');
                    })
                    .finally(() => {
                        setLoading(false);
                    });
            }
        }

        document.getElementById('categoryForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitIcon = document.getElementById('submitIcon');
            const cancelBtn = document.getElementById('cancelBtn');

            const formData = new FormData(this);
            const categoryId = formData.get('category_id');
            const method = formData.get('_method');
            const url = method === 'PUT' ?
                '{{ route('categories.update', ':id') }}'.replace(':id', categoryId) :
                '{{ route('categories.store') }}';

            submitBtn.disabled = true;
            cancelBtn.disabled = true;
            submitText.textContent = method === 'PUT' ? 'Memperbarui...' : 'Menyimpan...';
            submitIcon.setAttribute('icon', 'solar:refresh-linear');

            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-HTTP-Method-Override': method,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Category save response:', data);
                    bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
                    showToast('success', data.message || (method === 'PUT' ? 'Kategori berhasil diperbarui' :
                        'Kategori berhasil ditambahkan'));
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(error => {
                    showToast('error', 'Gagal menyimpan kategori. Silakan coba lagi.');
                    submitBtn.disabled = false;
                    cancelBtn.disabled = false;
                    submitText.textContent = method === 'PUT' ? 'Simpan Perubahan' : 'Simpan';
                    submitIcon.setAttribute('icon', 'solar:paper-plane-linear');
                });
        });

        function showToast(type, message) {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');

            toast.className =
            `toast align-items-center text-white border-0 bg-${type === 'success' ? 'success' : 'danger'}`;
            toastMessage.textContent = message;

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }

        function setLoading(loading) {
            const btn = document.getElementById('addCategoryBtn');
            if (btn) {
                btn.disabled = loading;
                btn.innerHTML = loading ?
                    '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...' :
                    '<iconify-icon icon="mdi:plus-circle" class="me-2"></iconify-icon>Tambah Kategori';
            }
        }
    </script>
@endpush
