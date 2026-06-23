{{-- prospects
13/08/25
stefany
--}}
@extends('adminlte::page')

@section('title', 'Prospects')

@section('content_header')
    <h1 class="badge badge-success">Prospects</h1>
@stop

@section('content')
<div>
    {{-- Formulario de creación/edición --}}
    @if ($open)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Prospect' : 'Edit Prospect' }}</b></h5>
            <a href="{{ route('admin.prospects.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <form method="POST" action="{{ $create 
            ? route('admin.prospects.store') 
            : (isset($prospect) ? route('admin.prospects.update', $prospect->prospect_id) : '#') }}">
            @csrf
            @if (!$create)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $prospect->name ?? '') }}" required>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone', $prospect->phone ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $prospect->email ?? '') }}">
                </div>

                <div class="form-group">
                    <label>RFC</label>
                    <input type="text" class="form-control" name="rfc" value="{{ old('rfc', $prospect->rfc ?? '') }}">
                </div>

                <div class="form-group">
                    <label>District</label>
                    <input type="text" class="form-control" name="district" value="{{ old('district', $prospect->district ?? '') }}">
                </div>

                <div class="form-group">
                    <label>City</label>
                    <input type="text" class="form-control" name="city" value="{{ old('city', $prospect->city ?? '') }}">
                </div>

                <div class="form-group">
                    <label>State</label>
                    <input type="text" class="form-control" name="state" value="{{ old('state', $prospect->state ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <input type="text" class="form-control" name="address" value="{{ old('address', $prospect->address ?? '') }}">
                </div>

                <div class="form-group">
                    <label>Sector</label>
                    <select name="sector_id" class="form-control">
                        @foreach($sectors as $s)
                            <option value="{{ $s->sector_id }}" {{ old('sector_id', $prospect->sector_id ?? '') == $s->sector_id ? 'selected' : '' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
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
    <form method="GET" action="{{ route('admin.prospects.index') }}" id="search-form">
        <div class="d-flex flex-row mb-2">
            <div class="d-flex w-100">
                <input type="text" id="search-input" name="search" class="form-control" placeholder="Search Name" value="{{ request('search') }}">
                <button type="button" class="btn btn-secondary ml-2" data-toggle="collapse" data-target="#advancedFilters" title="Advanced Filters">
                    <i class="fas fa-filter"></i>
                </button>
                <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
            </div>
            <a href="{{ route('admin.prospects.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
        </div>

        {{-- Advanced Filters Collapse --}}
        <div class="collapse mb-3 {{ request('sector_id') || request('city') || request('state') ? 'show' : '' }}" id="advancedFilters">
            <div class="card card-body bg-light mb-0">
                <div class="row">
                    <div class="col-md-4">
                        <label>Sector</label>
                        <select name="sector_id" class="form-control" onchange="this.form.submit()">
                            <option value="">All Sectors</option>
                            @foreach($sectors as $s)
                                <option value="{{ $s->sector_id }}" {{ request('sector_id') == $s->sector_id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" placeholder="Filter by City" value="{{ request('city') }}">
                    </div>
                    <div class="col-md-4">
                        <label>State</label>
                        <input type="text" name="state" class="form-control" placeholder="Filter by State" value="{{ request('state') }}">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 text-right">
                        <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                        <a href="{{ route('admin.prospects.index') }}" class="btn btn-default btn-sm ml-1">Clear</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Tabla de prospects --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Rfc</th>
                    <th>Address</th>
                    <th>Since</th>
                    <th style="width: 120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prospects as $p)
                <tr>
                    <td>{{ $p->sector->name ?? '-' }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->phone ?? '-' }}</td>
                    <td>{{ $p->email ?? '-' }}</td>
                    <td>{{ $p->rfc ?? '-' }}</td>
                    <td>{{ $p->address ?? '-' }}</td>
                    <td>{{ $p->created_at ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.prospects.edit', $p->prospect_id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.prospects.destroy', $p->prospect_id) }}" method="POST" class="d-inline delete-prospect-form">
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
            {{ $prospects->appends(request()->except('page'))->links('bootstrap-links') }}
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

    $('.delete-prospect-form').submit(function(e) {
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
