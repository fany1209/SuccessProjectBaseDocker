<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Cliente</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Bienvenido, {{ Auth::guard('client')->user()->nombre_contacto }}</h1>
        <p class="text-gray-600">Empresa: {{ Auth::guard('client')->user()->empresa }}</p>
        
        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Tus Documentos</h2>
            <p>Aquí aparecerá la lista de tus facturas y remisiones próximamente.</p>
        </div>

        <table class="w-full mt-4 bg-white border">
    <thead>
        <tr class="bg-gray-200">
            <th class="p-2">Folio</th>
            <th class="p-2">Fecha</th>
            <th class="p-2">Total</th>
            <th class="p-2">Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ventas as $venta)
<tr class="hover:bg-gray-50">
    <td class="p-3 font-medium text-blue-600">#{{ $venta->folio ?? $venta->sale_id }}</td>
    <td class="p-3 text-gray-600">{{ $venta->date }}</td> <td class="p-3 font-bold text-gray-900">${{ number_format($venta->total_calculado ?? 0, 2) }}</td>
    <td class="p-3 text-center">
        <a href="{{ route('portal.ver-detalle', $venta->sale_id) }}" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 inline-block mr-2">Ver Detalle</a>
        <a href="{{ route('portal.descargar-pdf', $venta->sale_id) }}" class="border border-green-600 text-green-600 px-3 py-1 rounded text-sm hover:bg-green-50 inline-block">
    Descargar PDF
</a>
    </td>
</tr>
@endforeach
    </tbody>
</table>
        <form action="{{ route('portal.logout') }}" method="POST" class="mt-8">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800">Cerrar Sesión</button>
        </form>
    </div>
</body>
</html>