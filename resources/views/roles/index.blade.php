@extends('layouts.app')

@section('title', 'Hak Akses - Sistem Ticketing Layanan Kominfo')

@section('page-title', 'Hak Akses')

@section('content')
<div class="container-fluid">

    <style>
        .page-header {
            margin-bottom: 25px;
        }

        .page-title-custom {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 14px;
        }

        .role-card {
            border: none;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
            overflow: hidden;
            transition: all .3s ease;
            height: 100%;
        }

        .role-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
        }

        .role-header {
            background: linear-gradient(135deg, #0d6efd, #4f8cff);
            padding: 18px 20px;
            color: white;
        }

        .role-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .role-badge {
            background: rgba(255,255,255,.2);
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid rgba(255,255,255,.3);
        }

        .role-body {
            padding: 20px;
        }

        .permission-title {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .permission-item {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 10px;
            transition: all .2s ease;
            border: 1px solid transparent;
        }

        .permission-item:hover {
            background: #eef4ff;
            border-color: #d7e7ff;
        }

        .form-check {
            margin: 0;
        }

        .form-check-input {
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-check-label {
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #495057;
            width: 100%;
        }

        .stats-card {
            border: none;
            border-radius: 18px;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
            padding: 20px;
            margin-bottom: 25px;
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            color: #0d6efd;
        }

        .stats-label {
            color: #6c757d;
            font-size: 14px;
        }

        .loading-overlay {
            opacity: .6;
            pointer-events: none;
        }
    </style>

    <div class="page-header">
        <h2 class="page-title-custom">
            <iconify-icon icon="solar:shield-user-linear" class="me-2"></iconify-icon>
            Manajemen Hak Akses
        </h2>

        <div class="page-subtitle">
            Kelola Role dan Permission pengguna sistem ticketing
        </div>
    </div>

    <div class="row mb-4">

        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number">
                    {{ $roles->count() }}
                </div>
                <div class="stats-label">
                    Total Role
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number">
                    {{ $permissions->count() }}
                </div>
                <div class="stats-label">
                    Total Permission
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <div class="stats-number">
                    {{ $roles->sum(fn($role) => $role->permissions->count()) }}
                </div>
                <div class="stats-label">
                    Permission Terpasang
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">

        @foreach($roles as $role)

        <div class="col-lg-6">

            <div class="role-card" id="role-card-{{ $role->id }}">

                <div class="role-header d-flex justify-content-between align-items-center">

                    <h5>
                        <iconify-icon icon="solar:user-id-linear" class="me-2"></iconify-icon>
                        {{ ucfirst($role->name) }}
                    </h5>

                    <span class="role-badge">
                        {{ $role->permissions->count() }} Permission
                    </span>

                </div>

                <div class="role-body">

                    <div class="permission-title">
                        Centang permission yang diizinkan untuk role ini
                    </div>

                    @foreach($permissions as $permission)

                    <div class="permission-item">

                        <div class="form-check">

                            <input
                                type="checkbox"
                                class="form-check-input permission-checkbox"
                                data-role-id="{{ $role->id }}"
                                value="{{ $permission->name }}"
                                id="role{{ $role->id }}permission{{ $permission->id }}"
                                @if($role->permissions->contains($permission)) checked @endif
                            >

                            <label
                                class="form-check-label"
                                for="role{{ $role->id }}permission{{ $permission->id }}"
                            >
                                {{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}
                            </label>

                        </div>

                    </div>

                    @endforeach

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.permission-checkbox').forEach(checkbox => {

    checkbox.addEventListener('change', function() {

        const roleId = this.dataset.roleId;

        const roleCard = document.getElementById(`role-card-${roleId}`);

        roleCard.classList.add('loading-overlay');

        const permissions = Array.from(
            document.querySelectorAll(
                `.permission-checkbox[data-role-id="${roleId}"]:checked`
            )
        ).map(cb => cb.value);

        fetch(`/roles/${roleId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                permissions: permissions
            })
        })
        .then(response => response.json())
        .then(data => {

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });

        })
        .catch(error => {

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat menyimpan permission'
            });

            console.error(error);

        })
        .finally(() => {

            roleCard.classList.remove('loading-overlay');

        });

    });

});
</script>
@endsection