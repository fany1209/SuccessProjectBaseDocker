<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Success Suministros Sustentables') }}</title>

    <!--Tailwind-->
    <script src="https://cdn.tailwindcss.com"></script>
    <!--Remixicon-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <!--Swiper-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- SweetAlert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Favicon-->
    <link rel="icon" href="{{ asset('images/successIconG.ico') }}" type="image/x-icon" />
    <!-- CSS y librerías -->
    <link href="{{ asset('vendor/aos/aos.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Bootstrap JS Bundle (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('css')
    <!-- Vite scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=PT+Serif+Caption:ital@0;1&family=Righteous&display=swap');

        .nav-active {
            color: #72c393;
        }

        .nav-hover:hover {
            color: #72c393;
        }

        .text-pt-serif {
            font-family: "PT Serif Caption", serif;
            font-weight: 300;
            font-style: normal;
            opacity: 0.9;
        }

        .text-green-mod {
            color: rgba(69, 245, 46, 0.815);
        }

        .back-to-top-btn {
            position: fixed;
            bottom: 15px;
            right: 15px;
            display: none;
            z-index: 5;
        }
    </style>
</head>
<body>
    @unless(isset($hideNavbar) && $hideNavbar)
    @include('partials.public-navbar')
    @endunless
    <main>
        @yield('content')
    </main>
    @unless(isset($hideFooter) && $hideFooter)
    @include('partials.public-footer')
    @endunless
    <button onclick="scrollToTop()" id="btop" class="btn btn-success back-to-top-btn">
        <img src="{{ asset('images/up.png') }}" width="16" alt="top" />
    </button>
    <a href="https://wa.me/524613818787" target="_blank">
        <div class="whatsapp-widget" data-toggle="tooltip" data-placement="right" title="Envíanos un WhatsApp">
            <i class="fab fa-whatsapp fa-2x" style="color: white;"></i>
        </div>
    </a>
    {{-- Scripts --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        AOS.init();
        const scrollToTopButton = document.getElementById('btop');
        window.addEventListener('scroll', () => {
            scrollToTopButton.style.display = window.pageYOffset > 350 ? 'block' : 'none';
        });
        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
    @stack('js')
</body>
</html>
