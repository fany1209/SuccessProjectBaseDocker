{{--user
11/08/25
stefany 
--}}
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
        <h1 class="badge badge-success">Users</h1>
@stop

@section('content')
<div>
    {{-- Mostrar el formulario solo si $open es true --}}
    @if ($open)
    <div class="card">
        <div class="card-header">
            <div class="row d-flex justify-content-between align-items-center">
                @if ($create)
                    <h5 class="mb-0"><b>Add User</b></h5>
                @else
                    <h5 class="mb-0"><b>Edit User</b></h5>
                @endif

                <a href="{{ route('admin.users.index') }}" class="btn btn-danger">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

    <form method="POST" action="{{ $create ? route('admin.users.store') : route('admin.users.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @if (!$create) @method('PUT') @endif

        <div class="card-body">
            {{-- Name, Email, Password, Role --}}
            <div class="row">
                <div class="form-group col-6">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $user->name ?? '') }}">
                </div>
                <div class="form-group col-6">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $user->email ?? '') }}">
                </div>
            </div>

            @if (!$create)
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="new_pwd_checkbox" name="new_pwd" {{ old('new_pwd') ? 'checked' : '' }}>
                    <label for="new_pwd_checkbox">Change Password</label>
                </div>
            @endif

            <div class="row">
                <div class="form-group col-6">
                    <label>Password</label>
                    <input type="password" min="8" class="form-control" name="password" {{ ($create || old('new_pwd')) ? '' : 'disabled' }}>
                </div>
                <div class="form-group col-6">
                    <label>Confirm Password</label>
                    <input type="password" min="8" class="form-control" name="password_confirmation" {{ ($create || old('new_pwd')) ? '' : 'disabled' }}>
                </div>
            </div>

            {{-- Role --}}
            <div class="form-group">
                <label>Role</label>
                <select class="form-control" name="role_id">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ (old('role_id', $user->roles->first()->id ?? '') == $role->id) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Upload new photo --}}
            <div class="form-group">
                <label>Upload New Photo</label>
                <input type="file" class="form-control" name="profile_photo" id="profile_photo">
                <div id="preview-container" style="display:none;">
                    <img id="preview-image" width="128" alt="Preview" class="mb-2">
                    <button type="button" class="btn btn-sm btn-outline-danger" id="remove-preview">Remove</button>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success">Save</button>
        </div>
    </form>
@endif


    {{-- Form separado SOLO para eliminar la foto actual --}}
    @if (!$create && !empty($user->profile_photo_path))
        <div class="mt-2">
            <img src="{{ Storage::url($user->profile_photo_path) }}" width="128" alt="Current Photo">
            <form action="{{ route('admin.users.removePhoto', $user->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('Are you sure you want to remove this photo?')">
                    <i class="fas fa-trash-alt"></i> Remove Photo
                </button>
            </form>
        </div>
    @endif
    
    <div class="d-flex flex-row mb-2">
        <form id="searchForm" method="GET" action="{{ route('admin.users.index') }}" class="d-flex w-100">
            <input 
                type="text" 
                id="searchInput"
                class="form-control" 
                name="search" 
                placeholder="Search" 
                value="{{ request('search') }}"
            >
            <button type="submit" class="btn btn-success ml-2">
                <i class="fas fa-search"></i>
            </button>
        </form>

        <a href="{{ route('admin.users.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Since</th>
                    <th>Photo</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge badge-info">{{ $user->getRoleNames()->first() }}</span></td>
                        <td> {{ $user->created_at ? $user->created_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>
                            @if ($user->profile_photo_path)
                                <img src="{{ Storage::url($user->profile_photo_path) }}" alt="User Photo" class="img-circle" width="64">
                            @else
                                <span>No Photo</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-row">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger ml-1">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>

        <div class="table-responsive mt-2">
            {{ $users->appends(request()->except('page'))->links('bootstrap-links') }}
        </div>
    </div>
</div>
@stop
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '{{ session('success') }}',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session('error') }}',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false
    });
</script>
@endif

<script>
    // Confirmación SweetAlert para borrar usuario
    document.querySelectorAll('form.d-inline').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let input = document.getElementById('searchInput');
    let form = document.getElementById('searchForm');
    let timeout = null;

    input.addEventListener('keyup', function () {
        clearTimeout(timeout);
        timeout = setTimeout(function () {
            form.submit();
        }, 500); // medio segundo de espera después de escribir
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('profile_photo');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const removeBtn = document.getElementById('remove-preview');

    input.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });

    removeBtn.addEventListener('click', function() {
        previewImage.src = '';
        previewContainer.style.display = 'none';
        input.value = '';
    });
});
</script>

@stop
