@php
    $imgCls   = 'h-16 w-16 bg-green-400 rounded-md p-1 object-contain pointer-events-none select-none me-2';
    $monedas  = ['MXN','USD','EUR'];
@endphp

<x-modal id="g-warehouse">
    <form class="flex flex-col items-center w-full gap-2"
          action="{{ route('insumos_entradas.store') }}"
          method="POST"
          id="insumos-entrada-form">
        @csrf

        <x-wrapper-form-1>
            <img src="{{ asset('images/purchases/02.png') }}" alt="Insumos entrada" class="{{ $imgCls }}">
            <x-tittle-form class="border-b-2 border-green-700 pb-2">Entrada de insumos</x-tittle-form>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="fecha_llegada">Fecha llegada</x-label>
                <x-input-1 required type="date" name="fecha_llegada" id="fecha_llegada"
                           value="{{ old('fecha_llegada') }}"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="supplier_id">Proveedor</x-label>
                <select name="supplier_id" id="supplier_id" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2">
                    <option value="">— Selecciona —</option>

                    @foreach(($suppliers ?? []) as $s)
                        <option value="{{ $s->supplier_id }}" @selected(old('supplier_id') == $s->supplier_id)>
                            {{ $s->name }}
                        </option>
                    @endforeach

                    <option value="__other__" @selected(old('supplier_id') === '__other__')>Otro…</option>
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 id="supplierOtherWrapper" class="hidden">
            <x-wrapper-form-2>
                <x-label for="supplier_name">Nombre del proveedor</x-label>
                <x-input-1 name="supplier_name" id="supplier_name" maxlength="255"
                           value="{{ old('supplier_name') }}"
                           placeholder="Ej: Química del Bajío"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="sector_id">Sector</x-label>
                <select name="sector_id" id="sector_id"
                        class="w-full rounded-md border border-gray-300 px-3 py-2">
                    <option value="">— Selecciona —</option>
                    @foreach(($sectors ?? []) as $sec)
                        <option value="{{ $sec->sector_id }}" @selected(old('sector_id') == $sec->sector_id)>
                            {{ $sec->name }}
                        </option>
                    @endforeach
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="insumo">Insumo</x-label>
                <select name="insumo" id="insumo" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2">
                    <option value="">— Selecciona —</option>
                    <option value="Directo" @selected(old('insumo') === 'Directo')>Directo</option>
                    <option value="Indirecto" @selected(old('insumo') === 'Indirecto')>Indirecto</option>
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="cantidad">Cantidad</x-label>
                <x-input-1 required type="number" step="0.001" min="0" name="cantidad" id="cantidad"
                           value="{{ old('cantidad') }}"
                           placeholder="0.000"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="unidad">Unidad (escríbela)</x-label>
                <x-input-1 required name="unidad" id="unidad" maxlength="20"
                           value="{{ old('unidad') }}"
                           placeholder="Ej: kg, g, ml, l, pza, caja, bidón..."></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="costo">Costo</x-label>
                <x-input-1 required type="number" step="0.0001" min="0" name="costo" id="costo"
                           value="{{ old('costo') }}"
                           placeholder="0.0000"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="moneda">Moneda</x-label>
                <select name="moneda" id="moneda" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2">
                    @foreach($monedas as $m)
                        <option value="{{ $m }}" @selected(old('moneda', 'MXN') === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-2>
            <x-label for="categoria">Categoría</x-label>
            <select name="categoria" id="categoria" required class="w-full rounded-md border border-gray-300 px-3 py-2">
                <option value="warehouse" @selected(old('categoria','warehouse')==='warehouse')>Almacén</option>
                <option value="purchases" @selected(old('categoria')==='purchases')>Compras</option>
                <option value="laboratory" @selected(old('categoria')==='laboratory')>Laboratorio</option>
                <option value="quality" @selected(old('categoria')==='quality')>Calidad</option>
                <option value="Human resources" @selected(old('categoria')==='human resources')>Recursos humanos</option>
                <option value="finance" @selected(old('categoria')==='finance')>Finance</option>
            </select>
        </x-wrapper-form-2>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="descripcion">Descripción</x-label>
                <x-textarea-1 name="descripcion" id="descripcion" maxlength="2000"
                              placeholder="Notas: presentación, lote, observaciones, etc.">{{ old('descripcion') }}</x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 colorBtn="red" class="close-modal" type="reset">Close</x-button-1>
            <x-button-1 colorBtn="orange" type="submit">Guardar</x-button-1>
        </x-wrapper-form-1>

    </form>
</x-modal>

@push('js')
<script>
$(function () {
    const modal = $('#g-warehouse');

    function toggleSupplierOther(){
        const v = modal.find('#supplier_id').val();
        const $wrap = modal.find('#supplierOtherWrapper');

        if (v === '__other__') {
            $wrap.removeClass('hidden');
            modal.find('#supplier_name').prop('required', true);
            modal.find('#sector_id').prop('required', true);
        } else {
            $wrap.addClass('hidden');
            modal.find('#supplier_name').prop('required', false).val('');
            modal.find('#sector_id').prop('required', false).val('');
        }
    }

    modal.on('change', '#supplier_id', toggleSupplierOther);
    $(document).on('click', '.open-modal', function(){
        const target = $(this).data('target');
        if (target === 'g-warehouse') {
            setTimeout(toggleSupplierOther, 50);
        }
    });

    toggleSupplierOther();

    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Saved',
            text: @json(session('success')),
            confirmButtonText: 'OK'
        });
    @endif

    @if ($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Check the fields',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonText: 'OK'
        });
    @endif
});
</script>
@endpush
