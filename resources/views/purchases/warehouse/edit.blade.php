<x-modal id="edit-warehouse-entry">
    <form id="edit-warehouse-form" method="POST" class="flex flex-col items-center w-full gap-2">
        @csrf
        @method('PUT')

        <x-wrapper-form-1>
            <x-tittle-form class="border-b-2 border-green-700 pb-2">Edit warehouse entry</x-tittle-form>
        </x-wrapper-form-1>

        <input type="hidden" id="wh_id" name="id">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="wh_fecha_llegada">Fecha de entrada</x-label>
                <x-input-1 required type="date" name="fecha_llegada" id="wh_fecha_llegada"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="wh_fecha_salida">Fecha de salida</x-label>
                <x-input-1 type="date" name="fecha_salida" id="wh_fecha_salida"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>

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
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="wh_proveedor">Proveedor</x-label>
                <x-input-1 required name="proveedor" id="wh_proveedor" maxlength="255"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="wh_insumo">Insumo</x-label>
                <select name="insumo" id="wh_insumo" required
                        class="w-full rounded-md border border-gray-300 px-3 py-2">
                    <option value="Directo">Directo</option>
                    <option value="Indirecto">Indirecto</option>
                </select>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="wh_lote">Lote de entrada</x-label>
                <x-input-1 name="lote" id="wh_lote" maxlength="255"></x-input-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="wh_lote_salida">Lote de salida</x-label>
                <x-input-1 name="lote_salida" id="wh_lote_salida" maxlength="255"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="wh_cantidad">Cantidad</x-label>
                <x-input-1 required type="number" step="0.001" min="0" name="cantidad" id="wh_cantidad"></x-input-1>
            </x-wrapper-form-2>

            <x-wrapper-form-2>
                <x-label for="wh_unidad">Unidad</x-label>
                <x-input-1 required name="unidad" id="wh_unidad" maxlength="20"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>



        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="wh_descripcion">Descripción</x-label>
                <x-textarea-1 name="descripcion" id="wh_descripcion" maxlength="2000"></x-textarea-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-button-1 colorBtn="red" class="close-modal" type="reset">Close</x-button-1>
            <x-button-1 colorBtn="orange" type="submit">Save</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>
