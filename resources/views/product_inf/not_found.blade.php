<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Código QR No Válido | Success ERP</title>
    <link rel="icon" href="{{ asset('images/successIconG.ico') }}" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-slate-100 via-gray-50 to-slate-200 min-h-screen flex items-center justify-center p-4 font-sans antialiased">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center">
        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
            <i class="ri-qr-code-line text-4xl"></i>
        </div>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 uppercase tracking-wider mb-3">
            QR No Encontrado
        </span>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Código QR No Válido o Expirado</h1>
        <p class="text-gray-600 text-sm mb-6 leading-relaxed">
            No se encontró información asociada al identificador único especificado en nuestra base de datos. Es posible que el producto haya sido retirado o el código sea incorrecto.
        </p>

        @if(!empty($uuid))
        <div class="bg-gray-50 border border-dashed border-gray-200 rounded-lg p-3 mb-6 text-xs text-gray-500 font-mono break-all select-all">
            UUID: {{ $uuid }}
        </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @auth
            <a href="{{ route('product-inf.index') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#198754] text-white font-medium rounded-lg shadow-sm hover:bg-[#157347] transition text-sm">
                <i class="ri-dashboard-line mr-2"></i> Ir a Product Inf
            </a>
            @else
            <a href="{{ route('main-menu') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-800 text-white font-medium rounded-lg shadow-sm hover:bg-gray-700 transition text-sm">
                <i class="ri-home-4-line mr-2"></i> Ir al Menú Principal
            </a>
            @endauth
        </div>
    </div>
</body>
</html>
