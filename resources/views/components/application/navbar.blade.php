@php
    $currentUser = Auth::user();
    $profilePhoto = $currentUser->profile_photo_path 
                    ? '/profile-photo/' . substr($currentUser->profile_photo_path, 15) 
                    : $currentUser->profile_photo_url;
                    
    $count = $currentUser->unreadNotifications->count();
    $notificationsList = $currentUser->notifications()->take(10)->get();

    // Contador de mensajes de contacto no leídos (solo para Admin)
    $unreadContactsCount = 0;
    $unreadContacts = collect();
    if ($currentUser->hasRole('Admin')) {
        $unreadContacts = \App\Models\Contact::whereNull('read_at')->orderBy('created_at', 'desc')->take(10)->get();
        $unreadContactsCount = \App\Models\Contact::whereNull('read_at')->count();
    }
@endphp

<nav x-data="{ open: false, showNotifications: false, showContactAlerts: false }" class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('main-menu') }}">
                        <img src="/images/logo.png" width="150" alt="Success Logo">
                    </a>
                </div>

                <div class="hidden space-x-4 sm:-my-px sm:ms-6 lg:flex xl:space-x-8">
                    @can('products.show')
                        <x-nav-link href="{{ route('catalogs.index') }}" :active="request()->routeIs('catalogs.index')">
                            {{ __('Catalog') }}
                        </x-nav-link>
                    @endcan

                    @can('warehouse.show')
                        <x-nav-link href="{{ route('warehouse') }}" :active="request()->routeIs('warehouse')">
                            {{ __('Warehouse') }}
                        </x-nav-link>
                    @endcan

                    @can('inventory.show')
                        <x-nav-link href="{{ route('inventory.index') }}" :active="request()->routeIs('inventory.index') && request('tab') !== 'sales'">
                            {{ __('Inventory') }}
                        </x-nav-link>
                    @endcan

                    @can('warehouse.show')
                        <x-nav-link href="{{ route('logistic.index') }}" :active="request()->routeIs('logistic.index')">
                            {{ __('Logistic') }}
                        </x-nav-link>
                    @endcan

                    @can('customers.show')
                        <x-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.index')">
                            {{ __('Customers') }}
                        </x-nav-link>
                    @endcan

                    @can('prospects.show')
                        <x-nav-link href="{{ route('prospects.index') }}" :active="request()->routeIs('prospects.index')">
                            {{ __('Prospects') }}
                        </x-nav-link>
                    @endcan

                    @can('suppliers.show')
                        <x-nav-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')">
                            {{ __('Suppliers') }}
                        </x-nav-link>
                    @endcan

                    @can('sales.show')
                        <x-nav-link href="{{ route('sales.index') }}" :active="request()->routeIs('sales.index')">
                            {{ __('Sales') }}
                        </x-nav-link>
                    @else
                        @role('Warehouse')
                            <x-nav-link href="{{ route('inventory.index', ['tab' => 'sales']) }}" :active="(request()->routeIs('inventory.index') && request('tab') === 'sales') || request()->routeIs('sales.almacen_detail')">
                                {{ __('Sales') }}
                            </x-nav-link>
                        @endrole
                    @endcan

                    @can('purchases.show')
                        <x-nav-link href="{{ route('purchases.index') }}" :active="request()->routeIs('purchases.index')">
                            {{ __('Purchases') }}
                        </x-nav-link>
                    @endcan

                    @can('quality.show')
                        <x-nav-link href="{{ route('quality.index') }}" :active="request()->routeIs('quality.index')">
                            {{ __('Quality') }}
                        </x-nav-link>
                    @endcan

                    @can('laboratory.show')
                        <x-nav-link href="{{ route('laboratory.index') }}" :active="request()->routeIs('laboratory.index')">
                            {{ __('Laboratory') }}
                        </x-nav-link>
                    @endcan

                    @can('quality.show')
                        <x-nav-link href="{{ route('production.yeast.index') }}" :active="request()->routeIs('production.yeast.index')">
                            {{ __('Producción') }}
                        </x-nav-link>
                    @endcan

                    @can('finance.show')
                        <x-nav-link href="{{ route('finance.index') }}" :active="request()->routeIs('finance.index')">
                            {{ __('Finance') }}
                        </x-nav-link>
                    @endcan

                    @can('production.show')
                        <x-nav-link href="{{ route('production.index') }}" :active="request()->routeIs('production.index')">
                            {{ __('Production') }}
                        </x-nav-link>
                    @endcan

                    <x-nav-link href="{{ route('tasks.index') }}" :active="request()->routeIs('tasks.index')">
                        {{ __('Tasks') }}
                    </x-nav-link>

                    @hasanyrole('Admin|Sales|Warehouse|Quality')
                        <x-nav-link href="{{ route('orders.index') }}" :active="request()->routeIs('orders.index')">
                            {{ __('Orders') }}
                        </x-nav-link>
                    @endhasanyrole
                </div>
            </div>

            <div class="hidden lg:flex lg:items-center lg:ms-6 space-x-3">
                
                <div class="relative">
                    <button @click="showNotifications = !showNotifications" class="relative p-2 text-gray-400 hover:text-indigo-600 focus:outline-none transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span id="notification-count" class="{{ $count > 0 ? '' : 'hidden' }} absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-[10px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full shadow-sm">
                            {{ $count }}
                        </span>
                    </button>

                    <div id="notifications-dropdown-list" x-show="showNotifications" 
                         @click.away="showNotifications = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden" 
                         style="display: none;">
                        <div class="p-3 border-b bg-gray-50 flex justify-between items-center font-bold text-xs text-gray-700 uppercase">
                            <span>Notificaciones</span>
                            @if($count > 0)
                                <button onclick="markAllNotificationsRead()" class="text-indigo-600 hover:underline normal-case font-normal text-[10px]">Marcar leídas</button>
                            @endif
                        </div>
                        @foreach ($notificationsList as $notification)
                            <div class="p-2 {{ $notification->read_at ? 'opacity-60 bg-gray-50' : 'bg-white border-l-4 border-green-500' }}">
                                <div class="flex justify-between">
                                    <span class="text-sm font-bold">{{ $notification->data['title'] ?? 'Notificación' }}</span>
                                    <small class="text-gray-400">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="text-xs">{{ $notification->data['message'] ?? 'No detail provided' }}</p>
                                
                                @if(isset($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="text-[10px] text-indigo-600 hover:underline">Ver detalles</a>
                                @endif

                                @if($notification->read_at)
                                    <span class="text-[10px] text-gray-400 italic block mt-1">Leída</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Alerta de mensajes de contacto (solo para Admin) --}}
                @if($currentUser->hasRole('Admin'))
                <div class="relative">
                    <button @click="showContactAlerts = !showContactAlerts" class="relative p-2 text-gray-400 hover:text-green-600 focus:outline-none transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span id="contact-alert-count" class="{{ $unreadContactsCount > 0 ? '' : 'hidden' }} absolute top-1 right-1 inline-flex items-center justify-center px-2 py-1 text-[10px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-green-600 rounded-full shadow-sm">
                            {{ $unreadContactsCount }}
                        </span>
                    </button>

                    <div x-show="showContactAlerts" 
                         @click.away="showContactAlerts = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden" 
                         style="display: none;">
                        <div class="p-3 border-b bg-gray-50 flex justify-between items-center font-bold text-xs text-gray-700 uppercase">
                            <span>Mensajes de Contacto</span>
                            <a href="{{ route('admin.contacts.index') }}" class="text-green-600 hover:underline normal-case font-normal text-[10px]">Ver todos</a>
                        </div>
                        @forelse ($unreadContacts as $uc)
                            <div class="p-3 bg-white border-l-4 border-green-500 border-b border-gray-100 contact-alert-item" data-contact-id="{{ $uc->contact_id }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <span class="text-sm font-bold text-gray-800">{{ $uc->name }}</span>
                                        <small class="text-gray-400 ml-2">{{ $uc->created_at->diffForHumans() }}</small>
                                    </div>
                                    <label class="flex items-center gap-1 cursor-pointer contact-read-checkbox">
                                        <input type="checkbox" class="mark-contact-read rounded border-gray-300 text-green-600 focus:ring-green-500" data-id="{{ $uc->contact_id }}">
                                        <span class="text-[10px] text-gray-500">Leído</span>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ Str::limit($uc->message, 100) }}</p>
                                @if($uc->phone)
                                    <span class="text-[10px] text-gray-400">📞 {{ $uc->phone }}</span>
                                @endif
                            </div>
                        @empty
                            <div class="p-4 text-center text-sm text-gray-400">
                                No hay mensajes sin leer
                            </div>
                        @endforelse
                    </div>
                </div>
                @endif

                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $profilePhoto }}" alt="{{ $currentUser->name }}" />
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="{{ route('main-menu') }}">{{ __('Main Menu') }}</x-dropdown-link>
                            @can('admin.dashboard')
                                <x-dropdown-link href="{{ route('admin.dashboard') }}">{{ __('Admin Console') }}</x-dropdown-link>
                            @endcan
                            <div class="border-t border-gray-200"></div>
                            <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <div class="-me-2 flex items-center lg:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': !open }" class="hidden lg:hidden bg-gray-50 border-t border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('main-menu') }}" :active="request()->routeIs('main-menu')">
                {{ __('Main Menu') }}
            </x-responsive-nav-link>

            @can('products.show')
                <x-responsive-nav-link href="{{ route('catalogs.index') }}" :active="request()->routeIs('catalogs.index')">
                    {{ __('Catalog') }}
                </x-responsive-nav-link>
            @endcan

            @can('warehouse.show')
                <x-responsive-nav-link href="{{ route('warehouse') }}" :active="request()->routeIs('warehouse')">
                    {{ __('Warehouse') }}
                </x-responsive-nav-link>
            @endcan

            @can('inventory.show')
                <x-responsive-nav-link href="{{ route('inventory.index') }}" :active="request()->routeIs('inventory.index') && request('tab') !== 'sales'">
                    {{ __('Inventory') }}
                </x-responsive-nav-link>
            @endcan

            @can('warehouse.show')
                <x-responsive-nav-link href="{{ route('logistic.index') }}" :active="request()->routeIs('logistic.index')">
                    {{ __('Logistic') }}
                </x-responsive-nav-link>
            @endcan

            @can('customers.show')
                <x-responsive-nav-link href="{{ route('customers.index') }}" :active="request()->routeIs('customers.index')">
                    {{ __('Customers') }}
                </x-responsive-nav-link>
            @endcan

            @can('prospects.show')
                <x-responsive-nav-link href="{{ route('prospects.index') }}" :active="request()->routeIs('prospects.index')">
                    {{ __('Prospects') }}
                </x-responsive-nav-link>
            @endcan

            @can('suppliers.show')
                <x-responsive-nav-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')">
                    {{ __('Suppliers') }}
                </x-responsive-nav-link>
            @endcan

            @can('sales.show')
                <x-responsive-nav-link href="{{ route('sales.index') }}" :active="request()->routeIs('sales.index')">
                    {{ __('Sales') }}
                </x-responsive-nav-link>
            @else
                @role('Warehouse')
                    <x-responsive-nav-link href="{{ route('inventory.index', ['tab' => 'sales']) }}" :active="(request()->routeIs('inventory.index') && request('tab') === 'sales') || request()->routeIs('sales.almacen_detail')">
                        {{ __('Sales') }}
                    </x-responsive-nav-link>
                @endrole
            @endcan

            @can('purchases.show')
                <x-responsive-nav-link href="{{ route('purchases.index') }}" :active="request()->routeIs('purchases.index')">
                    {{ __('Purchases') }}
                </x-responsive-nav-link>
            @endcan

            @can('quality.show')
                <x-responsive-nav-link href="{{ route('quality.index') }}" :active="request()->routeIs('quality.index')">
                    {{ __('Quality') }}
                </x-responsive-nav-link>
            @endcan

            @can('laboratory.show')
                <x-responsive-nav-link href="{{ route('laboratory.index') }}" :active="request()->routeIs('laboratory.index')">
                    {{ __('Laboratory') }}
                </x-responsive-nav-link>
            @endcan

            @can('finance.show')
                <x-responsive-nav-link href="{{ route('finance.index') }}" :active="request()->routeIs('finance.index')">
                    {{ __('Finance') }}
                </x-responsive-nav-link>
            @endcan

            @can('production.show')
                <x-responsive-nav-link href="{{ route('production.index') }}" :active="request()->routeIs('production.index')">
                    {{ __('Production') }}
                </x-responsive-nav-link>
            @endcan

            <x-responsive-nav-link href="{{ route('tasks.index') }}" :active="request()->routeIs('tasks.index')">
                {{ __('Tasks') }}
            </x-responsive-nav-link>

            @hasanyrole('Admin|Sales|Warehouse|Quality')
            <x-responsive-nav-link href="{{ route('orders.index') }}" :active="request()->routeIs('orders.index')">
                {{ __('Orders') }}
            </x-responsive-nav-link>
            @endhasanyrole
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
                <div class="shrink-0 me-3">
                    <img class="h-10 w-10 rounded-full object-cover border" src="{{ $profilePhoto }}" alt="{{ $currentUser->name }}" />
                </div>
                <div>
                    <div class="font-medium text-base text-gray-800">{{ $currentUser->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ $currentUser->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                @can('admin.dashboard')
                    <x-responsive-nav-link href="{{ route('admin.dashboard') }}">{{ __('Admin Console') }}</x-responsive-nav-link>
                @endcan
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    function markAllNotificationsRead() {
        fetch('{{ route("notifications.markRead") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).then(() => {
            window.location.reload();
        });
    }

    // Marcar mensajes de contacto como leídos
    document.querySelectorAll('.mark-contact-read').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            if (!this.checked) return;
            const contactId = this.dataset.id;
            const item = this.closest('.contact-alert-item');
            
            this.disabled = true;
            this.closest('.contact-read-checkbox').querySelector('span').textContent = 'Marcado';
            this.closest('.contact-read-checkbox').querySelector('span').classList.add('text-green-600', 'font-bold');
            
            item.classList.remove('border-green-500');
            item.classList.add('border-gray-300', 'opacity-60');

            fetch('/admin/contacts/' + contactId + '/mark-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
              .then(data => {
                if (data.success) {
                    let badge = document.getElementById('contact-alert-count');
                    if (badge) {
                        let current = parseInt(badge.textContent) || 0;
                        let newCount = Math.max(0, current - 1);
                        badge.textContent = newCount;
                        if (newCount === 0) badge.classList.add('hidden');
                    }
                }
            });
        });
    });
</script>