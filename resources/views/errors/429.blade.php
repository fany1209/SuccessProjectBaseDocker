<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 - Límite de solicitudes excedido | Success Suministros</title>
    <link rel="icon" href="{{ asset('images/successIconG.ico') }}" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen flex items-center justify-center p-4 antialiased">
    <div class="max-w-md w-full bg-slate-800/90 border border-slate-700/80 rounded-2xl p-8 shadow-2xl backdrop-blur-md text-center">
        <!-- Shield / Speedometer Icon -->
        <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-500/10 text-amber-400 rounded-full mb-6 ring-8 ring-amber-500/5">
            <i class="ri-shield-flash-line text-4xl"></i>
        </div>

        <!-- Title & Status -->
        <div class="inline-block px-3 py-1 bg-amber-500/20 text-amber-300 text-xs font-semibold rounded-full uppercase tracking-wider mb-3">
            Error 429 &bull; Rate Limit Exceeded
        </div>
        <h1 class="text-2xl font-bold text-white mb-3">
            Demasiadas Solicitudes
        </h1>

        <!-- Explanation -->
        <p class="text-slate-300 text-sm leading-relaxed mb-6">
            {{ $message ?? 'Has alcanzado el límite de operaciones permitidas en un periodo corto de tiempo. Esta medida protege la integridad y el rendimiento de nuestros servidores.' }}
        </p>

        <!-- Countdown / Cooldown Box -->
        @if(isset($retryAfter) && $retryAfter > 0)
        <div class="bg-slate-900/70 border border-slate-700/50 rounded-xl p-4 mb-6">
            <div class="text-xs text-slate-400 mb-1">Podrás intentar nuevamente en aproximadamente:</div>
            <div class="text-2xl font-mono font-bold text-amber-400" id="countdown">
                {{ $retryAfter }}s
            </div>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="window.history.back()" 
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm font-medium transition-colors duration-150">
                <i class="ri-arrow-left-line"></i>
                <span>Regresar</span>
            </button>
            <a href="{{ url('/') }}" 
               class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-sm font-medium transition-colors duration-150">
                <i class="ri-home-4-line"></i>
                <span>Ir al Inicio</span>
            </a>
        </div>

        <!-- Footer Notice -->
        <p class="mt-6 text-xs text-slate-500">
            Success Suministros Sustentables &bull; Sistema de Seguridad y Protección CEDIS
        </p>
    </div>

    @if(isset($retryAfter) && $retryAfter > 0)
    <script>
        (function() {
            let seconds = {{ (int) $retryAfter }};
            const el = document.getElementById('countdown');
            const interval = setInterval(() => {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(interval);
                    el.innerText = '¡Listo! Puedes reintentar ahora.';
                    el.classList.remove('text-amber-400');
                    el.classList.add('text-emerald-400');
                } else {
                    el.innerText = seconds + 's';
                }
            }, 1000);
        })();
    </script>
    @endif
</body>
</html>
