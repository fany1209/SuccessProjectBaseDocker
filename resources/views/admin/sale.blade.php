
{{--sales
14/08/25
stefany 
--}}
@extends('adminlte::page')

@section('title', 'Sales')

@section('content_header')
    <h1 class="badge badge-success">Sales</h1>
@stop

@section('content')
<div>
    {{-- Barra de búsqueda y filtro --}}
    <form method="GET" action="{{ route('admin.sales.index') }}" id="search-form">
        <div class="d-flex flex-row mb-2">
            <div class="d-flex w-100">
                <select name="customers_filter" class="form-control mr-2" style="max-width: 200px;" onchange="this.form.submit()">
                    <option value="prospects" {{ $customers_filter == 'prospects' ? 'selected' : '' }}>Prospects</option>
                    <option value="customers" {{ $customers_filter == 'customers' ? 'selected' : '' }}>Customers</option>
                </select>

                <input type="text" name="search" class="form-control" placeholder="Search Name" value="{{ $search }}" id="search-input">
                <button type="button" class="btn btn-secondary ml-2" data-toggle="collapse" data-target="#advancedFilters" title="Advanced Filters">
                    <i class="fas fa-filter"></i>
                </button>
                <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
            </div>
        </div>

        {{-- Advanced Filters Collapse --}}
        <div class="collapse mb-3 {{ request('seller') || request('sale_type') || request('date_from') || request('date_to') ? 'show' : '' }}" id="advancedFilters">
            <div class="card card-body bg-light mb-0">
                <div class="row">
                    <div class="col-md-3">
                        <label>Seller</label>
                        <input type="text" name="seller" class="form-control" placeholder="Filter by Seller" value="{{ request('seller') }}">
                    </div>
                    <div class="col-md-3">
                        <label>Sale Type</label>
                        <input type="text" name="sale_type" class="form-control" placeholder="Filter by Sale Type" value="{{ request('sale_type') }}">
                    </div>
                    <div class="col-md-3">
                        <label>From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label>To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12 text-right">
                        <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                        <a href="{{ route('admin.sales.index') }}" class="btn btn-default btn-sm ml-1">Clear</a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Tabla de sales --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Seller</th>
                    <th>First Time</th>
                    <th>Customer / Prospect</th>
                    <th>Purchase Order</th>
                    <th>Invoice</th>
                    <th>Sale Type</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                <tr>
                    <td>{{ $sale->sale_id }}</td>
                    <td>{{ $sale->seller }}</td>
                    <td>{{ $sale->first_time ? 'Yes' : 'No' }}</td>
                    <td>{{ $sale->name ?? '-' }}</td>
                    <td>{{ $sale->purchase_order }}</td>
                    <td>{{ $sale->invoice }}</td>
                    <td>{{ $sale->sale_type }}</td>
                    <td>{{ $sale->date }}</td>
                    <td>
                        <form action="{{ route('sales.destroy', $sale->sale_id) }}" method="POST" class="d-inline delete-form">
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
            {{ $sales->appends(request()->except('page'))->links('bootstrap-links') }}
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
