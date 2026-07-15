<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Cliente - Suministros Sustentables</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            /* Aplicamos una capa blanca con 92% de opacidad para que las imágenes del fondo sean sutiles */
            background-image: linear-gradient(rgba(248, 250, 252, 0.92), rgba(248, 250, 252, 0.92)), url("{{ asset('images/formats/agro.png') }}");
            /* Controla aquí el tamaño del patrón (ej. 100px de ancho) */
            background-size: 100px auto;
            background-repeat: repeat;
            background-attachment: fixed;
        }
        
        .bg-header-agro {
            background-image: linear-gradient(to right, rgba(6, 78, 59, 0.95), rgba(2, 44, 34, 0.85)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80');
            background-size: cover;
            background-position: center 30%;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        .table-row-hover {
            transition: all 0.2s ease;
        }
        .table-row-hover:hover {
            background-color: rgba(240, 253, 244, 0.95); 
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* Modal overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(2, 44, 34, 0.7);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .modal-overlay.open { display: flex; animation: fadeIn .2s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-card {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            animation: slideUp .25s cubic-bezier(.16,1,.3,1);
        }
        @keyframes slideUp { from { transform: translateY(24px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e2e8f0;
            transition: background-color .3s;
        }
    </style>
</head>
<body class="antialiased text-slate-800 flex flex-col min-h-screen">
    
    <header class="bg-header-agro text-white py-8 px-6 lg:px-12 shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-400 rounded-full mix-blend-overlay filter blur-[80px] opacity-40"></div>
        
        <div class="w-full mx-auto relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="bg-white/10 p-3 rounded-2xl backdrop-blur-md border border-white/20 shadow-xl">
                    <img src="{{ asset('images/logo-alt.png') }}" alt="Logo" class="h-16 w-auto drop-shadow-md">
                </div>
                <div>
                    <h1 class="text-3xl font-bold tracking-tight mb-1">Panel de Cliente</h1>
                    <p class="text-emerald-100/80 font-medium">Empresa: <span class="text-white">{{ Auth::guard('client')->user()->empresa }}</span></p>
                </div>
            </div>
            
        <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-sm text-emerald-100/70">Bienvenido(a)</p>
                    <p class="font-semibold text-lg">{{ Auth::guard('client')->user()->nombre_contacto }}</p>
                </div>

                {{-- Botón Cambiar Contraseña --}}
                <button
                    type="button"
                    id="btn-cambiar-password"
                    onclick="document.getElementById('modal-change-password').classList.add('open')"
                    class="flex items-center gap-2 px-4 py-2.5 bg-emerald-500/20 hover:bg-emerald-500/40 text-emerald-100 border border-emerald-400/30 rounded-xl transition-all duration-200 font-medium group"
                    title="Cambiar contraseña"
                >
                    <i class="fas fa-key group-hover:rotate-12 transition-transform"></i>
                    <span class="hidden sm:inline">Contraseña</span>
                </button>

                <form action="{{ route('portal.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-5 py-2.5 bg-red-500/20 hover:bg-red-500/40 text-red-100 border border-red-500/30 rounded-xl transition-all duration-200 font-medium group">
                        <i class="fas fa-sign-out-alt group-hover:-translate-x-1 transition-transform"></i> Cerrar Sesión
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="flex-grow w-full px-4 sm:px-6 lg:px-8 py-10 -mt-8 relative z-20">
        
        <div class="glass-panel rounded-2xl p-6 md:p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                    Historial de Facturas y Remisiones
                </h2>
                <div class="text-sm text-slate-500 font-medium">
                    <i class="fas fa-info-circle text-emerald-500 mr-1"></i> Documentos disponibles para descarga
                </div>
            </div>
            
            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-sm">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/75 text-slate-600 text-sm uppercase tracking-wider">
                            <th class="p-4 font-semibold border-b border-slate-200">Folio / ID</th>
                            <th class="p-4 font-semibold border-b border-slate-200">Fecha</th>
                            <th class="p-4 font-semibold border-b border-slate-200 text-center">Acciones y Documentos</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-slate-700 divide-y divide-slate-100">
                        @forelse($ventas as $venta)
                            @php
                                $documentosVenta = DB::table('portal_documents')
                                                     ->where('sale_id', $venta->sale_id)
                                                     ->get();
                                                     
                                $pdfManual = $documentosVenta->where('file_type', 'pdf')->first();
                                $xmlManual = $documentosVenta->where('file_type', 'xml')->first();
                                $coaManual = $documentosVenta->where('file_type', 'coa')->first();
                            @endphp

                            <tr class="table-row-hover bg-white/90">
                                <td class="p-4 font-bold text-emerald-700">
                                    #{{ $venta->folio ?? $venta->sale_id }}
                                </td>
                                <td class="p-4 font-medium text-slate-500 whitespace-nowrap">
                                    <i class="far fa-calendar-alt mr-2 text-slate-400"></i>{{ $venta->date }}
                                </td> 
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">
                                        
                                        <a href="{{ route('portal.ver-detalle', $venta->sale_id) }}" class="flex items-center gap-1.5 bg-slate-800 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-slate-700 transition shadow-sm">
                                            <i class="fas fa-eye"></i> Detalle
                                        </a>
                                        
                                        <a href="{{ route('portal.descargar-pdf', $venta->sale_id) }}" class="flex items-center gap-1.5 bg-white border border-emerald-500 text-emerald-600 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-emerald-50 transition shadow-sm">
                                            <i class="fas fa-download"></i> Recibo PDF
                                        </a>

                                        @if($pdfManual)
                                            <a href="{{ asset('storage/' . $pdfManual->file_path) }}" download="{{ $pdfManual->file_name }}" class="flex items-center gap-1.5 bg-rose-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-rose-700 transition shadow-sm" title="Descargar Factura PDF Externa">
                                                <i class="fas fa-file-pdf"></i> Factura PDF
                                            </a>
                                        @endif

                                        @if($xmlManual)
                                            <a href="{{ asset('storage/' . $xmlManual->file_path) }}" download="{{ $xmlManual->file_name }}" class="flex items-center gap-1.5 bg-amber-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-amber-600 transition shadow-sm" title="Descargar Factura XML">
                                                <i class="fas fa-file-code"></i> XML
                                            </a>
                                        @endif

                                        @if($coaManual)
                                            <a href="{{ asset('storage/' . $coaManual->file_path) }}" download="{{ $coaManual->file_name }}" class="flex items-center gap-1.5 bg-teal-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-teal-700 transition shadow-sm" title="Descargar Certificado COA">
                                                <i class="fas fa-certificate"></i> COA
                                            </a>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-inbox text-4xl mb-3 text-slate-300"></i>
                                        <p class="font-medium">No hay facturas ni remisiones disponibles en este momento.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
        </div>
        
    </main>

    {{-- ================================================================ --}}
    {{-- MODAL: Cambiar Contraseña --}}
    {{-- ================================================================ --}}
    <div id="modal-change-password" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-card">

            {{-- Header del modal --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 text-emerald-600 rounded-xl">
                        <i class="fas fa-key text-base"></i>
                    </div>
                    <div>
                        <h2 id="modal-title" class="text-base font-bold text-slate-800">Cambiar contraseña</h2>
                        <p class="text-xs text-slate-500">Actualiza las credenciales de tu cuenta</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Alerta de resultado --}}
            <div id="modal-alert" class="hidden mx-6 mt-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2"></div>

            {{-- Form --}}
            <form id="change-password-form" class="px-6 py-5 space-y-4">
                @csrf

                {{-- Contraseña actual --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Contraseña actual</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input
                            type="password"
                            id="cp-current"
                            name="current_password"
                            placeholder="Tu contraseña actual"
                            required
                            class="w-full pl-9 pr-10 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                        >
                        <button type="button" onclick="toggleVis('cp-current','eye-c1','eye-c2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-emerald-500 transition">
                            <i id="eye-c1" class="fas fa-eye text-sm"></i>
                            <i id="eye-c2" class="fas fa-eye-slash text-sm hidden"></i>
                        </button>
                    </div>
                </div>

                {{-- Nueva contraseña --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nueva contraseña</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-shield-alt text-sm"></i>
                        </span>
                        <input
                            type="password"
                            id="cp-new"
                            name="new_password"
                            placeholder="Mínimo 8 caracteres"
                            required
                            minlength="8"
                            class="w-full pl-9 pr-10 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                        >
                        <button type="button" onclick="toggleVis('cp-new','eye-n1','eye-n2')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-emerald-500 transition">
                            <i id="eye-n1" class="fas fa-eye text-sm"></i>
                            <i id="eye-n2" class="fas fa-eye-slash text-sm hidden"></i>
                        </button>
                    </div>
                    {{-- Barra de fortaleza --}}
                    <div class="mt-2 flex gap-1">
                        <div class="strength-bar flex-1" id="sb1"></div>
                        <div class="strength-bar flex-1" id="sb2"></div>
                        <div class="strength-bar flex-1" id="sb3"></div>
                        <div class="strength-bar flex-1" id="sb4"></div>
                    </div>
                    <p id="strength-label" class="text-xs text-slate-400 mt-1"></p>
                </div>

                {{-- Confirmar nueva contraseña --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Confirmar nueva contraseña</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-check-circle text-sm"></i>
                        </span>
                        <input
                            type="password"
                            id="cp-confirm"
                            name="new_password_confirmation"
                            placeholder="Repite la nueva contraseña"
                            required
                            minlength="8"
                            class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 transition"
                        >
                    </div>
                    <p id="match-msg" class="text-xs text-red-500 mt-1 hidden">Las contraseñas no coinciden.</p>
                </div>

                {{-- Footer del form --}}
                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        onclick="closeModal()"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition"
                    >Cancelar</button>
                    <button
                        type="submit"
                        id="cp-submit"
                        class="flex-1 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-green-700 hover:from-emerald-500 hover:to-green-600 text-white text-sm font-semibold shadow-md shadow-emerald-200 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
    {{-- Fin Modal --}}

    <script>
        // ── Helpers ──────────────────────────────────────────────────────────
        function toggleVis(inputId, eyeOpenId, eyeCloseId) {
            const el = document.getElementById(inputId);
            const isPass = el.type === 'password';
            el.type = isPass ? 'text' : 'password';
            document.getElementById(eyeOpenId).classList.toggle('hidden', !isPass);
            document.getElementById(eyeCloseId).classList.toggle('hidden', isPass);
        }

        function closeModal() {
            document.getElementById('modal-change-password').classList.remove('open');
            document.getElementById('change-password-form').reset();
            document.getElementById('modal-alert').classList.add('hidden');
            document.getElementById('strength-label').textContent = '';
            document.getElementById('match-msg').classList.add('hidden');
            ['sb1','sb2','sb3','sb4'].forEach(id => document.getElementById(id).style.backgroundColor = '#e2e8f0');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('modal-change-password').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // ── Indicador de fortaleza ───────────────────────────────────────────
        document.getElementById('cp-new').addEventListener('input', function() {
            const val = this.value;
            let score = 0;
            if (val.length >= 8)       score++;
            if (/[A-Z]/.test(val))     score++;
            if (/[0-9]/.test(val))     score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const colors = ['#ef4444','#f97316','#eab308','#22c55e'];
            const labels = ['Muy débil','Débil','Buena','Fuerte'];
            const bars   = ['sb1','sb2','sb3','sb4'];

            bars.forEach((id, i) => {
                document.getElementById(id).style.backgroundColor = i < score ? colors[score-1] : '#e2e8f0';
            });

            const lbl = document.getElementById('strength-label');
            lbl.textContent   = val.length ? `Seguridad: ${labels[score-1] || labels[0]}` : '';
            lbl.style.color   = score ? colors[score-1] : '#94a3b8';
        });

        // ── Validación coincidencia ───────────────────────────────────────────
        ['cp-new','cp-confirm'].forEach(id => {
            document.getElementById(id).addEventListener('input', () => {
                const match = document.getElementById('cp-new').value === document.getElementById('cp-confirm').value;
                const msg   = document.getElementById('match-msg');
                if (document.getElementById('cp-confirm').value) {
                    msg.classList.toggle('hidden', match);
                } else {
                    msg.classList.add('hidden');
                }
            });
        });

        // ── Envío AJAX ────────────────────────────────────────────────────────
        document.getElementById('change-password-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn   = document.getElementById('cp-submit');
            const alert = document.getElementById('modal-alert');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            alert.classList.add('hidden');

            try {
                const formData = new FormData(this);

                const res = await fetch('{{ route("portal.password.change") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await res.json();

                alert.classList.remove('hidden','bg-green-50','border-green-200','text-green-700','bg-red-50','border-red-200','text-red-700');

                if (data.success) {
                    alert.className = 'mx-6 mt-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 bg-green-50 border border-green-200 text-green-700';
                    alert.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                    this.reset();
                    ['sb1','sb2','sb3','sb4'].forEach(id => document.getElementById(id).style.backgroundColor = '#e2e8f0');
                    document.getElementById('strength-label').textContent = '';
                    setTimeout(closeModal, 2200);
                } else {
                    alert.className = 'mx-6 mt-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 bg-red-50 border border-red-200 text-red-700';
                    alert.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Error al actualizar.');
                }
            } catch (err) {
                const alert = document.getElementById('modal-alert');
                alert.classList.remove('hidden');
                alert.className = 'mx-6 mt-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2 bg-red-50 border border-red-200 text-red-700';
                alert.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error de conexión. Intenta de nuevo.';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save"></i> Actualizar';
            }
        });
    </script>

</body>
</html>