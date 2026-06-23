{{-- customers
13/08/25
stefany
Actualizado por: fany - 19-03-2026
--}}
@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
    <h1 class="badge badge-success">Customers</h1>
@stop

@section('content')
<div>
    {{-- Formulario de creación/edición --}}
    @if ($open)
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Customer' : 'Edit Customer' }}</b></h5>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <form method="POST" action="{{ $create 
            ? route('admin.customers.store') 
            : (isset($customer) ? route('admin.customers.update', $customer->customer_id) : '#') }}">
            @csrf
            @if (!$create)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Name (Fiscal)</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $customer->name ?? '') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Sector</label>
                        <select name="sector_id" class="form-control" id="sector_id">
                            <option value="">Select a sector</option>
                            @foreach ($sectors as $sector)
                                <option value="{{ $sector->sector_id }}" {{ old('sector_id', $customer->sector_id ?? '') == $sector->sector_id ? 'selected' : '' }}>
                                    {{ $sector->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Contact Person</label>
                        <input type="text" class="form-control" name="contact" value="{{ old('contact', $customer->contact ?? '') }}" placeholder="Full name of contact">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Vendedor</label>
                        <input type="text" class="form-control" name="vendedor" value="{{ old('vendedor', $customer->vendedor ?? (auth()->user()->name ?? '')) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" value="{{ old('phone', $customer->phone ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email', $customer->email ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>RFC</label>
                        <input type="text" class="form-control" maxlength="13" name="rfc" value="{{ old('rfc', $customer->rfc ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6">
                        <label>Fiscal Address</label>
                        <input type="text" class="form-control" name="address" value="{{ old('address', $customer->address ?? '') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>City</label>
                        <input type="text" class="form-control" name="city" value="{{ old('city', $customer->city ?? '') }}">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Postal Code</label>
                        <input type="text" class="form-control" name="postal_code" value="{{ old('postal_code', $customer->postal_code ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-4">
                        <label>District (Colonia)</label>
                        <input type="text" class="form-control" name="district" value="{{ old('district', $customer->district ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>State</label>
                        <input type="text" class="form-control" name="state" value="{{ old('state', $customer->state ?? '') }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Country</label>
                        <input type="text" class="form-control" name="country" value="{{ old('country', $customer->country ?? 'Mexico') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label>Delivery Address</label>
                    <input type="text" class="form-control" name="delivery_address" value="{{ old('delivery_address', $customer->delivery_address ?? '') }}" placeholder="Specify if different from fiscal address">
                </div>

                <input type="hidden" name="customer_code" value="{{ $customer->customer_code ?? 'AUTO' }}">

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

    <form method="GET" action="{{ route('admin.customers.index') }}" id="search-form">
        <div class="d-flex flex-row mb-2">
            <div class="d-flex w-100">
                <input type="text" class="form-control" name="search" id="search-input" placeholder="Search Customer by Name, RFC or Contact" value="{{ request('search') }}">
                <button type="button" class="btn btn-secondary ml-2" data-toggle="collapse" data-target="#advancedFilters" title="Advanced Filters">
                    <i class="fas fa-filter"></i>
                </button>
                <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
            </div>
            <a href="{{ route('admin.customers.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
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
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-default btn-sm ml-1">Clear</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Tabla de customers --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Phone</th>
                    <th>RFC</th>
                    <th>City/State</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $c)
                <tr>
                    <td><span class="badge badge-secondary">{{ $c->customer_code }}</span></td>
                    <td>{{ $c->name }}</td>
                    <td>{{ $c->contact ?? 'N/A' }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>{{ $c->rfc }}</td>
                    <td>{{ $c->city }}, {{ $c->state }}</td>
                    <td>
                        <a href="{{ route('admin.customers.edit', $c->customer_id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.customers.destroy', $c->customer_id) }}" method="POST" class="d-inline delete-form">
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
            {{ $customers->appends(request()->except('page'))->links('bootstrap-links') }}
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
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {
            if (result.isConfirmed) { form.submit(); }
        });
    });

    const input = document.getElementById('search-input');
    const form = document.getElementById('search-form');
    let timeout = null;

    if(input){
        input.addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => { form.submit(); }, 700);
        });
    }
});
</script>
@stop
