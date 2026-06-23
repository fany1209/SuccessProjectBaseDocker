@extends('layouts.app')

@section('content')
<div class="mx-4 py-6">

    <div class="flex justify-between items-center mb-6">
        <div class="flex w-44 h-10">
            <label class="relative inline-flex cursor-pointer items-center w-full">
                <input type="checkbox" id="toggleSalesView" class="peer sr-only" />
                <div class="peer flex h-full w-full items-center justify-center gap-10 rounded-md bg-blue-500
                    after:absolute after:left-0 after:h-full after:w-1/2 after:rounded-md after:bg-white/40
                    after:transition-all after:content-[''] peer-checked:bg-purple-600 peer-checked:after:translate-x-full
                    peer-focus:outline-none duration-300 text-sm text-white font-bold shadow-md">
                    <span>Quotes</span>
                    <span class="mr-2">Sales</span>
                </div>
            </label>
        </div>

        <div id="viewStatus" class="w-52 h-10 bg-blue-500 flex items-center justify-center text-white font-bold rounded-md shadow-md cursor-pointer hover:bg-blue-600 transition-all">
            <p>+ ADD QUOTE</p>
        </div>
    </div>

    <div id="createQuoteForm" class="hidden mt-4 mb-8 bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-inner">
        @include('sales.quotes.form-create')
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
        <div class="flex-1 w-full md:w-auto">
            <input type="text" id="searchInput" placeholder="Buscar por folio o empresa..." 
                   class="w-full md:w-96 border border-gray-300 rounded-lg px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" />
        </div>
        
        <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full font-bold shadow-sm border border-blue-200 text-sm">
            Total Cotizaciones: <span id="allQuotesCount">{{ $quotes->count() }}</span>
        </div>
    </div>

    <div id="quotesTableContainer" class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <table id="quotesTable" class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-[11px]">
                <tr>
                    <th class="px-6 py-4 font-bold text-gray-500 uppercase text-center">FOLIO</th>
                    <th class="px-6 py-4 font-bold text-gray-500 uppercase text-center">DATE</th>
                    <th class="px-6 py-4 font-bold text-gray-500 uppercase text-center">COMPANY</th>
                    <th class="px-6 py-4 font-bold text-gray-500 uppercase text-center">ATTENTION</th>
                    <th class="px-6 py-4 font-bold text-gray-500 uppercase text-center">STATUS</th> 
                    <th class="px-6 py-4 font-bold text-gray-500 uppercase text-center">TOTAL (INC. IVA)</th>
                    <th class="px-6 py-4 font-bold text-gray-500 uppercase text-center">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($quotes as $quote)
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="px-6 py-4 text-center font-mono font-bold text-blue-600 text-sm">
                        {{ $quote->folio }}
                    </td>
                   <td class="px-6 py-4 text-center text-sm">
                        <div class="flex flex-col items-center">
                            <span class="text-gray-700 font-medium">
                                {{ $quote->date ? \Carbon\Carbon::parse($quote->date)->format('d/m/Y') : 'N/A' }}
                            </span>

                            @php
                                $dias = $quote->date ? (int) \Carbon\Carbon::parse($quote->date)->diffInDays(now()) : 0;
                                
                                $esMia = $quote->user_id == auth()->id();
                                $estaPendiente = $quote->quotes_status_id != 3; 
                                $esVieja = $dias >= 7;
                            @endphp

                            @if($esMia && $estaPendiente && $esVieja)
                                <span class="mt-1 inline-flex items-center px-2 py-1 rounded-full text-[10px] font-black bg-red-100 text-red-700 border border-red-200 animate-bounce shadow-sm" 
                                    style="white-space: nowrap;">
                                        Esta cotización tiene {{ $dias }} días sin ser aprobada.
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-700 font-medium uppercase">
                        {{ $quote->company ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-700">
                        {{ $quote->attention ?? 'N/A' }}
                    </td>
                     <td class="px-6 py-4 text-center text-sm">
                            <select onchange="updateQuoteStatus({{ $quote->quote_id }}, this.value)" 
                                    class="px-2 py-1 rounded-full text-[10px] font-bold border focus:outline-none cursor-pointer
                                    {{ match($quote->quotes_status_id) {
                                        1 => 'bg-gray-100 text-gray-800 border-gray-200', 
                                        2 => 'bg-blue-100 text-blue-800 border-blue-200',   
                                        3 => 'bg-green-100 text-green-800 border-green-200', 
                                        4 => 'bg-red-100 text-red-800 border-red-200',     
                                        default => 'bg-gray-100 text-gray-800 border-gray-200',
                                    } }}">
                                <option value="1" {{ $quote->quotes_status_id == 1 ? 'selected' : '' }}>BORRADOR</option>
                                <option value="2" {{ $quote->quotes_status_id == 2 ? 'selected' : '' }}>ENVIADA</option>
                                <option value="3" {{ $quote->quotes_status_id == 3 ? 'selected' : '' }}>ACEPTADA</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-bold text-gray-900 font-mono bg-gray-50/50">
                            @php
                                $totalConIvaReal = 0;
                                foreach($quote->details as $item) {
                                    $subtotalFila = $item->quantity * $item->cost;
                                    $totalConIvaReal += $subtotalFila + ($subtotalFila * ($item->iva ?? 0.16));
                                }
                            @endphp
                            ${{ number_format($totalConIvaReal, 2) }}
                        </td>
                   <td class="px-6 py-4 text-center">
                        <div class="flex justify-center items-center space-x-2">
                            <a href="{{ route('quotes.edit', $quote->quote_id) }}" 
                            class="p-2 bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-all transform hover:scale-105" 
                            title="Editar">
                                <img width="18" src="{{ asset('images/editar.png') }}" class="icon-white">
                            </a>

                            <button onclick="viewQuoteDetails({{ $quote->quote_id }})" 
                                    class="p-2 bg-yellow-500 rounded-lg hover:bg-yellow-600 shadow-sm transition-all transform hover:scale-105" 
                                    title="Ver Detalles">
                                <img width="20" src="{{ asset('images/ver.png') }}" class="icon-white">
                            </button>

                            <a href="{{ route('quotes.pdf', $quote->quote_id) }}" target="_blank"
                            class="p-2 bg-cyan-600 rounded-lg hover:bg-cyan-700 shadow-sm transition-all transform hover:scale-105" 
                            title="Generar PDF">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </a>

                            <button onclick="deleteQuote({{ $quote->quote_id }})" 
                                    class="p-2 bg-red-600 rounded-lg hover:bg-red-700 shadow-sm transition-all transform hover:scale-105" 
                                    title="Borrar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = $('#quotesTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json' },
        pageLength: 10,
        order: [[0, 'desc']], 
        dom: 'rtip', 
        responsive: true
    });
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            table.search(this.value).draw();
        });
    }

    const toggle = document.getElementById('toggleSalesView');
    if (toggle) {
        toggle.checked = false;
        toggle.addEventListener('change', function () {
            if (this.checked) window.location.href = '/sales';
        });
    }

    const viewStatus = document.getElementById('viewStatus');
    const createQuoteForm = document.getElementById('createQuoteForm');

    if (viewStatus && createQuoteForm) {
        viewStatus.addEventListener('click', function() {
            createQuoteForm.classList.toggle('hidden');
            const p = viewStatus.querySelector('p');
            if (!createQuoteForm.classList.contains('hidden')) {
                p.innerText = "✕ CLOSE";
                viewStatus.classList.replace('bg-blue-500', 'bg-gray-500');
                createQuoteForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                p.innerText = "ADD QUOTE";
                viewStatus.classList.replace('bg-gray-500', 'bg-blue-500');
            }
        });
    }
});

