{{--dashboard
11/08/25
stefany 
--}}
@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1 class="badge badge-success">Dashboard</h1>
@stop

@section('content')
<div>
    <div class="card bg-light d-md-none">
        <div class="card-header">
            <h5 class="m-0"><b>Shortcuts</b></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-lg-4 mb-2">
                    <a href="#low-stock" class="btn btn-outline-primary col-12">Low Stock Products</a>
                </div>

                <div class="col-6 col-lg-4 mb-2">
                    <a href="#high-stock" class="btn btn-outline-primary col-12">High Stock Products</a>
                </div>

                <div class="col-6 col-lg-4 mb-2">
                    <a href="#recent-inputs" class="btn btn-outline-primary col-12">Recent Product Inputs</a>
                </div>

                <div class="col-12 col-lg-4 mb-2">
                    <a href="#recent-outputs" class="btn btn-outline-primary col-12">Recent Product Outputs</a>
                </div>

                <div class="col-12 col-lg-4 mb-2">
                    <a href="#warehouse-movs" class="btn btn-outline-primary col-12">Recent Warehouse Movements</a>
                </div>

                <div class="col-12 col-lg-4">
                    <a href="#tweaks" class="btn btn-outline-primary col-12">Recent Stock Tweaks</a>
                </div>

            </div>

        </div>
    </div>
    <div class="row row-cols-1 row-cols-md-2">
        <section id="low-stock">
            <div class="col mb-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="m-0 p-0"><b>Low Stock Products</b></h5>
                    </div>
                    <div class="card-body">
                        <div class="overflow-auto" style="max-height: 410px">
                            @if ($products_low->count() == 0)
                                <div class="container bg-light rounded p-5">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="img-container">
                                                <img src="{{ asset('images/caja.png') }}" width="128" alt="">
                                            </div>
                                            <div class="text-container">
                                                <h3>There are no products in low stock</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <ul class="list-group">
                                    @foreach ($products_low as $product)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                @if (isset($product->images[0]))
                                                    <img class="rounded"
                                                        src="{{ Storage::url($product->images[0]->path) }}"
                                                        alt="" width="64" />
                                                @else
                                                    <img class="rounded"
                                                        src="https://images.unsplash.com/photo-1499696010180-025ef6e1a8f9?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=1470&amp;q=80"
                                                        alt="" width="64" />
                                                @endif

                                                <div class="ml-2">
                                                    <p class="font-weight-bold mb-0">{{ $product->name }}</p>
                                                    <p class="mb-0 small text-danger">{{ $product->stock }}
                                                        {{ $product->unit }}
                                                    </p>
                                                </div>
                                            </div>
                                            <i class="fas fa-exclamation-circle"></i>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="high-stock">
            <div class="col mb-2">
                <div class="card">
                    <h5 class="card-header"><b>High Stock Products</b></h5>
                    <div class="card-body">
                        <div class="overflow-auto" style="max-height: 410px">
                            @if ($products_high->count() == 0)
                                <div class="container bg-light rounded p-5">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="img-container">
                                                <img src="{{ asset('images/caja.png') }}" width="128" alt="">
                                            </div>
                                            <div class="text-container">
                                                <h3>There are no products in high stock</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <ul class="list-group">
                                    @foreach ($products_high as $product)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                @if (isset($product->images[0]))
                                                    <img class="rounded"
                                                        src="{{ Storage::url($product->images[0]->path) }}"
                                                        alt="" width="64" />
                                                @else
                                                    <img class="rounded"
                                                        src="https://images.unsplash.com/photo-1499696010180-025ef6e1a8f9?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=1470&amp;q=80"
                                                        alt="" width="64" />
                                                @endif

                                                <div class="ml-2">
                                                    <p class="font-weight-bold mb-0">{{ $product->name }}</p>
                                                    <p class="mb-0 small text-info">{{ $product->stock }}
                                                        {{ $product->unit }}
                                                    </p>
                                                </div>
                                            </div>
                                            <i class="fas fa-exclamation-circle"></i>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="recent-inputs">
            <div class="col mb-2">
                <div class="card">
                    <h5 class="card-header"><b>Recent Product Inputs</b></h5>
                    <div class="card-body">
                        <div class="overflow-auto" style="max-height: 410px">
                            @if ($inventory_inputs->count() == 0)
                                <div class="container bg-light rounded p-5">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="img-container">
                                                <img src="{{ asset('images/caja.png') }}" width="128"
                                                    alt="">
                                            </div>
                                            <div class="text-container">
                                                <h3>There are no recent inputs</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="accordion border rounded" id="inputs-accordion">
                                    @foreach ($inventory_inputs as $input)
                                        <div class="card mb-0">
                                            <div class="card-header" id="inputs-heading{{ $loop->index }}">
                                                <div class="row d-flex justify-content-between align-items-center">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block text-left"
                                                            type="button" data-toggle="collapse"
                                                            data-target="#inputs-collapse{{ $loop->index }}"
                                                            aria-expanded="true">
                                                            <b>{{ $input->created_at->format('l, d F Y, H:i') }}</b>
                                                        </button>
                                                    </h2>
                                                    <div class="btn-group">
                                                        <button class="btn p-0" type="button"
                                                            data-toggle="dropdown">
                                                            <span
                                                                class="badge badge-success">{{ $input->products->count() }}</span>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a href="{{ route('makePDF', ['movType' => 'input', 'movId' => $input->input_id]) }}"
                                                                target="_blank" class="dropdown-item"
                                                                type="button">Download
                                                                Input Format</a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <div id="inputs-collapse{{ $loop->index }}" class="collapse"
                                                data-parent="#inputs-accordion">
                                                <div class="card-body">
                                                    @foreach ($input->products as $product)
                                                        <div class="d-flex align-items-center mb-2">
                                                            @if (isset($product->images[0]))
                                                                <img class="rounded"
                                                                    src="{{ Storage::url($product->images[0]->path) }}"
                                                                    alt="" width="64" />
                                                            @else
                                                                <img class="rounded"
                                                                    src="https://images.unsplash.com/photo-1499696010180-025ef6e1a8f9?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=1470&amp;q=80"
                                                                    alt="" width="64" />
                                                            @endif
                                                            <div class="ml-2">
                                                                <p class="font-weight-bold mb-0">{{ $product->name }}
                                                                </p>
                                                                <p class="mb-0 font-weight-bold small text-success">
                                                                    {{ $product->pivot->quantity }}
                                                                    {{ $product->unit }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="recent-outputs">
            <div class="col mb-2">
                <div class="card">
                    <h5 class="card-header"><b>Recent Product Outputs</b></h5>
                    <div class="card-body">
                        <div class="overflow-auto" style="max-height: 410px">
                            @if ($inventory_outputs->count() == 0)
                                <div class="container bg-light rounded p-5">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="img-container">
                                                <img src="{{ asset('images/caja.png') }}" width="128"
                                                    alt="">
                                            </div>
                                            <div class="text-container">
                                                <h3>There are no recent outputs</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="accordion border rounded" id="outputs-accordion">
                                    @foreach ($inventory_outputs as $output)
                                        <div class="card mb-0">
                                            <div class="card-header" id="outputs-heading{{ $loop->index }}">
                                                <div class="row d-flex justify-content-between align-items-center">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block text-left"
                                                            type="button" data-toggle="collapse"
                                                            data-target="#outputs-collapse{{ $loop->index }}"
                                                            aria-expanded="true">
                                                            <b>{{ $output->created_at->format('l, d F Y, H:i') }}</b>
                                                        </button>
                                                    </h2>
                                                    <div class="btn-group">
                                                        <button class="btn p-0" type="button"
                                                            data-toggle="dropdown">
                                                            <span
                                                                class="badge badge-danger">{{ $output->products->count() }}</span>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a href="{{ route('makePDF', ['movType' => 'output', 'movId' => $output->output_id]) }}"
                                                                target="_blank" class="dropdown-item"
                                                                type="button">Download
                                                                Output Format</a>
                                                        </div>
                                                    </div>

                                                </div>

                                            </div>

                                            <div id="outputs-collapse{{ $loop->index }}" class="collapse"
                                                data-parent="#outputs-accordion">
                                                <div class="card-body">
                                                    @foreach ($output->products as $product)
                                                        <div class="d-flex align-items-center mb-2">
                                                            @if (isset($product->images[0]))
                                                                <img class="rounded"
                                                                    src="{{ Storage::url($product->images[0]->path) }}"
                                                                    alt="" width="64" />
                                                            @else
                                                                <img class="rounded"
                                                                    src="https://images.unsplash.com/photo-1499696010180-025ef6e1a8f9?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=1470&amp;q=80"
                                                                    alt="" width="64" />
                                                            @endif
                                                            <div class="ml-2">
                                                                <p class="font-weight-bold mb-0">{{ $product->name }}
                                                                </p>
                                                                <p class="mb-0 font-weight-bold small text-danger">
                                                                    {{ $product->pivot->quantity }}
                                                                    {{ $product->unit }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="warehouse-movs">
            <div class="col mb-2">
                <div class="card">
                    <h5 class="card-header"><b>Recent Warehouse Movements</b></h5>
                    <div class="card-body">
                        <div class="overflow-auto" style="max-height: 410px">
                            @if ($warehouse_movs->count() == 0)
                                <div class="container bg-light rounded p-5">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="img-container">
                                                <img src="{{ asset('images/caja.png') }}" width="128"
                                                    alt="">
                                            </div>
                                            <div class="text-container">
                                                <h3>There are no recent warehouse movements</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="accordion border rounded" id="movs-accordion">
                                    @foreach ($warehouse_movs as $mov)
                                        <div class="card mb-0">
                                            <div class="card-header" id="movs-heading{{ $loop->index }}">
                                                <div class="row d-flex justify-content-between align-items-center">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block text-left"
                                                            type="button" data-toggle="collapse"
                                                            data-target="#movs-collapse{{ $loop->index }}"
                                                            aria-expanded="true">
                                                            <b>{{ $mov->date }}</b>
                                                        </button>
                                                    </h2>
                                                    <span class="badge badge-warning">{{ $mov->lName }}</span>
                                                </div>

                                            </div>

                                            <div id="movs-collapse{{ $loop->index }}" class="collapse"
                                                data-parent="#movs-accordion">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        @if (isset($product->images[0]))
                                                            <img class="rounded"
                                                                src="{{ Storage::url($product->images[0]->path) }}"
                                                                alt="" width="104" />
                                                        @else
                                                            <img class="rounded"
                                                                src="https://images.unsplash.com/photo-1499696010180-025ef6e1a8f9?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=1470&amp;q=80"
                                                                alt="" width="104" />
                                                        @endif

                                                        <div class="ml-2">
                                                            <p class="font-weight-bold mb-0">{{ $mov->name }}</p>
                                                            <p class="mb-0 small">
                                                                Quantity: <b>{{ $mov->quantity }}</b>
                                                            </p>
                                                            <p class="mb-0 small">
                                                                Weight Per Unit: <b>{{ $mov->weight_per_unit }}</b>
                                                            </p>
                                                            <p class="mb-0 small">
                                                                Total: <b>{{ $mov->net_weight }}</b>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="tweaks">
            <div class="col mb-2">
                <div class="card">
                    <h5 class="card-header"><b>Recent Stock Tweaks</b></h5>
                    <div class="card-body">
                        <div class="overflow-auto" style="max-height: 410px">
                            @if ($tweaks->count() == 0)
                                <div class="container bg-light rounded p-5">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <div class="img-container">
                                                <img src="{{ asset('images/caja.png') }}" width="128"
                                                    alt="">
                                            </div>
                                            <div class="text-container">
                                                <h3>There are no recent stock tweaks</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="accordion border rounded" id="tweaks-accordion">
                                    @foreach ($tweaks as $tweak)
                                        <div class="card mb-0">
                                            <div class="card-header" id="tweaks-heading{{ $loop->index }}">
                                                <div class="row d-flex justify-content-between align-items-center">
                                                    <h2 class="mb-0">
                                                        <button class="btn btn-link btn-block text-left"
                                                            type="button" data-toggle="collapse"
                                                            data-target="#tweaks-collapse{{ $loop->index }}"
                                                            aria-expanded="true">
                                                            <b>{{ $tweak->created_at->format('l, d F Y, H:i') }}</b>
                                                        </button>
                                                    </h2>
                                                    @if ($tweak->type == 'Input')
                                                        <span class="badge badge-success">Input</span>
                                                    @else
                                                        <span class="badge badge-danger">Output</span>
                                                    @endif
                                                </div>

                                            </div>

                                            <div id="tweaks-collapse{{ $loop->index }}" class="collapse"
                                                data-parent="#tweaks-accordion">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        @if (isset($product->images[0]))
                                                            <img class="rounded"
                                                                src="{{ Storage::url($product->images[0]->path) }}"
                                                                alt="" width="64" />
                                                        @else
                                                            <img class="rounded"
                                                                src="https://images.unsplash.com/photo-1499696010180-025ef6e1a8f9?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;w=1470&amp;q=80"
                                                                alt="" width="64" />
                                                        @endif

                                                        <div class="ml-2">
                                                            <p class="font-weight-bold mb-0">{{ $tweak->name }}</p>
                                                            <p
                                                                class="mb-0 small font-weight-bold {{ $tweak->type == 'Input' ? 'text-success' : 'text-danger' }}">
                                                                {{ $tweak->quantity }} {{ $tweak->unit }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        @php
            $unreadContactsCount = \App\Models\Contact::whereNull('read_at')->count();
        @endphp
        
        @if($unreadContactsCount > 0)
            Swal.fire({
                title: 'Nuevos Mensajes de Contacto',
                text: 'Tienes {{ $unreadContactsCount }} mensaje(s) sin leer de la página web.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-eye"></i> Ver mensajes',
                cancelButtonText: 'Cerrar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('admin.contacts.index') }}";
                }
            });
        @endif
    });
</script>
@stop
