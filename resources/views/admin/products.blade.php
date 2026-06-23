{{--products
15/08/25
stefany 
--}}
@extends('adminlte::page')

@section('title', 'Products')

@section('content_header')
    <h1 class="badge badge-success">Products</h1>
@stop

@section('content')
<div>

    {{-- Formulario creación/edición --}}
    <div class="card" @if(!($open ?? false)) style="display:none;" @endif>
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><b>{{ $create ? 'Add Product' : 'Edit Product' }}</b></h5>
            <a href="{{ route('admin.products.index') }}" class="btn btn-danger"><i class="fas fa-times"></i></a>
        </div>

        <div class="card-body">
            @if ($categories->count() == 0)
                <div class="alert alert-primary">
                    <strong>There are no Categories to assign a Product</strong>
                </div>
            @endif

            <form method="POST" action="{{ $create ? route('admin.products.store') : route('admin.products.update', $product->product_id ?? 0) }}" enctype="multipart/form-data">
                @csrf
                @if (!$create)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="form-group col-6">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->category_id }}"
                                    {{ old('category_id', $product->category_id ?? '') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Product Name"
                            value="{{ old('name', $product->name ?? '') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        <label>SKU</label>
                        <input type="text" class="form-control" name="sku" placeholder="SKU"
                            value="{{ old('sku', $product->sku ?? '') }}">
                    </div>

                    <div class="form-group col-6">
                        <label>SAT Code</label>
                        <input type="text" class="form-control" name="sat_code" placeholder="SAT Code"
                            value="{{ old('sat_code', $product->sat_code ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        <label>Stock Min</label>
                        <input type="number" step="0.01" class="form-control" name="stock_min"
                            value="{{ old('stock_min', $product->stock_min ?? '') }}">
                    </div>

                    <div class="form-group col-6">
                        <label>Stock Max</label>
                        <input type="number" step="0.01" class="form-control" name="stock_max"
                            value="{{ old('stock_max', $product->stock_max ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        <label>Presentation</label>
                        <input type="text" class="form-control" name="presentation"
                            value="{{ old('presentation', $product->presentation ?? '') }}">
                    </div>

                    <div class="form-group col-6">
                        <label>Unit</label>
                        <input type="text" class="form-control" name="unit"
                            value="{{ old('unit', $product->unit ?? '') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        <label>Batch Code</label>
                        <input type="text" class="form-control" name="batch_code"
                            value="{{ old('batch_code', $product->batch_code ?? '') }}">
                    </div>

                    <div class="form-group col-6">
                        <label>Images</label>
                        <input type="file" class="form-control" name="images[]" multiple id="image-input">

                        <div id="image-preview" class="mt-2 d-flex flex-wrap"></div>
                        @if(isset($linked_images))
                            <div class="mt-2">
                                
                                @foreach($linked_images as $img)
                                    <div class="d-flex align-items-center mb-1">
                                        @php
                                            $image_name = basename($img->path);
                                        @endphp
                                        <img src="/image/{{ $image_name }}" width="50">
                                        <a href="{{ route('admin.products.deleteImage', $img->image_id) }}" class="btn btn-sm btn-danger">Delete</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="form-group col-6">
                        <label>PDF Files</label>
                        <input type="file" class="form-control" name="files[]" multiple id="pdf-input">

                        <div id="pdf-preview" class="mt-2"></div>
                        @if(isset($linked_files))
                            <div class="mt-2">
                                @foreach($linked_files as $file)
                                    <div class="d-flex align-items-center mb-1">
                                        <a href="{{ asset('storage/' . $file->path) }}" target="_blank">
                                            📄 {{ basename($file->path) }}
                                        </a>
                                        <a href="{{ route('admin.products.deleteFile', $file->id ?? $file->file_id) }}" 
                                        class="btn btn-sm btn-danger ms-2">Delete</a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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

    {{-- Filtros --}}
    <div class="d-flex mb-2">
        <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex w-100" id="search-form">
            <input type="text" class="form-control" name="search" placeholder="Search Name" value="{{ request('search') }}" id="search-input">

            <select name="category_id" class="form-control ml-2" style="max-width: 200px;">
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-success ml-2"><i class="fas fa-search"></i></button>
        </form>

        <a href="{{ route('admin.products.create') }}" class="btn btn-success ml-2"><i class="fas fa-plus"></i></a>
    </div>

    {{-- Tabla de productos --}}
    @if($products->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>SAT Code</th>
                    <th>Stock Min</th>
                    <th>Stock Max</th>
                    <th>Presentation</th>
                    <th>Unit</th>
                    <th>Batch Code</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($products as $p)
                    <tr>
                        <td>{{ $p->category->name ?? 'N/A' }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->sat_code }}</td>
                        <td>{{ $p->stock_min }}</td>
                        <td>{{ $p->stock_max }}</td>
                        <td>{{ $p->presentation }}</td>
                        <td>{{ $p->unit }}</td>
                        <td>{{ $p->batch_code }}</td>
                        <td>
                            <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-primary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="d-inline delete-form">
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
        {{ $products->appends(request()->except('page'))->links('bootstrap-links') }}
    </div>
    @else
        <p>No products found.</p>
    @endif

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
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('search-input');
        const form = document.getElementById('search-form');
        let timeout = null;

        input.addEventListener('keyup', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => { form.submit(); }, 500);
        });
        input.addEventListener('keydown', function() { clearTimeout(timeout); });

        const categorySelect = form.querySelector('select[name="category_id"]');
        categorySelect.addEventListener('change', function() { form.submit(); });
    });

    document.addEventListener('DOMContentLoaded', function() {

        // === Preview con eliminación de imágenes ===
        const imageInput = document.getElementById('image-input');
        const imagePreview = document.getElementById('image-preview');
        let imageFiles = []; 

        imageInput.addEventListener('change', function() {
            imageFiles = Array.from(this.files);
            renderImagePreview();
        });

        function renderImagePreview() {
            imagePreview.innerHTML = "";
            imageFiles.forEach((file, index) => {
                if (!file.type.startsWith("image/")) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const container = document.createElement('div');
                    container.classList.add("d-inline-block", "position-relative", "me-2", "mb-2");

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = "70px";
                    img.classList.add("rounded", "shadow");

                    const btn = document.createElement('button');
                    btn.type = "button";
                    btn.innerHTML = "❌";
                    btn.classList.add("btn", "btn-sm", "btn-danger", "position-absolute");
                    btn.style.top = "0";
                    btn.style.right = "0";

                    btn.addEventListener('click', function() {
                        imageFiles.splice(index, 1);
                        updateFileInput(imageInput, imageFiles);
                        renderImagePreview();
                    });

                    container.appendChild(img);
                    container.appendChild(btn);
                    imagePreview.appendChild(container);
                };
                reader.readAsDataURL(file);
            });
        }

        // === Preview con eliminación de PDFs ===
        const pdfInput = document.getElementById('pdf-input');
        const pdfPreview = document.getElementById('pdf-preview');
        let pdfFiles = [];

        pdfInput.addEventListener('change', function() {
            pdfFiles = Array.from(this.files);
            renderPdfPreview();
        });

        function renderPdfPreview() {
            pdfPreview.innerHTML = "";
            pdfFiles.forEach((file, index) => {
                if (file.type !== "application/pdf") return;

                const div = document.createElement('div');
                div.classList.add("d-flex", "align-items-center", "mb-1");

                const name = document.createElement('span');
                name.textContent = "📄 " + file.name;

                const btn = document.createElement('button');
                btn.type = "button";
                btn.innerHTML = "❌";
                btn.classList.add("btn", "btn-sm", "btn-danger", "ms-2");

                btn.addEventListener('click', function() {
                    pdfFiles.splice(index, 1);
                    updateFileInput(pdfInput, pdfFiles);
                    renderPdfPreview();
                });

                div.appendChild(name);
                div.appendChild(btn);
                pdfPreview.appendChild(div);
            });
        }

        function updateFileInput(input, files) {
            const dataTransfer = new DataTransfer();
            files.forEach(file => dataTransfer.items.add(file));
            input.files = dataTransfer.files;
        }
    });
 </script>
@stop