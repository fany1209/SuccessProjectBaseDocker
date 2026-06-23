{{-- trailers
13/08/25
stefany
--}}
@extends('adminlte::page')

@section('title', 'Trailers')

@section('content_header')
    <h1 class="badge badge-success">Trailers</h1>
@stop

@section('content')
<div>
    {{-- Formulario de creación/edición --}}
    @if ($open)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Trailer' : 'Edit Trailer' }}</b></h5>
            <a href="{{ route('admin.trailers.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <form method="POST" action="{{ $create 
        ? route('admin.trailers.store') 
        : (isset($trailer) ? route('admin.trailers.update', $trailer->trailer_id) : '#') }}">

            @csrf
            @if (!$create)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label>Plate</label>
                    <input type="text" class="form-control" name="plate" value="{{ old('plate', $trailer->plate ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Transport Line</label>
                    <select class="form-control" name="transport_line_id" required>
                        @foreach($transport_lines as $line)
                            <option value="{{ $line->transport_line_id }}" {{ old('transport_line_id', $trailer->transport_line_id ?? '') == $line->transport_line_id ? 'selected' : '' }}>
                                {{ $line->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Unit Number</label>
                    <input type="text" class="form-control" name="unit_number" value="{{ old('unit_number', $trailer->unit_number ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <input type="text" class="form-control" name="type" value="{{ old('type', $trailer->type ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Color</label>
                    <input type="text" class="form-control" name="color" value="{{ old('color', $trailer->color ?? '') }}">
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Barra de búsqueda y botón agregar --}}
    <div class="d-flex flex-row mb-2">
        <form method="GET" action="{{ route('admin.trailers.index') }}" id="search-form" class="d-flex w-100">
            <input type="text" id="search-input" name="search" class="form-control" placeholder="Search Plate" value="{{ request('search') }}">
            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>

        <a href="{{ route('admin.trailers.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Tabla de trailers --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Transport Line</th>
                    <th>Plate</th>
                    <th>Unit Number</th>
                    <th>Type</th>
                    <th>Color</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($trailers as $t)
                <tr>
                    <td>{{ $t->transportLine->name ?? '-' }}</td>
                    <td>{{ $t->plate }}</td>
                    <td>{{ $t->unit_number }}</td>
                    <td>{{ $t->type }}</td>
                    <td>{{ $t->color }}</td>
                    <td>
                        <a href="{{ route('admin.trailers.edit', $t->trailer_id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.trailers.destroy', $t->trailer_id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm ml-1"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-2">
            {{ $trailers->appends(request()->except('page'))->links('bootstrap-links') }}
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
            html: errorMessages
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

    // Búsqueda automática
    const input = document.getElementById('search-input');
    const form = document.getElementById('search-form');
    let timeout = null;

    input.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => { form.submit(); }, 500);
    });

    input.addEventListener('keydown', function() {
        clearTimeout(timeout);
    });
});
</script>
@stop
