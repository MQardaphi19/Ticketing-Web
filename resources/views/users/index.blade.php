@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Sistem Tiket Layanan Kominfo')

@section('page-title', 'Manajemen Pengguna')

@section('content')
<style> 

.premium-input{
    background: #ffffff !important;
    border: 2px solid #dbeafe !important;
    color: #172554 !important;
    font-weight: 500;
    min-height: 48px;
    transition: all .25s ease;
}

.premium-input:focus{
    border-color: #2563eb !important;
    box-shadow: 0 0 0 0.25rem rgba(37,99,235,.15) !important;
    background: #ffffff !important;
    color: #172554 !important;
}

.premium-input::placeholder{
    color: #94a3b8 !important;
}

.form-label{
    color: #172554 !important;
    font-weight: 600 !important;
    margin-bottom: 8px;
}

.form-select.premium-input{
    appearance: auto !important;
    -webkit-appearance: auto !important;
    -moz-appearance: auto !important;
    padding-right: 2.5rem !important;
}


/* ======================================
   RESET PASSWORD MODAL PREMIUM
====================================== */

.reset-password-modal{
    border-radius: 22px;
    overflow: hidden;
}

.reset-password-modal .modal-header{
    background: linear-gradient(
        135deg,
        #172554,
        #1e3a8a,
        #2563eb
    );

    padding: 22px 28px;
}

.reset-password-modal .modal-title{
    color: #fff !important;
    font-size: 1.2rem;
}

.reset-password-modal .modal-body{
    padding: 28px;
    background: #f8fafc;
}

.reset-password-modal .modal-footer{
    padding: 20px 28px;
    background: #ffffff;
}

.reset-user-info{
    display: flex;
    align-items: center;
    gap: 12px;

    background: #eff6ff;

    border: 1px solid #bfdbfe;

    border-radius: 14px;

    padding: 14px;

    margin-bottom: 20px;
}

.reset-user-info iconify-icon{
    font-size: 34px;
    color: #2563eb;
}

.premium-input{
    min-height: 50px;

    border-radius: 12px;

    border: 2px solid #dbeafe !important;

    color: #172554 !important;
}

.premium-input:focus{
    border-color: #2563eb !important;

    box-shadow: 0 0 0 .25rem rgba(37,99,235,.15) !important;
}

.form-label{
    font-weight: 600;
    color: #172554;
}

.reset-btn{
    background: linear-gradient(
        135deg,
        #172554,
        #2563eb
    ) !important;

    border: none !important;

    border-radius: 12px;

    min-width: 170px;

    height: 48px;

    font-weight: 600;
}

