<header>
    <div class="px-5 bg-dark col-span-12 w-full flex flex-col items-center py-16 px-4">
        <nav class="navbar navbar-dark navbar-expand-md">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/pruebaSuccess.png') }}" width="148" class="img-fluid">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse dark" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a href="{{ url('/') }}"
                           class="nav-link nav-hover {{ Route::is('home') ? 'nav-active' : '' }}">
                            Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('contact') }}"
                           class="nav-link nav-hover {{ Route::is('contact') ? 'nav-active' : '' }}">
                            Contacto
                        </a>
                    </li>
                </ul>
                <div class="col d-flex justify-content-end">
                    <ul class="navbar-nav">
                        @if (Route::has('login'))
                            @auth
                                <li>
                                    <a href="{{ url('/main-menu') }}"
                                       class="nav-link nav-hover {{ Route::is('main-menu') ? 'nav-active' : '' }}">
                                        Menú Principal
                                    </a>
                                </li>
                                @if (auth()->user()->role === 'admin')
                                    <li>
                                        <a href="{{ route('register') }}"
                                           class="nav-link nav-hover {{ Route::is('register') ? 'nav-active' : '' }}">
                                            Register
                                        </a>
                                    </li>
                                @endif
                            @else
                                <li>
                                    <a href="{{ route('login') }}"
                                       class="nav-link nav-hover {{ Route::is('login') ? 'nav-active' : '' }}">
                                        Iniciar Sesión
                                    </a>
                                </li>
                            @endauth
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
