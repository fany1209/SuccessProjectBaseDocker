{{-- vehicles
13/08/25
stefany
--}}
@extends('adminlte::page')

@section('title', 'Vehicles')

@section('content_header')
        <h1 class="badge badge-success">Vehicles</h1>
@stop

@section('content')
<div>
    {{-- Formulario de creación/edición --}}
    @if ($open)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Vehicle' : 'Edit Vehicle' }}</b></h5>
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <form method="POST" action="{{ $create 
            ? route('admin.vehicles.store') 
            : (isset($vehicle) ? route('admin.vehicles.update', $vehicle->vehicle_id) : '#') }}">
            
            @csrf
            @if (!$create)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label>Plate</label>
                    <input type="text" class="form-control" name="plate" value="{{ old('plate', $vehicle->plate ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Transport Line</label>
                    <select class="form-control" name="transport_line_id" required>
                        @foreach($transport_lines as $line)
                            <option value="{{ $line->transport_line_id }}" {{ old('transport_line_id', $vehicle->transport_line_id ?? '') == $line->transport_line_id ? 'selected' : '' }}>
                                {{ $line->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Unit Number</label>
                    <input type="text" class="form-control" name="unit_number" value="{{ old('unit_number', $vehicle->unit_number ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <input type="text" class="form-control" name="type" value="{{ old('type', $vehicle->type ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Color</label>
                    <input type="text" class="form-control" name="color" value="{{ old('color', $vehicle->color ?? '') }}">
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Barra de búsqueda y botón agregar --}}
    <div class="d-flex flex-row mb-2">
        <form method="GET" action="{{ route('admin.vehicles.index') }}" class="d-flex w-100" id="search-form">
            <input type="text" name="search" class="form-control" placeholder="Search Plate" value="{{ request('search') }}" id="search-input">
            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>

        <a href="{{ route('admin.vehicles.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Tabla de vehicles --}}
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
                @foreach ($vehicles as $vehicle)
                <tr>
                    <td>{{ $vehicle->transportLine->name ?? '-' }}</td>
                    <td>{{ $vehicle->plate }}</td>
                    <td>{{ $vehicle->unit_number }}</td>
                    <td>{{ $vehicle->type }}</td>
                    <td>{{ $vehicle->color }}</td>
                    <td>
                        <a href="{{ route('admin.vehicles.edit', $vehicle->vehicle_id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                     <form action="{{ route('admin.vehicles.destroy', $vehicle->vehicle_id) }}" method="POST" class="d-inline delete-vehicle-form">
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
            {{ $vehicles->appends(request()->except('page'))->links('bootstrap-links') }}
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

    $('.delete-vehicle-form').submit(function(e) {
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
