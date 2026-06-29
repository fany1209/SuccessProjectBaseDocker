@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto px-4">

  <section class="col-span-12 w-full flex flex-col items-center px-1">
    <div class="flex flex-col justify-center items-center w-full">

      <div class="mt-6 text-center w-full">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
          Cuentas por cobrar
        </h1>
        <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
      </div>

      <x-section-1>
        <div class="flex justify-end items-center w-full my-1 gap-2">
          <!-- TODO: add buttons for create/add if necessary -->
          <x-button data-target="add-cuenta-por-cobrar"
            class="open-modal bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
            Añadir Cuenta
          </x-button>
        </div>
      </x-section-1>

      <div class="w-full mt-4">
        <table id="cuentas-por-cobrar-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
          <thead class="bg-gray-50 text-gray-700 uppercase text-md">
            <tr>
              <th class="px-4 py-3">ID</th>
              <th class="px-4 py-3">Cliente</th>
              <th class="px-4 py-3">Monto</th>
              <th class="px-4 py-3">Fecha Vencimiento</th>
              <th class="px-4 py-3">Estatus</th>
              <th class="px-4 py-3">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-md">
            <!-- Data will be loaded here by Datatables if implemented, or manually rendered -->
            <tr>
              <td colspan="6" class="text-center py-4 text-gray-500">Módulo en construcción. Datos no disponibles aún.</td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </section>

</div>
@endsection

@push('js')
<script>
$(function(){
  // Table initialization can go here later
});
</script>
@endpush
