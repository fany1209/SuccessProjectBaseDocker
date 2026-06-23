{{--transport-lines
13/08/25
stefany 
--}}
{{-- transport-lines --}}
@extends('adminlte::page')

@section('title', 'Transport Lines')

@section('content_header')
        <h1 class="badge badge-success">Transport Lines</h1>
@stop

@section('content')
<div>
    {{-- Formulario crear/editar --}}
    <div class="card" @if(!($open ?? false)) style="display:none;" @endif>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Transport Line' : 'Edit Transport Line' }}</b></h5>
            <a href="{{ route('admin.transport-lines.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <div class="card-body">
                <form method="POST" action="{{ $create ? route('admin.transport-lines.store') : ($transport_line ? route('admin.transport-lines.update', $transport_line->transport_line_id) : '#') }}">
                @csrf
                @if(!$create)
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Name" 
                           value="{{ old('name', $transport_line->name ?? '') }}" required>
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

                <div class="card-footer mt-2">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Barra de búsqueda y botón agregar --}}
    <div class="d-flex flex-row mb-2">
        <form method="GET" action="{{ route('admin.transport-lines.index') }}" class="d-flex w-100" id="search-form">
            <input type="text" name="search" class="form-control" placeholder="Search Name" id="search-input"
                   value="{{ request('search') }}">
            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>

        <a href="{{ route('admin.transport-lines.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Tabla --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transport_lines as $line)
                    <tr>
                        <td>{{ $line->name }}</td>
                        <td class="d-flex">
                            <a href="{{ route('admin.transport-lines.edit', $line) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.transport-lines.destroy', $line) }}" method="POST" class="d-inline ms-1 delete-form">
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
            {{ $transport_lines->appends(request()->except('page'))->links('bootstrap-links') }}
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
        Swal.fire({icon: 'success', title: 'Success!', text: '{{ session('success') }}', timer: 2500, showConfirmButton: false});
    @endif

    @if ($errors->any())
        let errorMessages = `@foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach`;
        Swal.fire({icon: 'error', title: 'Form errors', html: errorMessages});
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
            if (result.isConfirmed) { form.submit(); }
        });
    });

    // Búsqueda automática
    const input = document.getElementById('search-input');
    const form = document.getElementById('search-form');
    let timeout = null;

    input.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => { form.submit(); }, 500);
    });
    input.addEventListener('keydown', function() { clearTimeout(timeout); });
});
</script>
@stop