function viewQuoteDetails(id) {
    window.location.href = `/quotes/${id}`;
}

function deleteQuote(id) {
    Swal.fire({
        title: '¿Eliminar cotización?',
        text: "Se borrarán todos los productos asociados. Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3b82f6',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Eliminando...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            $.ajax({
                type: 'POST',
                url: `/quotes/${id}`,
                data: {
                    _token: "{{ csrf_token() }}",
                    _method: 'DELETE'
                },
                success: function(res) {
                    Swal.fire({ 
                        icon: 'success', 
                        title: 'Éxito', 
                        text: res.message || 'Cotización eliminada correctamente' 
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr) {
                    let msg = 'No se pudo eliminar la cotización.';
                    if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    
                    Swal.fire({ 
                        icon: 'error', 
                        title: 'Error', 
                        text: msg 
                    });
                }
            });
        }
    })
}

function updateQuoteStatus(id, statusId) {
    Swal.fire({
        title: 'Actualizando estatus...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading() }
    });

    $.ajax({
        type: 'POST',
        url: `/quotes/${id}/update-status`, 
        data: {
            _token: "{{ csrf_token() }}",
            _method: 'PATCH',
            quotes_status_id: statusId
        },
        success: function(res) {
            Swal.fire({ 
                icon: 'success', 
                title: 'Estatus actualizado', 
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1000 
            }).then(() => {
                window.location.reload(); 
            });
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar el estatus' });
        }
    });
}
</script>
@endpush