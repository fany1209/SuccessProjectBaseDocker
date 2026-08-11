@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pb-12">
  <section class="col-span-12 w-full flex flex-col items-center px-1">
    
    <!-- Encabezado -->
    <div class="mt-6 text-center w-full relative">
      <a href="{{ route('sistemas-ti.index') }}" class="absolute left-0 top-0 text-[#198754] hover:text-[#157347] transition flex items-center gap-1 font-semibold">
        <i class="fas fa-chevron-left text-xl"></i> Volver
      </a>

      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        <i class="fas fa-list-check mr-1"></i> Historial de Inspecciones
      </h1>
      <p class="text-sm text-gray-600 mt-1">Registro y seguimiento de cédulas técnicas de equipos</p>
      <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
    </div>

    @if(session('success'))
    <div class="w-full mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
            <span class="block sm:inline font-medium"><i class="fas fa-check-circle mr-1"></i> {{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Barra de herramientas -->
    <div class="mb-2 mt-6 flex justify-between items-end w-full flex-wrap gap-4">
      <div>
        <a href="{{ route('sistemas-ti.inspecciones.create') }}" class="bg-[#198754] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#157347] transition flex items-center gap-1 shadow-sm">
            <i class="fas fa-plus mr-1"></i> Nueva Inspección
        </a>
      </div>
      <div class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-xl p-3 shadow-sm flex items-center flex-wrap gap-3">
        <!-- Buscador simple opcional (se puede implementar después en el controlador) -->
        <form action="{{ route('sistemas-ti.inspecciones.index') }}" method="GET" class="flex items-center flex-wrap gap-3 m-0">
          <div class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]" placeholder="Buscar por folio o serie...">
          </div>
          <button type="submit" class="bg-[#217346] text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-[#1e6b40] transition flex items-center gap-1 shadow-sm">
            <i class="fas fa-search"></i> Buscar
          </button>
        </form>
      </div>
    </div>

    <!-- Tabla de Inspecciones -->
    <div class="w-full mt-4">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto w-full p-4">
            <table class="display w-full divide-y divide-gray-200 text-sm text-left">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs tracking-wider">
                    <tr>
                        <th class="px-3 py-3">Folio</th>
                        <th class="px-3 py-3">Fecha</th>
                        <th class="px-3 py-3">Equipo</th>
                        <th class="px-3 py-3">Serie</th>
                        <th class="px-3 py-3">Ubicación / Área</th>
                        <th class="px-3 py-3 text-center w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    @forelse($inspections as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-3 py-3">
                            <span class="font-bold text-[#198754]">{{ $item->folio }}</span>
                        </td>
                        <td class="px-3 py-3 font-medium text-gray-600">
                            {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-3 py-3">
                            <div class="text-sm font-semibold text-gray-800">{{ $item->brand ?: 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->model ?: 'N/A' }}</div>
                        </td>
                        <td class="px-3 py-3">
                            <span class="px-2 py-1 bg-blue-50 text-blue-800 rounded text-xs font-semibold">{{ $item->serial_number ?: 'N/A' }}</span>
                        </td>
                        <td class="px-3 py-3">
                            <div class="text-sm text-gray-800">{{ $item->location ?: 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->area ?: 'N/A' }}</div>
                        </td>
                        <td class="px-3 py-3 text-center">
                            <a href="{{ route('sistemas-ti.inspecciones.print', $item->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-gray-100 border border-gray-300 text-gray-700 hover:bg-gray-200 transition font-medium text-xs shadow-sm inline-flex items-center" title="Imprimir formato">
                                <i class="fas fa-print mr-1"></i> Imprimir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500 font-medium">No se encontraron inspecciones registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
