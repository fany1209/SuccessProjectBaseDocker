{{-- warehouses
15/08/25
Stefany
--}}
{{-- warehouses --}}
@extends('adminlte::page')

@section('title', 'Warehouses')

@section('content_header')
    <h1 class="badge badge-success">Warehouses</h1>
@stop

@section('content')
<div>
    {{-- Formulario creación/edición --}}
    <div class="card" @if(!($open ?? false)) style="display:none;" @endif>
        <div class="card-header">
            <div class="row d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><b>{{ ($create ?? true) ? 'Add Warehouse' : 'Edit Warehouse' }}</b></h5>
                <a href="{{ route('admin.warehouses.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ ($create ?? true) ? route('admin.warehouses.store') : route('admin.warehouses.update', $warehouse->warehouse_id ?? 0) }}">
                @csrf
                @if(!($create ?? true))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" placeholder="Warehouse Name" 
                        value="{{ old('name', $warehouse->name ?? '') }}" required>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger mt-2">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card-footer mt-3">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Barra de búsqueda y botón agregar --}}
    <div class="d-flex flex-row mb-2">
        <form method="GET" action="{{ route('admin.warehouses.index') }}" class="d-flex w-100" id="search-form">
            <input type="text" class="form-control" name="search" placeholder="Search Name" value="{{ $search ?? '' }}" id="search-input">
            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Formulario tipo "modal" para generar archivo --}}
    <div class="card" @if(!($open2 ?? false)) style="display:none;" @endif>
    <div class="card-header">
        <h5>Generate File for {{ $modal_data['warehouse_name'] ?? '' }}</h5>
        <a href="{{ route('admin.warehouses.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($modal_data['warehouse_id']) ? route('admin.warehouses.generateFile', $modal_data['warehouse_id']) : '#' }}">

        @csrf
            <div class="row">
                <div class="form-group col-4">
                    <label>Week A</label>
                    <select name="week_a" class="form-control" required>
                        @foreach($weeks as $week)
                            <option value="{{ $week }}" 
                                {{ old('week_a', $modal_data['week_a'] ?? '') == $week ? 'selected' : '' }}>
                                {{ $week }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label>Week B</label>
                    <select name="week_b" class="form-control" required>
                        @foreach($weeks as $week)
                            <option value="{{ $week }}" 
                                {{ old('week_b', $modal_data['week_b'] ?? '') == $week ? 'selected' : '' }}>
                                {{ $week }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-4">
                    <label>Year</label>
                    <select name="year" class="form-control" required>
                        @foreach($years as $year)
                            <option value="{{ $year }}" 
                                {{ old('year', $modal_data['year'] ?? '') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger mt-2">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-footer mt-3">
                <button type="submit" class="btn btn-info">Generate File</button>
            </div>
        </form>
    </div>
</div>

    {{-- Tabla de warehouses --}}
    <div class="table-responsive mt-3">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($warehouses as $w)
                    <tr>
                        <td>{{ $w->name }}</td>
                        <td>
                            <a href="{{ route('admin.warehouses.edit', $w->warehouse_id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.warehouses.destroy', $w->warehouse_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger ml-1"><i class="fas fa-trash-alt"></i></button>
                            </form>

                            <a href="{{ route('admin.warehouses.modal', $w->warehouse_id) }}" class="btn btn-info ml-1">
                                <i class="fas fa-file-alt"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-2">
        {{ $warehouses->appends(request()->except('page'))->links('bootstrap-links') }}
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
            if(result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('search-input');
    const form = document.getElementById('search-form');
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
