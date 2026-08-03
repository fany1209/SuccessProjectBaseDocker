@extends('layouts.app', ['hideNavbar' => true])
@php
    $user = Auth::user();
    $user_name = $user->name;
    $initials = collect(explode(' ', $user_name))->map(fn($n) => strtoupper(substr($n, 0, 1)))->join('');
@endphp
@section('content')
<x-application.section-1 class="my-4">
    <div class="flex flex-col justify-center items-center gap-8">
        <div class="relative inline-block text-left">
            <a href="#" id="menuButton">
                <div class="flex justify-center items-center w-[200px] h-[200px] rounded-full shadow">
                    @if ($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url }}" width="200" class="rounded-full" alt="{{ $user_name }}"/>
                    @else
                        {{ $initials }}
                    @endif
                </div>
            </a>
            <div id="dropdownMenu" class="hidden absolute left-1/2 mt-2 w-48 bg-white shadow-lg rounded-md">
                <h6 class="m-2 pb-2 border-b-2">Manage Account</h6>
                @can('admin.dashboard')
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Admin Console</a>
                @endcan
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="m-2 py-1 px-2 bg-red-500 text-white tracking-[2px] font-base rounded-md" type="submit">Log Out</button>
                </form>
            </div>
        </div>
        <h2 class="text-3xl lg:text-6xl text-gray-700 border-b-2 border-green-700 pb-4 font-semibold tracking-[2px]">Welcome {{ $user_name }}</h2>
        @include('partials.complaint-banner')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 w-full">
            @can('warehouse.show')
                <x-application.buttons.menu-btn module="warehouse" link="{{ route('warehouse') }}" image="{{ asset('images/main-menu/cajas.png') }}">
                    WAREHOUSE
                </x-application.buttons.menu-btn>
            @endcan
            @can('warehouse.show')
                <x-application.buttons.menu-btn module="logistic" link="{{ route('logistic.index') }}" image="{{ asset('images/main-menu/logistica.png') }}">
                    LOGISTIC
                </x-application.buttons.menu-btn>
            @endcan
            @can('rh.show')
                <x-application.buttons.menu-btn module="human resources" link="{{ route('rh.index') }}" image="{{ asset('images/main-menu/personal.png') }}">
                    HUMAN RESOURCES
                </x-application.buttons.menu-btn>
            @endcan
            @can('quality.show')
                <x-application.buttons.menu-btn module="quality" link="{{ route('quality.index') }}" image="{{ asset('images/main-menu/calidad.png') }}">
                    QUALITY
                </x-application.buttons.menu-btn>
            @endcan
            @can('products.show')
                <x-application.buttons.menu-btn module="products" link="{{ route('catalogs.index') }}" image="{{ asset('images/main-menu/productos.png') }}">
                    PRODUCTS
                </x-application.buttons.menu-btn>
            @endcan
            @can('sales.show')
                <x-application.buttons.menu-btn module="sales" link="{{ route('sales.index') }}" image="{{ asset('images/main-menu/ventas.png') }}">
                    SALES
                </x-application.buttons.menu-btn>
            @endcan
            @can('purchases.show')
                <x-application.buttons.menu-btn module="purchases" link="{{ route('purchases.index') }}" image="{{ asset('images/main-menu/compras.png') }}">
                    PURCHASES
                </x-application.buttons.menu-btn>
            @endcan
            @can('laboratory.show')
                <x-application.buttons.menu-btn module="laboratory" link="{{ route('laboratory.index') }}" image="{{ asset('images/main-menu/laboratorio.png') }}">
                    LABORATORY
                </x-application.buttons.menu-btn>
            @endcan
            @can('finance.show')
                <x-application.buttons.menu-btn module="finance" link="{{ route('finance.index') }}" image="{{ asset('images/main-menu/finance.png') }}">
                    FINANCE
                </x-application.buttons.menu-btn>
            @endcan
            @can('production.show')
               <x-application.buttons.menu-btn module="production" link="{{ route('production.index') }}" image="{{ asset('images/main-menu/production.png') }}">
                    PRODUCTION
                </x-application.buttons.menu-btn>
            @endcan
            @can('id.show')
                <x-application.buttons.menu-btn module="i+d" link="#" image="{{ asset('images/main-menu/i+d.png') }}">
                    I+D
                </x-application.buttons.menu-btn>
            @endcan
            @can('fumigation.show')
                <x-application.buttons.menu-btn module="fumigation" link="{{ route('fumigaciones.index') }}" image="{{ asset('images/main-menu/fumigacion.png') }}">
                    FUMIGATIONS
                </x-application.buttons.menu-btn>
            @endcan
             @can('minutas.show')
                <x-application.buttons.menu-btn module="minutas" link="{{ route('minutas.index') }}" image="{{ asset('images/main-menu/minutas.png') }}">
                    MINUTAS
                </x-application.buttons.menu-btn>
            @endcan
                <x-application.buttons.menu-btn module="tasks" link="{{ route('tasks.index') }}" image="{{ asset('images/main-menu/tasks.png') }}">
                    TASKS
                </x-application.buttons.menu-btn>
                
                <x-application.buttons.menu-btn module="muestras" link="{{ route('muestras.index') }}" image="{{ asset('images/laboratory/02.png') }}">
                    Solicitud de muestras
                </x-application.buttons.menu-btn>

                <x-application.buttons.menu-btn module="orders" link="{{ route('orders.index') }}" image="{{ asset('images/main-menu/orders.png') }}">
                    ORDERS
                </x-application.buttons.menu-btn>

                @php
                    $yaContestoClima = \App\Models\ClimaLaboral::where('user_id', Auth::id())->exists();
                @endphp

                @if($yaContestoClima)
                    <x-application.buttons.menu-btn module="clima_laboral" link="#" class="opacity-50 cursor-not-allowed" onclick="Swal.fire({icon: 'info', title: 'Completada', text: 'Ya has contestado la encuesta de clima laboral. ¡Gracias por tu participación!', confirmButtonColor: '#198754'}); return false;" image="{{ asset('images/main-menu/personal.png') }}">
                        ENCUESTA CLIMA LABORAL <br><span class="text-[12px] text-yellow-300 tracking-normal leading-tight block mt-1">(Completada)</span>
                    </x-application.buttons.menu-btn>
                @else
                    <x-application.buttons.menu-btn module="clima_laboral" link="#" class="open-modal" data-target="modal-clima-laboral" image="{{ asset('images/main-menu/personal.png') }}">
                        ENCUESTA CLIMA LABORAL
                    </x-application.buttons.menu-btn>
                @endif
        </div>
    </div>