.reset-btn:hover{
    transform: translateY(-2px);

    box-shadow: 0 8px 20px rgba(37,99,235,.25);
}
</style>
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
                  <th>Dinas</th>
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
                          <a class="dropdown-item" href="#" onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->nip }}', '{{ $user->department }}', '{{ $user->phone }}', '{{ $user->role }}')">
                            <iconify-icon icon="solar:pen-linear" class="me-2"></iconify-icon>Edit
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" href="#" onclick="openResetPasswordModal({{ $user->id }}, @js($user->name))">
                            <iconify-icon icon="solar:lock-password-linear" class="me-2"></iconify-icon>Reset Password
                          </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <a class="dropdown-item text-danger" href="#" onclick="openDeleteUserModal({{ $user->id }})">
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
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

    <div class="modal-header border-0 text-white"
        style="background: linear-gradient(135deg, #172554 0%, #1e3a8a 50%, #2563eb 100%);">

        <div>
            <h5 class="modal-title fw-bold text-white" id="modalTitle">
                <iconify-icon icon="solar:user-plus-linear" class="me-2"></iconify-icon>
                Tambah Pengguna
            </h5>
            <small class="text-white-50">
                Tambahkan pengguna baru ke dalam sistem
            </small>
        </div>

        <button type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="modal"></button>
    </div>

    <form id="userForm">
        @csrf

        <div class="modal-body p-4 bg-light">

            <input type="hidden" name="user_id" id="userId">
            <input type="hidden" name="_method" value="POST" id="formMethod">

            <div class="row g-3">

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Nama Lengkap
                    </label>
                    <input type="text"
                        class="form-control rounded-3 premium-input"
                        name="name"
                        id="userName"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Email
                    </label>
                    <input type="email"
                        class="form-control rounded-3 premium-input"
                        name="email"
                        id="userEmail"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        NIP
                    </label>
                    <input type="text"
                        class="form-control rounded-3 premium-input"
                        name="nip"
                        id="userNip"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Nomor Telepon
                    </label>
                    <input type="text"
                        class="form-control rounded-3 premium-input"
                        name="phone"
                        id="userPhone"
                        required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Dinas
                    </label>

                    <select class="form-select rounded-3 premium-input"
                        name="department"
                        id="userDepartment"
                        required>

                        <option value="">Pilih Dinas</option>
                        <option value="Dinas Pendidikan">Dinas Pendidikan</option>
                        <option value="Dinas Kesehatan">Dinas Kesehatan</option>
                        <option value="Dinas BKPSDM">Dinas BKPSDM</option>
                        <option value="Dinas Sosial">Dinas Sosial</option>
                        <option value="Dinas Dukcapil">Dinas Dukcapil</option>
                        <option value="Inspektorat">Inspektorat</option>
                        <option value="Dinas Perizinan">Dinas Perizinan</option>
                        <option value="Dinas Kecamatan">Dinas Kecamatan</option>
                        <option value="Dinas Bappelitbangda">Dinas Bappelitbangda</option>
                        <option value="Dinas Kominfo">Dinas Kominfo</option>

                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">
                        Role
                    </label>

                    <select class="form-select rounded-3 premium-input"
                        name="role"
                        id="userRole"
                        required>

                        <option value="">Pilih Role</option>
                        <option value="pegawai-dinas">Pegawai Dinas</option>
                        <option value="kepala-diskominfo">Kepala Dinas</option>
                        <option value="admin">Admin</option>

                    </select>
                </div>

                <div class="col-md-6" id="passwordField">
                    <label class="form-label fw-semibold">
                        Password
                    </label>

                    <input type="password"
                        class="form-control rounded-3 premium-input"
                        name="password"
                        id="userPassword"
                        required>
                </div>

                <div class="col-md-6" id="passwordConfirmationField">
                    <label class="form-label fw-semibold">
                        Konfirmasi Password
                    </label>

                    <input type="password"
                        class="form-control rounded-3 premium-input"
                        name="password_confirmation"
                        id="userPasswordConfirmation"
                        required>
                </div>

            </div>

        </div>

        <div class="modal-footer border-0 bg-white px-4 pb-4">

            <button type="button"
                class="btn btn-light px-4 rounded-3"
                data-bs-dismiss="modal"
                id="cancelBtn">
                Batal
            </button>

            <button type="submit"
                class="btn text-white px-4 rounded-3"
                id="submitBtn"
                style="background: linear-gradient(135deg,#172554,#2563eb);">

                <iconify-icon
                    icon="solar:paper-plane-linear"
                    class="me-2"
                    id="submitIcon">
                </iconify-icon>

                <span id="submitText">
                    Simpan
                </span>

            </button>

        </div>

    </form>

</div>
  </div>
</div>

