{{--employees
11/08/25
stefany 
--}}
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="badge badge-success">Employees</h1>
@stop

@section('content')
<div>
    {{-- Suppose $open and $create come from the controller --}}
    @if ($open)
    <div class="card">
        <div class="card-header">
            <div class="row d-flex justify-content-between align-items-center">
                @if ($create)
                    <h5 class="mb-0"><b>Add Employee</b></h5>
                @else
                    <h5 class="mb-0"><b>Edit Employee</b></h5>
                @endif

                <a href="{{ route('admin.employees.index') }}" class="btn btn-danger">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <form method="POST" action="{{ $create ? route('admin.employees.store') : route('admin.employees.update', $employee->employee_id) }}">
            @csrf
            @if (!$create)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row">
                    <div class="form-group col-12 col-sm-4">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Name" value="{{ old('name', $employee->name ?? '') }}">
                    </div>
                    <div class="form-group col-12 col-sm-4">
                        <label>Last Name</label>
                        <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="{{ old('last_name', $employee->last_name ?? '') }}">
                    </div>
                    <div class="form-group col-12 col-sm-4">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" placeholder="Phone" value="{{ old('phone', $employee->phone ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-12 col-sm-6">
                        <label>RFC</label>
                        <input type="text" class="form-control" maxlength="13" name="rfc" placeholder="RFC" value="{{ old('rfc', $employee->rfc ?? '') }}">
                    </div>
                    <div class="form-group col-12 col-sm-6">
                        <label>CURP</label>
                        <input type="text" class="form-control" maxlength="18" name="curp" placeholder="CURP" value="{{ old('curp', $employee->curp ?? '') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12 col-sm-6">
                        <label>Address</label>
                        <input type="text" class="form-control" name="address" placeholder="Address" value="{{ old('address', $employee->address ?? '') }}">
                    </div>
                    <div class="form-group col-12 col-sm-6">
                        <label>District</label>
                        <input type="text" class="form-control" name="district" placeholder="District" value="{{ old('district', $employee->district ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-12 col-sm-6">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" placeholder="City" value="{{ old('city', $employee->city ?? '') }}">
                    </div>
                    <div class="form-group col-12 col-sm-6">
                        <label>State</label>
                        <input type="text" class="form-control" name="state" placeholder="State" value="{{ old('state', $employee->state ?? '') }}">
                    </div>
                </div>


                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
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
    @endif

    {{-- Search and Add button --}}
    <div class="d-flex flex-row mb-2">
        <form method="GET" action="{{ route('admin.employees.index') }}" class="d-flex w-100">
            <input type="text" class="form-control" name="search" placeholder="Search Name" value="{{ request('search') }}">
            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>

        <a href="{{ route('admin.employees.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Employees table --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>RFC</th>
                    <th>CURP</th>
                    <th>Address</th>
                    <th>District</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Since</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($employees as $employee)
                    <tr>
                        <td>{{ $employee->name }} {{ $employee->last_name }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>{{ $employee->rfc }}</td>
                        <td>{{ $employee->curp }}</td>
                        <td>{{ $employee->address }}</td>
                        <td>{{ $employee->district }}</td>
                        <td>{{ $employee->city }}</td>
                        <td>{{ $employee->state }}</td>
                        <td>{{ $employee->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.employees.edit', $employee->employee_id) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>

                            <form action="{{ route('admin.employees.destroy', $employee->employee_id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger ml-1">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="table-responsive mt-2">
            {{ $employees->links('bootstrap-links') }}
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Mostrar SweetAlert de éxito desde sesión
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 2500,
            showConfirmButton: false
        });
    @endif

    // Mostrar SweetAlert errores de validación
    @if ($errors->any())
        let errorMessages = `@foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach`;
        Swal.fire({
            icon: 'error',
            title: 'Form errors',
            html: errorMessages,
        });
    @endif

    // Confirmación SweetAlert antes de borrar
    $('.delete-form').submit(function(e) {
        e.preventDefault();
        const form = this;

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
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

    // Búsqueda con debounce (500ms) y botón funcionando
    let typingTimer;
    const doneTypingInterval = 500;
    const $searchInput = $('form.d-flex.w-100 input[name="search"]');
    const $searchForm = $('form.d-flex.w-100');

    $searchInput.on('keyup', function() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            $searchForm.submit();
        }, doneTypingInterval);
    });

    $searchInput.on('keydown', function() {
        clearTimeout(typingTimer);
    });
});
</script>
@stop
