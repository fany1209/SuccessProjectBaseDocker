{{-- categories
15/08/25
stefany
--}}
@extends('adminlte::page')

@section('title', 'Categories')

@section('content_header')
        <h1 class="badge badge-success">Categories-products</h1>
@stop

@section('content')
<div>
    {{-- Tarjeta formulario creación/edición --}}
    <div class="card" @if(!($open ?? false)) style="display:none;" @endif>
        <div class="card-header">
            <div class="row d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><b>{{ $create ? 'Add Category' : 'Edit Category' }}</b></h5>
                <a href="{{ route('admin.categories.products.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ $create ? route('admin.categories.products.store') : route('admin.categories.products.update', $category->category_id ?? 0) }}">
                @csrf
                @if (!$create)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="form-group col-12">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Category Name"
                            value="{{ old('name', $category->name ?? '') }}" required>
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

    {{-- Filtro de búsqueda --}}
    <div class="d-flex flex-row mb-2">
        <form method="GET" action="{{ route('admin.categories.products.index') }}" class="d-flex w-100" id="search-form">
            <input type="text" class="form-control" name="search" placeholder="Search Name"
                value="{{ request('search') }}" id="search-input">
            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>

        <a href="{{ route('admin.categories.products.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Tabla de categorías --}}
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($categories as $cat)
                    <tr>
                        <td>{{ $cat->name }}</td>
                        <td>
                            <a href="{{ route('admin.categories.products.edit', $cat) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.categories.products.destroy', $cat) }}" method="POST" class="d-inline delete-form">
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
        {{ $categories->appends(request()->except('page'))->links('bootstrap-links') }}
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
        }, 500);
    });

    input.addEventListener('keydown', function() {
        clearTimeout(timeout);
    });
});
</script>
@stop
