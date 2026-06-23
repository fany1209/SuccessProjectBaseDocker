@extends('layouts.main')
@section('content')
<section class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="container py-5">
        <div class="row justify-content-center" data-aos="zoom-in" data-aos-duration='700'>
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                <div class="card border border-light-subtle rounded-3 shadow-sm">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex justify-content-center mb-3">
                            <img src="{{ asset('images/logo-alt.png') }}" alt="Success Logo" width="192" class="mx-auto">
                        </div>
                        @if (session('status'))
                            <div class="alert alert-success mb-4">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger mb-4">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Iniciar Sesión</h2>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="row gy-3 overflow-hidden">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="email" id="email" required>
                                        <label for="email">Email</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" name="password" id="password" required>
                                        <label for="password">Password</label>
                                    </div>
                                </div>

                                {{-- <div class="col-12 text-end">
                                    <a href="#" class="link-primary text-decoration-none">¿Olvidaste tu contraseña?</a>
                                </div> --}}

                                <div class="col-12">
                                    <div class="d-grid my-3">
                                        <button type="submit" class="btn btn-primary btn-lg">Aceptar</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
