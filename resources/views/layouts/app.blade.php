<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Success Suministros Sustentables') }}</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('images/successIconG.ico') }}" type="image/x-icon" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="{{ asset('vendor/aos/aos.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    @stack('css')
    @vite([
        'resources/css/app.css', 
        'resources/js/app.js'
        ])
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="font-sans antialiased">
    <x-banner/>
    @unless(isset($hideNavbar) && $hideNavbar)
    <x-application.navbar/>
    @endunless
    <main>
        @yield('content')
    </main>
    <button id="btop" onclick="scrollToTop()"
        class="fixed bottom-3 right-3 bg-green-500 hover:bg-green-400 rounded-md px-2 py-2 transition-all duration-200" style="display: none"><img
            src="{{ asset('images/up.png') }}" width="20"></button>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/aos/aos.js') }}"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            @auth
                var pusher = new Pusher('7916e627f69fccca6f7d', {
                    cluster: 'us2',
                    forceTLS: true
                });

                var channel = pusher.subscribe('user-tasks.{{ auth()->id() }}');

                channel.bind('task-assigned', function(data) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: '<span class="text-indigo-600 font-bold uppercase">¡Nueva Tarea!</span>',
                        html: `Te han asignado: <b>${data.title}</b>`,
                        showConfirmButton: false,
                        timer: 8000,
                        timerProgressBar: true,
                        didClick: () => {
                            window.location.href = "{{ route('tasks.index') }}";
                        }
                    });

                    let badge = $('#notification-count');
                    if(badge.length > 0) {
                        let current = parseInt(badge.text()) || 0;
                        badge.text(current + 1).removeClass('hidden').show();
                    }
                });

                // Polling for Almacen Notifications
                let shownNotifications = [];
                function fetchUnreadNotifications() {
                    $.get('{{ route("almacen_notifications.unread") }}', function(res) {
                        if(res.notifications) {
                            let newNotifs = Object.values(res.notifications).filter(n => !shownNotifications.includes(n.id));
                            if(newNotifs.length > 0) {
                                newNotifs.forEach(n => {
                                    shownNotifications.push(n.id);
                                    let msg = n.data.message || 'Nueva notificación de almacén';
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'info',
                                        title: 'Notificación de Venta',
                                        text: msg,
                                        showConfirmButton: true,
                                        confirmButtonText: 'Ver detalles',
                                        showCancelButton: true,
                                        cancelButtonText: 'Cerrar',
                                        timer: 10000,
                                        timerProgressBar: true
                                    }).then((result) => {
                                        if (result.isConfirmed && n.data.url) {
                                            $.post('{{ url("/almacen-notifications/mark-read") }}/' + n.id, function() {
                                                window.location.href = n.data.url;
                                            });
                                        }
                                    });

                                    let dropdown = $('#notifications-dropdown-list');
                                    if(dropdown.length > 0) {
                                        let urlLink = n.data.url ? `<a href="${n.data.url}" class="text-[10px] text-indigo-600 hover:underline">Ver detalles</a>` : '';
                                        let html = `
                                            <div class="p-2 bg-white border-l-4 border-green-500">
                                                <div class="flex justify-between">
                                                    <span class="text-sm font-bold">Notificación</span>
                                                    <small class="text-gray-400">Ahora mismo</small>
                                                </div>
                                                <p class="text-xs">${msg}</p>
                                                ${urlLink}
                                            </div>
                                        `;
                                        dropdown.children().first().after(html);
                                    }
                                });

                                let badge = $('#notification-count');
                                if(badge.length > 0) {
                                    let current = parseInt(badge.text()) || 0;
                                    badge.text(current + newNotifs.length).removeClass('hidden').show();
                                }
                            }
                        }
                    });
                }
                
                fetchUnreadNotifications();
                setInterval(fetchUnreadNotifications, 30000); // 30 seconds

            @endauth
        });

        const scrollToTopButton = document.getElementById('btop');
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 350) {
                scrollToTopButton.style.display = 'block';
            } else {
                scrollToTopButton.style.display = 'none';
            }
        });

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        function modals(){
            $(document).on('click','.open-modal',function(){
                const target = $(this).data('target');
                $('#' + target).removeClass('hidden');
            });
            $(document).on('click','.close-modal',function(){
                $(this).closest('.fixed').addClass('hidden');
            });
            $(document).on('click','.fixed',function(e){
                if ($(e.target).is('.fixed')) {
                    $(this).addClass('hidden');
                }
            });
        }
        modals();
    </script>
    @stack('js')
</body>
</html>