</x-application.section-1>

@include('rh.modals.clima_laboral')
@endsection
@push('js')
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
        const button = document.getElementById('menuButton');
        const menu = document.getElementById('dropdownMenu');

        button.addEventListener('click', function(e) {
            e.preventDefault();
            menu.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!button.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add('hidden');
            }
        });

        // Alerta emergente de pagos pendientes
        @if(auth()->check() && auth()->user()->unreadNotifications->where('type', 'App\Notifications\PendingPaymentAlert')->count() > 0)
            let pendingAlerts = {!! json_encode(auth()->user()->unreadNotifications->where('type', 'App\Notifications\PendingPaymentAlert')->pluck('data.pending_sales')->first()) !!};
            
            if (pendingAlerts && pendingAlerts.length > 0) {
                let htmlContent = '<div style="text-align: left; font-size: 14px; max-height: 250px; overflow-y: auto;">';
                pendingAlerts.forEach(function(sale) {
                    htmlContent += '<div style="padding: 10px; border-bottom: 1px solid #eee; margin-bottom: 5px;">';
                    htmlContent += '<strong>Cliente:</strong> ' + sale.customer + '<br>';
                    htmlContent += '<strong>Venta Folio:</strong> ' + sale.folio + '<br>';
                    htmlContent += '<strong style="color:red;">Días de atraso:</strong> ' + sale.days_late + ' días';
                    htmlContent += '</div>';
                });
                htmlContent += '</div>';

                Swal.fire({
                    icon: 'warning',
                    title: '¡Atención! Pagos Atrasados',
                    html: '<strong>Existen ventas a crédito que han superado su fecha límite de pago:</strong><br><br>' + htmlContent,
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#d33',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }
        @endif
        // Alerta emergente de contactos no leídos (solo admin)
        @if(auth()->check() && auth()->user()->hasRole('Admin'))
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
        @endif

        // Alerta de sesión (error general)
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Aviso',
                text: '{{ session('error') }}',
                confirmButtonColor: '#d33'
            });
        @endif
        
        // Alerta de sesión (éxito general)
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#198754'
            });
        @endif
    </script>
@endpush