<div class="modal fade" id="resetPasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-lg reset-password-modal">

    <div class="modal-header border-0">
        <h5 class="modal-title fw-bold text-white">
            <iconify-icon icon="solar:lock-password-linear" class="me-2"></iconify-icon>
            Reset Password
        </h5>

        <button type="button"
            class="btn-close btn-close-white"
            data-bs-dismiss="modal">
        </button>
    </div>

    <form id="resetPasswordForm">
        @csrf

        <div class="modal-body">

            <input type="hidden" name="user_id" id="resetUserId">

            <div class="reset-user-info">
                <iconify-icon icon="solar:user-circle-linear"></iconify-icon>

                <div>
                    <small class="text-muted d-block">
                        Reset password untuk pengguna
                    </small>

                    <span class="fw-semibold" id="resetUserName"></span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    Password Baru
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="password"
                    class="form-control premium-input"
                    name="password"
                    id="resetPassword"
                    minlength="8"
                    required>
            </div>

            <div class="mb-0">
                <label class="form-label">
                    Konfirmasi Password Baru
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="password"
                    class="form-control premium-input"
                    name="password_confirmation"
                    id="resetPasswordConfirmation"
                    minlength="8"
                    required>
            </div>

        </div>

        <div class="modal-footer border-0">

            <button
                type="button"
                class="btn btn-light px-4"
                data-bs-dismiss="modal"
                id="resetCancelBtn">
                Batal
            </button>

            <button
                type="submit"
                class="btn btn-primary reset-btn"
                id="resetSubmitBtn">

                <iconify-icon
                    icon="solar:lock-password-linear"
                    class="me-2"
                    id="resetSubmitIcon">
                </iconify-icon>

                <span id="resetSubmitText">
                    Reset Password
                </span>

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

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-body text-center p-5">

                <div class="mb-4">
                    <div class="mx-auto d-flex align-items-center justify-content-center"
                         style="width:90px;height:90px;border-radius:50%;
                         background:rgba(220,53,69,.12);">
                        <iconify-icon
                            icon="solar:trash-bin-trash-bold"
                            width="50"
                            class="text-danger">
                        </iconify-icon>
                    </div>
                </div>

                <h4 class="fw-bold mb-2">
                    Hapus Pengguna?
                </h4>

                <p class="text-muted mb-4">
                    Pengguna yang dihapus tidak dapat dikembalikan kembali.
                    Pastikan Anda benar-benar ingin menghapus data ini.
                </p>

                <input type="hidden" id="deleteUserId">

                <div class="d-flex justify-content-center gap-2">

                    <button
                        type="button"
                        class="btn btn-light px-4"
                        data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button
                        type="button"
                        class="btn btn-danger px-4"
                        onclick="confirmDeleteUser()">
                        <iconify-icon
                            icon="solar:trash-bin-trash-linear"
                            class="me-1">
                        </iconify-icon>
                        Hapus
                    </button>

                </div>

            </div>

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
    document.getElementById('userPassword').value = '';
    document.getElementById('userPasswordConfirmation').value = '';
    
    document.getElementById('passwordField').style.display = 'block';
    document.getElementById('passwordConfirmationField').style.display = 'block';
    document.getElementById('userPassword').required = true;
    document.getElementById('userPasswordConfirmation').required = true;
    
    new bootstrap.Modal(document.getElementById('userModal')).show();
  }

  function openEditModal(id, name, email, nip, department, phone, role) {
    document.getElementById('modalTitle').textContent = 'Edit Pengguna';
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('userId').value = id;
    document.getElementById('userName').value = name;
    document.getElementById('userEmail').value = email;
    document.getElementById('userNip').value = nip;
    document.getElementById('userPhone').value = phone;
    document.getElementById('userDepartment').value = department;
    document.getElementById('userRole').value = role;
    document.getElementById('userPassword').value = '';
    document.getElementById('userPasswordConfirmation').value = '';
    
    document.getElementById('passwordField').style.display = 'none';
    document.getElementById('passwordConfirmationField').style.display = 'none';
    document.getElementById('userPassword').required = false;
    document.getElementById('userPasswordConfirmation').required = false;
    
    new bootstrap.Modal(document.getElementById('userModal')).show();
  }

  function openDeleteUserModal(id) {
    document.getElementById('deleteUserId').value = id;

    new bootstrap.Modal(
        document.getElementById('deleteUserModal')
    ).show();
}

