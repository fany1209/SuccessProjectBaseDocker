{{--roles
11/08/25
stefany 
--}}
{{-- roles.blade.php --}}
@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <h1 class="badge badge-success">Roles</h1>
@stop

@section('content')
<div>
    {{-- Formulario creación/edición --}}
    <div class="card" @if(!($open ?? false)) style="display:none;" @endif>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Role' : 'Edit Role' }}</b></h5>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <form method="POST" action="{{ $create ? route('admin.roles.store') : route('admin.roles.update', $role->id ?? 0) }}">
            @csrf
            @if (!$create)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Role Name"
                        value="{{ old('name', $role->name ?? '') }}" required>
                </div>

                <div class="form-group mt-2">
                    <label>Permissions</label>
                    <div class="overflow-auto border p-2" style="max-height: 200px;">
                        @foreach ($permissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    name="permissions[]"
                                    value="{{ $permission->id }}"
                                    id="perm{{ $permission->id }}"
                                    @if(in_array($permission->id, $rolePermissions ?? [])) checked @endif>
                                <label class="form-check-label" for="perm{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mt-2">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>

    <div class="d-flex flex-row mb-2">
    <form method="GET" action="{{ route('admin.roles.index') }}" class="d-flex w-100" id="search-form">
        <input type="text" class="form-control" name="search" placeholder="Search Name" value="{{ request('search') }}" id="search-input">
        <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
    </form>

        <a href="{{ route('admin.roles.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Permissions</th>
                    <th>Since</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($roles as $roleItem)
                    <tr>
                        <td>{{ $roleItem->name }}</td>
                        <td>
                            @foreach ($roleItem->permissions as $perm)
                                <span class="badge badge-info m-1">{{ $perm->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            {{ $roleItem->created_at ? $roleItem->created_at->format('Y-m-d') : 'N/A' }}
                        </td>
                        <td class="d-flex">
                            <a href="{{ route('admin.roles.edit', $roleItem->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.roles.destroy', $roleItem->id) }}" method="POST" class="ms-1 delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-2">
            {{ $roles->appends(request()->except('page'))->links('bootstrap-links') }}
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 2500,
            showConfirmButton: false
        });
    @endif

    @if ($errors->any())
        let errorMessages = `@foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach`;
        Swal.fire({
            icon: 'error',
            title: 'Form errors',
            html: errorMessages,
        });
    @endif

    $('.delete-form').submit(function(e) {
        e.preventDefault();
        const form = this;
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('search-form');
    const input = document.getElementById('search-input');
    let timeout = null;

    input.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            form.submit();
        }, 500);
    });

    input.addEventListener('keydown', function() {
        clearTimeout(timeout);
    });
});
</script>
@stop
