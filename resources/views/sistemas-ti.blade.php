@extends('layouts.app')

@section('content')
<div class="px-4 py-6">
    <div class="mt-2 mb-6">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
            Sistemas TI
        </h1>
        <p class="text-sm text-gray-600">Módulo de gestión de inventario e inspección de equipos de TI.</p>
        <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <!-- Sección 1: Inventario -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Inventario</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-6">Gestión, control y seguimiento del inventario de equipos, periféricos, componentes y licencias de TI de la empresa.</p>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('sistemas-ti.inventario') }}" class="px-4 py-2 bg-[#198754] text-white rounded-md text-sm hover:bg-[#157347] transition shadow-sm font-medium">Gestionar Inventario</a>
                </div>
            </div>
        </div>

        <!-- Sección 2: Inspección de Equipos -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-300">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="p-2 bg-orange-100 rounded-lg text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-800">Inspección de Equipos</h2>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-6">Registro, programación y seguimiento de las inspecciones preventivas, correctivas y mantenimientos de los equipos.</p>
                <div class="flex justify-end mt-4">
                    <a href="{{ route('sistemas-ti.inspecciones.index') }}" class="px-4 py-2 bg-[#198754] text-white rounded-md text-sm hover:bg-[#157347] transition shadow-sm font-medium">Ver Inspecciones</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
