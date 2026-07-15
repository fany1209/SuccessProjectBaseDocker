<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olvidé mi contraseña — Portal de Clientes</title>
    <meta name="description" content="Restablece tu contraseña del Portal de Clientes de Suministros Sustentables.">
    <link rel="icon" href="{{ asset('images/successIconG.ico') }}" type="image/x-icon" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#f4fbf1', 100: '#e5f6df', 200: '#caeec0', 300: '#a3df95',
                            400: '#77c35a', 500: '#5ab03c', 600: '#439229', 700: '#367524',
                            800: '#2d5d21', 900: '#264d1f', 950: '#122a0e',
                        },
                        green: {
                            50: '#f4fbf1', 100: '#e5f6df', 200: '#caeec0', 300: '#a3df95',
                            400: '#77c35a', 500: '#5ab03c', 600: '#439229', 700: '#367524',
                            800: '#2d5d21', 900: '#264d1f', 950: '#122a0e',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            overflow: hidden;
        }

        .bg-agro {
            background-image: linear-gradient(to bottom, rgba(6, 78, 59, 0.7), rgba(2, 44, 34, 0.9)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -2;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            animation: float 10s ease-in-out infinite alternate;
        }

        .orb-1 { width: 300px; height: 300px; background: rgba(52, 211, 153, 0.3); top: -100px; left: -100px; }
        .orb-2 { width: 400px; height: 400px; background: rgba(16, 185, 129, 0.2); bottom: -150px; right: -50px; animation-delay: -5s; }

        @keyframes float {
            0% { transform: translateY(0) scale(1); }
            100% { transform: translateY(30px) scale(1.1); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px rgba(2, 44, 34, 0.8) inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen antialiased text-white relative">

    <div class="bg-agro"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="w-full max-w-md px-6 animate-fade-in-up">
        <div class="glass-card rounded-3xl p-8 sm:p-10 relative overflow-hidden">

            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-400 to-green-600"></div>

            <div class="text-center mb-8 mt-2">
                <div class="inline-flex items-center justify-center mb-4">
                    <img src="{{ asset('images/logo-alt.png') }}" alt="Suministros Sustentables Logo" class="h-28 w-auto drop-shadow-md">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white mb-2">¿Olvidaste tu contraseña?</h1>
                <p class="text-sm text-gray-200 leading-relaxed">
                    Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                </p>
            </div>

            {{-- Mensaje de éxito --}}
            @if(session('status'))
                <div class="bg-emerald-500/20 border border-emerald-500/50 text-emerald-200 p-4 rounded-xl text-sm mb-6 flex items-start space-x-3 backdrop-blur-md">
                    <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Mensajes de error --}}
            @if($errors->any())
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-4 rounded-xl text-sm mb-6 flex items-start space-x-3 backdrop-blur-md">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('portal.password.email') }}" method="POST" class="space-y-6" id="forgot-password-form">
                @csrf

                <div class="space-y-1">
                    <label for="email" class="block text-sm font-medium text-gray-200 ml-1">Correo Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="tu@correo.com"
                            autocomplete="email"
                            class="block w-full pl-11 pr-3 py-3 border border-emerald-900/50 rounded-xl leading-5 bg-emerald-950/40 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-emerald-950/60 transition-all duration-200 sm:text-sm"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    id="submit-btn"
                    class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-emerald-900/50 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-emerald-900 focus:ring-emerald-500 transform hover:-translate-y-0.5 transition-all duration-200"
                >
                    Enviar enlace de restablecimiento
                    <svg class="ml-2 h-5 w-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('portal.login') }}" class="inline-flex items-center text-sm text-emerald-300 hover:text-emerald-200 transition-colors duration-200">
                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al inicio de sesión
                </a>
            </div>

        </div>

        <p class="text-center text-xs text-gray-300 mt-6 animate-fade-in-up" style="animation-delay: 0.2s">
            &copy; {{ date('Y') }} Sistema de Suministros Sustentables.<br>Todos los derechos reservados.
        </p>
    </div>

</body>
</html>