function confirmDeleteUser() {

    const id = document.getElementById('deleteUserId').value;

    fetch('{{ route('users.destroy', ':id') }}'.replace(':id', id), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(handleJsonResponse)
    .then(data => {

        bootstrap.Modal
            .getInstance(document.getElementById('deleteUserModal'))
            .hide();

        showToast(
            'success',
            data.message || 'Pengguna berhasil dihapus'
        );

        const item = document.querySelector(
            `.user-item[data-id="${id}"]`
        );

        if (item) {
            item.style.opacity = '0';

            setTimeout(() => {
                item.remove();
            }, 300);
        }
    })
    .catch(error => {
        showToast(
            'error',
            error.message || 'Gagal menghapus pengguna'
        );
    });
}

  function openResetPasswordModal(id, name) {
    document.getElementById('resetUserId').value = id;
    document.getElementById('resetUserName').textContent = name;
    document.getElementById('resetPassword').value = '';
    document.getElementById('resetPasswordConfirmation').value = '';
    document.getElementById('resetSubmitBtn').disabled = false;
    document.getElementById('resetCancelBtn').disabled = false;
    document.getElementById('resetSubmitText').textContent = 'Reset Password';
    document.getElementById('resetSubmitIcon').setAttribute('icon', 'solar:lock-password-linear');

    new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
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
      ? '{{ route('users.update', ':id') }}'.replace(':id', userId)
      : '{{ route('users.store') }}';
    
    submitBtn.disabled = true;
    cancelBtn.disabled = true;
    submitText.textContent = method === 'PUT' ? 'Memperbarui...' : 'Menyimpan...';
    submitIcon.setAttribute('icon', 'solar:refresh-linear');
    
    fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'X-HTTP-Method-Override': method
      },
      body: formData
    })
    .then(handleJsonResponse)
    .then(data => {
      bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
      showToast('success', data.message || (method === 'PUT' ? 'Pengguna berhasil diperbarui' : 'Pengguna berhasil ditambahkan'));
      setTimeout(() => location.reload(), 1000);
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('error', error.message || 'Gagal menyimpan pengguna. Silakan coba lagi.');
      submitBtn.disabled = false;
      cancelBtn.disabled = false;
      submitText.textContent = method === 'PUT' ? 'Simpan Perubahan' : 'Simpan';
      submitIcon.setAttribute('icon', 'solar:paper-plane-linear');
    });
  });

  document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('resetSubmitBtn');
    const submitText = document.getElementById('resetSubmitText');
    const submitIcon = document.getElementById('resetSubmitIcon');
    const cancelBtn = document.getElementById('resetCancelBtn');
    const userId = document.getElementById('resetUserId').value;
    const formData = new FormData(this);

    submitBtn.disabled = true;
    cancelBtn.disabled = true;
    submitText.textContent = 'Mereset...';
    submitIcon.setAttribute('icon', 'solar:refresh-linear');

    fetch('{{ route('users.password.reset', ':id') }}'.replace(':id', userId), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
        'X-HTTP-Method-Override': 'PUT'
      },
      body: formData
    })
    .then(handleJsonResponse)
    .then(data => {
      bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
      showToast('success', data.message || 'Password pengguna berhasil direset');
    })
    .catch(error => {
      console.error('Error:', error);
      showToast('error', error.message || 'Gagal mereset password. Silakan coba lagi.');
      submitBtn.disabled = false;
      cancelBtn.disabled = false;
      submitText.textContent = 'Reset Password';
      submitIcon.setAttribute('icon', 'solar:lock-password-linear');
    });
  });

  function handleJsonResponse(response) {
    return response.json().then(data => {
      if (!response.ok) {
        const errors = data.errors ? Object.values(data.errors).flat().join(' ') : '';
        throw new Error(errors || data.message || 'Request gagal diproses.');
      }

      return data;
    });
  }

  function showToast(type, message) {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    toast.className = `toast align-items-center text-white border-0 bg-${type === 'success' ? 'success' : type === 'info' ? 'info' : 'danger'}`;
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
