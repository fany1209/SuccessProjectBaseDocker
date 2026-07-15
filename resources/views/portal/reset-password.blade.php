<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva contraseña — Portal de Clientes</title>
    <meta name="description" content="Establece una nueva contraseña para tu cuenta del Portal de Clientes.">
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

        /* Strength indicator */
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            transition: width 0.3s ease, background-color 0.3s ease;
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
                <h1 class="text-2xl font-bold tracking-tight text-white mb-2">Crear nueva contraseña</h1>
                <p class="text-sm text-gray-200 leading-relaxed">
                    Ingresa y confirma tu nueva contraseña para <span class="text-emerald-300 font-medium">{{ $email }}</span>
                </p>
            </div>

            {{-- Mensajes de error --}}
            @if($errors->any())
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 p-4 rounded-xl text-sm mb-6 flex items-start space-x-3 backdrop-blur-md">
                    <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('portal.password.update') }}" method="POST" class="space-y-5" id="reset-password-form">
                @csrf

                {{-- Campos ocultos --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                {{-- Nueva contraseña --}}
                <div class="space-y-1">
                    <label for="password" class="block text-sm font-medium text-gray-200 ml-1">Nueva contraseña</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            minlength="8"
                            placeholder="Mínimo 8 caracteres"
                            autocomplete="new-password"
                            class="block w-full pl-11 pr-10 py-3 border border-emerald-900/50 rounded-xl leading-5 bg-emerald-950/40 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-emerald-950/60 transition-all duration-200 sm:text-sm"
                        >
                        {{-- Toggle visibilidad --}}
                        <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-emerald-400 transition-colors">
                            <svg id="eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-closed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Indicador de fortaleza --}}
                    <div class="mt-2 px-1">
                        <div class="flex gap-1 mb-1">
                            <div class="strength-bar flex-1 bg-white/10" id="bar-1"></div>
                            <div class="strength-bar flex-1 bg-white/10" id="bar-2"></div>
                            <div class="strength-bar flex-1 bg-white/10" id="bar-3"></div>
                            <div class="strength-bar flex-1 bg-white/10" id="bar-4"></div>
                        </div>
                        <p class="text-xs text-gray-400" id="strength-text"></p>
                    </div>
                </div>

                {{-- Confirmar contraseña --}}
                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-200 ml-1">Confirmar contraseña</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-emerald-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            required
                            minlength="8"
                            placeholder="Repite la contraseña"
                            autocomplete="new-password"
                            class="block w-full pl-11 pr-3 py-3 border border-emerald-900/50 rounded-xl leading-5 bg-emerald-950/40 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-emerald-950/60 transition-all duration-200 sm:text-sm"
                        >
                    </div>
                    <p class="text-xs text-red-400 mt-1 hidden px-1" id="match-error">Las contraseñas no coinciden.</p>
                </div>

                <button
                    type="submit"
                    id="reset-btn"
                    class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-emerald-900/50 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-emerald-900 focus:ring-emerald-500 transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                    Actualizar contraseña
                    <svg class="ml-2 h-5 w-5 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
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

    <script>
        // Toggle mostrar/ocultar contraseña
        const toggleBtn = document.getElementById('toggle-password');
        const passInput = document.getElementById('password');
        const eyeOpen   = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        toggleBtn.addEventListener('click', () => {
            const isText = passInput.type === 'text';
            passInput.type = isText ? 'password' : 'text';
            eyeOpen.classList.toggle('hidden', !isText);
            eyeClosed.classList.toggle('hidden', isText);
        });

        // Indicador de fortaleza de contraseña
        passInput.addEventListener('input', () => {
            const val = passInput.value;
            let strength = 0;
            if (val.length >= 8) strength++;
            if (/[A-Z]/.test(val)) strength++;
            if (/[0-9]/.test(val)) strength++;
            if (/[^A-Za-z0-9]/.test(val)) strength++;

            const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
            const labels = ['Muy débil', 'Débil', 'Buena', 'Fuerte'];
            const bars   = [
                document.getElementById('bar-1'),
                document.getElementById('bar-2'),
                document.getElementById('bar-3'),
                document.getElementById('bar-4'),
            ];

            bars.forEach((bar, i) => {
                bar.style.backgroundColor = i < strength ? colors[strength - 1] : 'rgba(255,255,255,0.1)';
            });

            const text = document.getElementById('strength-text');
            text.textContent = val.length > 0 ? `Seguridad: ${labels[strength - 1] || 'Muy débil'}` : '';
            text.style.color = strength > 0 ? colors[strength - 1] : '#9ca3af';
        });

        // Validación de coincidencia de contraseñas
        const passConfirm = document.getElementById('password_confirmation');
        const matchError  = document.getElementById('match-error');

        function checkMatch() {
            if (passConfirm.value && passInput.value !== passConfirm.value) {
                matchError.classList.remove('hidden');
            } else {
                matchError.classList.add('hidden');
            }
        }

        passInput.addEventListener('input', checkMatch);
        passConfirm.addEventListener('input', checkMatch);
    </script>

</body>
</html>
