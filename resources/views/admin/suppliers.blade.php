{{-- suppliers
13/08/25
stefany
Actualizado: Jacob - 09-09-2025
--}}
@extends('adminlte::page')

@section('title', 'Suppliers')

@section('content_header')
    <h1 class="badge badge-success">Suppliers</h1>
@stop

@section('content')
<div>
    {{-- Formulario de creación/edición --}}
    @if ($open)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Supplier' : 'Edit Supplier' }}</b></h5>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <form method="POST" action="{{ $create 
            ? route('admin.suppliers.store') 
            : route('admin.suppliers.update', $supplier->supplier_id) }}">
            @csrf
            @if (!$create)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $supplier->name ?? '') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Contact</label>
                        <input type="text" class="form-control" name="contact" value="{{ old('contact', $supplier->contact ?? '') }}" maxlength="150">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $supplier->email ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>RFC</label>
                        <input type="text" class="form-control" maxlength="13" name="rfc" value="{{ old('rfc', $supplier->rfc ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-3">
                        <label>District</label>
                        <input type="text" class="form-control" name="district" value="{{ old('district', $supplier->district ?? '') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" value="{{ old('city', $supplier->city ?? '') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>State</label>
                        <input type="text" class="form-control" name="state" value="{{ old('state', $supplier->state ?? '') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Sector</label>
                        <select class="form-control" name="sector_id">
                            @foreach($sectors as $sector)
                                <option value="{{ $sector->sector_id }}" {{ old('sector_id', $supplier->sector_id ?? '') == $sector->sector_id ? 'selected' : '' }}>
                                    {{ $sector->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" class="form-control" name="address" value="{{ old('address', $supplier->address ?? '') }}">
                </div>

                <input type="hidden" name="supplier_code" value="{{ $supplier->supplier_code ?? 'AUTO' }}">

                @if ($errors->any())
                    <div class="alert alert-danger mt-2">
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

    <div class="d-flex flex-row mb-2">
        <form method="GET" action="{{ route('admin.suppliers.index') }}" class="d-flex w-100" id="search-form">
            <input type="text" name="search" class="form-control" placeholder="Search Name, Contact or RFC" value="{{ request('search') }}" id="search-input">
            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>

        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Tabla de suppliers --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th> 
                    <th>Phone</th>
                    <th>Email</th>
                    <th>RFC</th>
                    <th>City</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($suppliers as $s)
                <tr>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->contact }}</td> 
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->email }}</td>
                    <td>{{ $s->rfc }}</td>
                    <td>{{ $s->city }}</td>
                    <td>
                        <a href="{{ route('admin.suppliers.edit', $s->supplier_id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.suppliers.destroy', $s->supplier_id) }}" method="POST" class="d-inline delete-form">
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
            {{ $suppliers->appends(request()->except('page'))->links('bootstrap-links') }}
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

    const input = document.getElementById('search-input');
    const form = document.getElementById('search-form');
    let timeout = null;

    if(input) {
        input.addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => { form.submit(); }, 700);
        });
    }
});
</script>
@stop
