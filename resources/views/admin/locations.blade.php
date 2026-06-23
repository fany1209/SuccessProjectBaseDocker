{{--locations
12/08/25
stefany
--}}
@extends('adminlte::page')

@section('title', 'Locations')

@section('content_header')
    <h1 class="badge badge-success">Locations</h1>
@stop

@section('content')
<div>
    {{-- Tarjeta formulario creación/edición, se oculta si $open es false --}}
    <div class="card" @if(!($open ?? false)) style="display:none;" @endif>
        <div class="card-header">
            <div class="row d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><b>{{ $create ? 'Add Location' : 'Edit Location' }}</b></h5>
                <a href="{{ route('admin.locations.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
            </div>
        </div>

        <div class="card-body">
            @if ($warehouses->count() == 0)
                <div class="alert alert-primary" role="alert">
                    <strong>There are no Warehouses to assign a Location</strong>
                </div>
            @endif

            <form method="POST" action="{{ $create ? route('admin.locations.store') : route('admin.locations.update', $location->location_id ?? 0) }}">
                @csrf
                @if (!$create)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="form-group col-6">
                        <label>Warehouse</label>
                        <select name="warehouse_id" class="form-control" required>
                            <option value="">-- Select Warehouse --</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->warehouse_id }}"
                                    {{ old('warehouse_id', $location->warehouse_id ?? '') == $warehouse->warehouse_id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Location Name"
                            value="{{ old('name', $location->name ?? '') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        <label>Allergen</label>
                        <input type="text" class="form-control" name="allergen" placeholder="Allergen"
                            value="{{ old('allergen', $location->allergen ?? '') }}">
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mt-2">
                        <ul>
                            @foreach ($errors->all() as $error)
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

    {{-- Filtros de búsqueda y warehouse --}}
    <div class="d-flex flex-row mb-2">
             <form method="GET" action="{{ route('admin.locations.index') }}" class="d-flex w-100" id="search-form">
                <input type="text" class="form-control" name="search" placeholder="Search Name" value="{{ request('search') }}" id="search-input">

            <select name="warehouse_id" class="form-control ml-2" style="max-width: 200px;">
                <option value="">All Warehouses</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->warehouse_id }}" {{ request('warehouse_id') == $warehouse->warehouse_id ? 'selected' : '' }}>
                        {{ $warehouse->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>

        <a href="{{ route('admin.locations.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Tabla de locations --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Warehouse</th>
                    <th>Name</th>
                    <th>Allergen</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($locations as $loc)
                    <tr>
                        <td>{{ $loc->warehouse->name ?? 'N/A' }}</td>
                        <td>{{ $loc->name }}</td>
                        <td>{{ $loc->allergen ?? '-' }}</td>
                        <td>
                            <a href="{{ route('admin.locations.edit', $loc) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.locations.destroy', $loc) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger ml-1"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    <div class="mt-2">
        {{ $locations->appends(request()->except('page'))->links('bootstrap-links') }}
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
    const input = document.getElementById('search-input');
    const form = document.getElementById('search-form');
    let timeout = null;

    input.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            form.submit();
        }, 500);  // espera 500ms después de que el usuario deja de escribir
    });

    input.addEventListener('keydown', function() {
        clearTimeout(timeout);
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('search-form');
    const warehouseSelect = form.querySelector('select[name="warehouse_id"]');

    warehouseSelect.addEventListener('change', function() {
        form.submit();
    });
});
</script>


@stop
