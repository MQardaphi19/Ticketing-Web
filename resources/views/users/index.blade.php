@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="row">
  <div class="col-lg-12">
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h5 class="mb-0 fw-semibold">Daftar Pengguna</h5>
            <p class="text-muted mb-0 small">Kelola pengguna sistem dan hak aksesnya</p>
          </div>
          <button type="button" class="btn btn-primary" onclick="openCreateModal()" id="addUserBtn">
            <iconify-icon icon="solar:user-plus-linear" class="me-2"></iconify-icon>Tambah Pengguna
          </button>
        </div>

        <div id="loadingState" style="display: none;">
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-3 mb-0">Memuat data pengguna...</p>
          </div>
        </div>

        <div id="usersContent">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="selectAll">
                    </div>
                  </th>
                  <th>Nama</th>
                  <th>NIP</th>
                  <th>Departemen</th>
                  <th>Role</th>
                  <th>Tiket</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($users as $user)
                <tr class="user-item" data-id="{{ $user->id }}">
                  <td>
                    <div class="form-check">
                      <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img src="{{ asset('assets/images/profile/user1.jpg') }}" class="rounded-circle" width="40" height="40">
                      <div>
                        <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                        <p class="text-muted mb-0 small">{{ $user->email }}</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    <span class="small">{{ $user->nip }}</span>
                  </td>
                  <td>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2">{{ $user->department }}</span>
                  </td>
                  <td>
                    <span class="badge @if($user->role == 'admin' || $user->role == 'super-admin') bg-danger-subtle text-danger @elseif($user->role == 'teknisi') bg-warning-subtle text-warning @else bg-success-subtle text-success @endif rounded-pill px-2">
                      {{ ucfirst(str_replace('-', ' ', $user->role)) }}
                    </span>
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="small text-muted">Dibuat: {{ $user->tickets_count }}</span>
                      <span class="small text-muted">Ditugaskan: {{ $user->assigned_tickets_count }}</span>
                    </div>
                  </td>
                  <td>
                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                      <iconify-icon icon="solar:check-circle-linear" class="me-1"></iconify-icon>Aktif
                    </span>
                  </td>
                  <td>
                    <div class="dropdown">
                      <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <iconify-icon icon="solar:menu-dots-circle-linear"></iconify-icon>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" href="#" onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->nip }}', '{{ $user->department }}', '{{ $user->phone }}')">
                            <iconify-icon icon="solar:pen-linear" class="me-2"></iconify-icon>Edit
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#" onclick="showToast('info', 'Fitur reset password akan segera tersedia.')">
                            <iconify-icon icon="solar:lock-password-linear" class="me-2"></iconify-icon>Reset Password
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#" onclick="showToast('info', 'Fitur kelola role akan segera tersedia.')">
                            <iconify-icon icon="solar:shield-linear" class="me-2"></iconify-icon>Kelola Role
                          </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <a class="dropdown-item text-danger" href="#" onclick="deleteUser({{ $user->id }})">
                            <iconify-icon icon="solar:trash-bin-linear" class="me-2"></iconify-icon>Hapus
                          </a>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <p class="text-muted mb-0 small">Menampilkan {{ $users->count() }} pengguna</p>
            <nav>
              <ul class="pagination mb-0">
                <li class="page-item disabled">
                  <a class="page-link" href="#" tabindex="-1">Sebelumnya</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                  <a class="page-link" href="#">Selanjutnya</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Tambah Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="userForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="user_id" id="userId">
          <input type="hidden" name="_method" value="POST" id="formMethod">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="name" id="userName" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email <span class="text-danger">*</span></label>
              <input type="email" class="form-control" name="email" id="userEmail" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">NIP <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="nip" id="userNip" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
              <input type="text" class="form-control" name="phone" id="userPhone" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Departemen <span class="text-danger">*</span></label>
              <select class="form-select" name="department" id="userDepartment" required>
                <option value="">Pilih Departemen</option>
                <option value="TIK">TIK</option>
                <option value="Pelayanan Publik">Pelayanan Publik</option>
                <option value="Informasi dan Komunikasi">Informasi dan Komunikasi</option>
                <option value="Administrasi">Administrasi</option>
                <option value="Keuangan">Keuangan</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role <span class="text-danger">*</span></label>
              <select class="form-select" name="role" id="userRole" required>
                <option value="">Pilih Role</option>
                <option value="pemohon">Pemohon</option>
                <option value="teknisi">Teknisi</option>
                <option value="admin">Admin</option>
                <option value="super-admin">Super Admin</option>
              </select>
            </div>
            <div class="col-md-6" id="passwordField">
              <label class="form-label">Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="password" id="userPassword" required>
            </div>
            <div class="col-md-6" id="passwordConfirmationField">
              <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" name="password_confirmation" id="userPasswordConfirmation" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal" id="cancelBtn">Batal</button>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <iconify-icon icon="solar:paper-plane-linear" class="me-2" id="submitIcon"></iconify-icon>
            <span id="submitText">Simpan</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
  <div id="toast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body" id="toastMessage"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
      checkbox.checked = this.checked;
    });
  });

  function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Pengguna';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('userId').value = '';
    document.getElementById('userName').value = '';
    document.getElementById('userEmail').value = '';
    document.getElementById('userNip').value = '';
    document.getElementById('userPhone').value = '';
    document.getElementById('userDepartment').value = '';
    document.getElementById('userRole').value = '';
    
    document.getElementById('passwordField').style.display = 'block';
    document.getElementById('passwordConfirmationField').style.display = 'block';
    document.getElementById('userPassword').required = true;
    document.getElementById('userPasswordConfirmation').required = true;
    
    new bootstrap.Modal(document.getElementById('userModal')).show();
  }

  function openEditModal(id, name, email, nip, department, phone) {
    document.getElementById('modalTitle').textContent = 'Edit Pengguna';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('userId').value = id;
    document.getElementById('userName').value = name;
    document.getElementById('userEmail').value = email;
    document.getElementById('userNip').value = nip;
    document.getElementById('userPhone').value = phone;
    document.getElementById('userDepartment').value = department;
    document.getElementById('userRole').value = '';
    
    document.getElementById('passwordField').style.display = 'none';
    document.getElementById('passwordConfirmationField').style.display = 'none';
    document.getElementById('userPassword').required = false;
    document.getElementById('userPasswordConfirmation').required = false;
    
    new bootstrap.Modal(document.getElementById('userModal')).show();
  }

  function deleteUser(id) {
    if (confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')) {
      setLoading(true);
      
      fetch('{{ route('admin.users.destroy', ':id') }}'.replace(':id', id), {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        showToast('success', data.message || 'Pengguna berhasil dihapus');
        const item = document.querySelector(.user-item[data-id="${id}"]);
        if (item) {
          item.style.opacity = '0';
          setTimeout(() => item.remove(), 300);
        }
      })
      .catch(error => {
        showToast('error', 'Gagal menghapus pengguna. Silakan coba lagi.');
      })
      .finally(() => {
        setLoading(false);
      });
    }
  }

  document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitIcon = document.getElementById('submitIcon');
    const cancelBtn = document.getElementById('cancelBtn');
    
    const formData = new FormData(this);
    const userId = formData.get('user_id');
    const method = formData.get('_method');
    const url = method === 'PUT' 
      ? '{{ route('admin.users.update', ':id') }}'.replace(':id', userId)
      : '{{ route('admin.users.store') }}';
    
    submitBtn.disabled = true;
    cancelBtn.disabled = true;
    submitText.textContent = method === 'PUT' ? 'Memperbarui...' : 'Menyimpan...';
    submitIcon.setAttribute('icon', 'solar:refresh-linear');
    
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
      bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
      showToast('success', data.message || (method === 'PUT' ? 'Pengguna berhasil diperbarui' : 'Pengguna berhasil ditambahkan'));
      setTimeout(() => location.reload(), 1000);
    })
    .catch(error => {
      showToast('error', 'Gagal menyimpan pengguna. Silakan coba lagi.');
      submitBtn.disabled = false;
      cancelBtn.disabled = false;
      submitText.textContent = method === 'PUT' ? 'Simpan Perubahan' : 'Simpan';
      submitIcon.setAttribute('icon', 'solar:paper-plane-linear');
    });
  });

  function showToast(type, message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    toast.className = toast align-items-center text-white border-0 bg-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'};
    toastMessage.textContent = message;
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
  }

  function setLoading(loading) {
    const btn = document.getElementById('addUserBtn');
    if (btn) {
      btn.disabled = loading;
      btn.innerHTML = loading 
        ? '<span class="spinner-border spinner-border-sm me-2"></span>Memuat...'
        : '<iconify-icon icon="solar:user-plus-linear" class="me-2"></iconify-icon>Tambah Pengguna';
    }
  }
</script>
@endpush