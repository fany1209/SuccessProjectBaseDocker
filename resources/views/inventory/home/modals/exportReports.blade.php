<x-modal id="export-reports">
    <x-wrapper-form-1>
        <x-tittle-form>Export Reports (Excel)</x-tittle-form>
    </x-wrapper-form-1>

    <form action="{{ route('reports') }}" method="GET" target="_blank" class="w-full flex flex-col gap-2 p-2">
        <label for="year" class="text-sm font-semibold text-gray-700">Seleccionar Año:</label>
        <select name="year" id="year" class="border border-gray-300 rounded-md p-2 focus:ring-green-500 focus:border-green-500">
            <option value="">Últimos 6 meses (Default)</option>
            @for($i = date('Y'); $i >= 2020; $i--)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>
        
        <div class="mt-4 w-full">
            <x-button-1 type="submit" colorBtn="green" class="w-full justify-center">
                <i class="ri-file-excel-2-line"></i> Descargar Reporte
            </x-button-1>
        </div>
    </form>

    <x-wrapper-form-1>
        <x-button type="button" class="close-modal w-full">Cerrar</x-button>
    </x-wrapper-form-1>
</x-modal>
