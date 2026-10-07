@extends('layouts.app')
@section('content')

<div class="p-6 h-full flex flex-col gap-4">
    <div class="flex justify-between items-center w-full">
        <h1 class="text-xl font-bold">Transferencias de Producción a Almacén (Pendientes)</h1>
        <a href="{{ route('warehouse') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Volver a Almacén</a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            <span class="block sm:inline">{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200 overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-left font-bold border-b border-gray-200 bg-gray-50">
                        <th class="px-6 py-3 text-xs uppercase tracking-wider text-gray-500">Área</th>
                        <th class="px-6 py-3 text-xs uppercase tracking-wider text-gray-500">Producto Genérico</th>
                        <th class="px-6 py-3 text-xs uppercase tracking-wider text-gray-500">Descripción Local</th>
                        <th class="px-6 py-3 text-xs uppercase tracking-wider text-gray-500">Cantidad</th>
                        <th class="px-6 py-3 text-xs uppercase tracking-wider text-gray-500">Contenido Total</th>
                        <th class="px-6 py-3 text-xs uppercase tracking-wider text-gray-500">Fecha</th>
                        <th class="px-6 py-3 text-xs uppercase tracking-wider text-gray-500">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($transfers as $transfer)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ strtoupper($transfer->area) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $transfer->product->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $transfer->product_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $transfer->quantity }} {{ $transfer->unit_type ? $transfer->unit_type : 'unidades' }} ({{ $transfer->weight_per_unit }} c/u)</td>
                        <td class="px-6 py-4 text-sm text-gray-500 font-bold">{{ $transfer->total_weight }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $transfer->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <button onclick="openReceiveModal({{ $transfer->transfer_id }}, '{{ $transfer->product_name }}', {{ $transfer->total_weight }})" class="text-blue-600 hover:text-blue-900 bg-blue-100 px-3 py-1 rounded">Dar Entrada</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-sm text-gray-500 text-center">No hay transferencias pendientes.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="receive-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto overflow-hidden">
        <form id="receive-form" method="POST">
            @csrf
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recibir Transferencia</h3>
                <p id="receive-info" class="text-sm text-gray-500 mt-1"></p>
            </div>
            
            <div class="p-4 flex flex-col gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ID Ubicación (Location ID) <span class="text-red-500">*</span></label>
                    <input type="number" name="location_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comentarios</label>
                    <textarea name="comments" rows="2" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2 border"></textarea>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex justify-end gap-2">
                <button type="button" onclick="closeReceiveModal()" class="px-4 py-2 bg-red-500 text-white text-sm rounded hover:bg-red-600">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">Confirmar Recepción</button>
            </div>
        </form>
    </div>
</div>

@endsection
@push('js')
<script>
    function openReceiveModal(transferId, productName, totalWeight) {
        document.getElementById('receive-form').action = `/warehouse/production-transfers/${transferId}/receive`;
        document.getElementById('receive-info').innerText = `Recibir total de ${totalWeight} de ${productName}`;
        document.getElementById('receive-modal').classList.remove('hidden');
    }

    function closeReceiveModal() {
        document.getElementById('receive-modal').classList.add('hidden');
    }

    $(document).ready(function() {
        $('#receive-form').on('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = $(form).find('button[type="submit"]');
            
            submitBtn.prop('disabled', true).text('Procesando...');

            $.ajax({
                url: form.action,
                method: 'POST',
                data: $(form).serialize(),
                success: function(res) {
                    alert(res.message || 'Transferencia recibida con éxito.');
                    location.reload();
                },
                error: function(err) {
                    alert(err.responseJSON?.message || 'Error al procesar.');
                    submitBtn.prop('disabled', false).text('Confirmar Recepción');
                }
            });
        });
    });
</script>
@endpush
