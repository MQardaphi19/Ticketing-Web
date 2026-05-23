@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Manage Roles and Permissions</h1>

    <style>
        .role-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .role-card h5 {
            margin-bottom: 10px;
        }
        .permission-list {
            list-style: none;
            padding-left: 0;
        }
        .permission-list li {
            margin-bottom: 5px;
        }
    </style>

    <div class="row">
        @foreach($roles as $role)
            <div class="col-md-6">
                <div class="role-card">
                    <h5>{{ $role->name }}</h5>
                    <ul class="permission-list">
                        @foreach($permissions as $permission)
                            <li>
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input permission-checkbox" 
                                           data-role-id="{{ $role->id }}" 
                                           value="{{ $permission->name }}"
                                           @if($role->permissions->contains($permission)) checked @endif>
                                    <label class="form-check-label">{{ $permission->name }}</label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const roleId = this.dataset.roleId;
            const permissions = Array.from(document.querySelectorAll(`.permission-checkbox[data-role-id="${roleId}"]:checked`))
                .map(cb => cb.value);

            fetch(`/roles/${roleId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ permissions })
            })
            .then(response => response.json())
            .then(data => alert(data.message));
        });
    });
</script>
@endsection