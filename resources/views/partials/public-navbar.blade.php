@php
    $refs = [
        (Object) ['link' => '/', 'section' => 'Inicio'],
        (Object) ['link' => '/contact', 'section' => 'Contacto'],
    ];
@endphp
<nav class="bg-gray-800 text-white">
    <x-public-site.section-1>
        <div class="flex justify-between h-16">
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="text-xl font-bold"><img src="{{ asset('images/pruebaSuccess.png') }}" width="148" class="img-fluid"></a>
            </div>
    
            <div class="hidden md:flex space-x-4 items-center">
                @foreach ($refs as $ref)
                <a href="{{ $ref->link }}" class="px-3 py-2 rounded-md hover:bg-gray-700">{{ $ref->section }}</a>
                @endforeach
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/main-menu') }}" class="px-3 py-2 rounded-md hover:bg-gray-700 text-2xl"><i class="ri-dashboard-horizontal-fill"></i></a>
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('register') }}" class="px-3 py-2 rounded-md hover:bg-gray-700 text-2xl">Register</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 rounded-md hover:bg-gray-700 text-2xl"><i class="ri-login-box-fill"></i></a>
                    @endauth
                @endif
            </div>
    
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="focus:outline-none"><i class="ri-menu-line text-2xl"></i></button>
            </div>
        </div>
    </x-public-site.section-1>
    <div id="mobile-menu" class="hidden md:hidden bg-gray-700">
        @foreach ($refs as $ref)
        <a href="{{ $ref->link }}" class="block px-4 py-2 hover:bg-gray-600">{{ $ref->section }}</a>
        @endforeach
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/main-menu') }}" class="block px-4 py-2 hover:bg-gray-600 text-2xl"><i class="ri-dashboard-horizontal-fill"></i></a>
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('register') }}" class="block px-4 py-2 hover:bg-gray-600 text-2xl">Register</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="block px-4 py-2 hover:bg-gray-600 text-2xl"><i class="ri-login-box-fill"></i></a>
            @endauth
        @endif
    </div>
</nav>

<script>
  const btn = document.getElementById('mobile-menu-button');
  const menu = document.getElementById('mobile-menu');

  btn.addEventListener('click', () => {
    menu.classList.toggle('hidden');
  });
</script>