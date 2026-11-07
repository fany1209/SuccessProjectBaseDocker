<x-modal id="production-modal" maxWidth="2xl">
    <form class="flex flex-col items-center w-full gap-3 p-2" id="production-form" action="{{ route('production.records.storeProduction') }}" method="POST">
        @csrf
        <input type="hidden" name="_method" id="prod-method" value="POST">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form id="prod-modal-title">Nuevo Registro de Producción</x-tittle-form>
                <p class="text-sm text-gray-500" id="prod-modal-subtitle">Selecciona la línea de producción y captura los datos de preparación y/o ensacado.</p>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <!-- Selector Visual de Línea / Área -->
        <div class="w-full px-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">1. Línea de Producción <span class="text-red-500">*</span></label>
            <input type="hidden" name="area" id="prod_area_input" value="fertil" required>
            <div class="grid grid-cols-2 gap-3">
                <button type="button" id="btn-prod-area-fertil" onclick="selectProdArea('fertil')" class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-green-600 bg-green-50 text-green-800">
                    <span class="w-3 h-3 rounded-full bg-green-600"></span>
                    Producción Fertil
                </button>
                <button type="button" id="btn-prod-area-vitayela" onclick="selectProdArea('vitayela')" class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50">
                    <span class="w-3 h-3 rounded-full bg-indigo-600"></span>
                    Producción Vitayela
                </button>
            </div>
        </div>

        <!-- Producto / Descripción con Autocompletado -->
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="prod_descripcion">Producto o Descripción de la Corrida <span class="text-red-500">*</span></x-label>
                <x-input-1 type="text" list="prod_suggestions_list" name="descripcion" id="prod_descripcion" placeholder="Selecciona de la lista o escribe el nombre del producto..." required autocomplete="off"></x-input-1>
                <datalist id="prod_suggestions_list">
                    @foreach($inventories->pluck('producto_descripcion')->merge($productions->pluck('descripcion'))->filter()->unique() as $desc)
                        <option value="{{ $desc }}"></option>
                    @endforeach
                </datalist>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <!-- Fase 1: Preparación -->
        <div class="w-full bg-gray-50 border border-gray-200 rounded-lg p-3">
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700">2. Datos de Preparación</span>
                <button type="button" onclick="document.getElementById('prod_fecha_preparacion').value = getTodayIso()" class="text-xs text-blue-600 hover:underline font-semibold cursor-pointer">Usar fecha de hoy</button>
            </div>
            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <x-label for="prod_fecha_preparacion">Fecha de Preparación</x-label>
                    <x-input-1 type="date" name="fecha_preparacion" id="prod_fecha_preparacion"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label for="prod_kg_preparados">Kg Preparados</x-label>
                    <x-input-1 type="number" step="0.01" min="0" name="kg_preparados" id="prod_kg_preparados" placeholder="Ej. 500.00"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>
        </div>

        <!-- Fase 2: Ensacado con Calculadora Automática -->
        <div class="w-full bg-emerald-50/50 border border-emerald-200 rounded-lg p-3">
            <div class="flex flex-wrap justify-between items-center gap-2 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">3. Datos de Ensacado (Opcional si aún sigue en preparación)</span>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-600">Peso por saco:</span>
                    <input type="number" step="0.5" min="1" id="helper_bag_weight" value="25" oninput="autoCalcBagging('sacks')" class="w-16 text-xs border border-gray-300 rounded px-2 py-1 text-center font-bold bg-white" title="Peso por saco para cálculo rápido">
                    <span class="text-xs text-gray-600">kg</span>
                </div>
            </div>

            <x-wrapper-form-1>
                <x-wrapper-form-2>
                    <div class="flex justify-between items-center">
                        <x-label for="prod_fecha_ensacado">Fecha de Ensacado</x-label>
                        <button type="button" onclick="document.getElementById('prod_fecha_ensacado').value = getTodayIso()" class="text-[11px] text-emerald-700 hover:underline font-semibold cursor-pointer">Hoy</button>
                    </div>
                    <x-input-1 type="date" name="fecha_ensacado" id="prod_fecha_ensacado"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label for="prod_num_sacos"># de Sacos</x-label>
                    <x-input-1 type="number" step="1" min="0" name="num_sacos" id="prod_num_sacos" placeholder="Ej. 20" oninput="autoCalcBagging('sacks')"></x-input-1>
                </x-wrapper-form-2>
                <x-wrapper-form-2>
                    <x-label for="prod_kg_ensacados">Kg Ensacados Totales</x-label>
                    <x-input-1 type="number" step="0.01" min="0" name="kg_ensacados" id="prod_kg_ensacados" placeholder="Se calcula automático"></x-input-1>
                </x-wrapper-form-2>
            </x-wrapper-form-1>
        </div>

        <x-wrapper-form-1 class="mt-2">
            <x-button-1 class="close-modal" type="button" onclick="closeProductionModal()" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" colorBtn="green">Guardar Registro</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

<script>
    function getTodayIso() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    function selectProdArea(area) {
        document.getElementById('prod_area_input').value = area;
        const btnFertil = document.getElementById('btn-prod-area-fertil');
        const btnVitayela = document.getElementById('btn-prod-area-vitayela');

        if (area === 'fertil') {
            btnFertil.className = 'flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-green-600 bg-green-50 text-green-800 shadow-xs';
            btnVitayela.className = 'flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
        } else {
            btnVitayela.className = 'flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-indigo-600 bg-indigo-50 text-indigo-800 shadow-xs';
            btnFertil.className = 'flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg border-2 font-bold text-sm transition-all cursor-pointer border-gray-200 bg-white text-gray-600 hover:bg-gray-50';
        }
    }

    function autoCalcBagging(source) {
        const numSacos = parseFloat(document.getElementById('prod_num_sacos').value) || 0;
        const bagWeight = parseFloat(document.getElementById('helper_bag_weight').value) || 25;

        if (source === 'sacks' && numSacos > 0) {
            document.getElementById('prod_kg_ensacados').value = (numSacos * bagWeight).toFixed(2);
            if (!document.getElementById('prod_fecha_ensacado').value) {
                document.getElementById('prod_fecha_ensacado').value = getTodayIso();
            }
        }
    }

    function openCreateProduction() {
        document.getElementById('production-form').action = "{{ route('production.records.storeProduction') }}";
        document.getElementById('prod-method').value = "POST";
        document.getElementById('prod-modal-title').innerText = "Nuevo Registro de Producción";
        document.getElementById('prod-modal-subtitle').innerText = "Selecciona la línea de producción y captura los datos de preparación y/o ensacado.";

        document.getElementById('production-form').reset();
        document.getElementById('helper_bag_weight').value = 25;
        document.getElementById('prod_fecha_preparacion').value = getTodayIso();

        const defaultArea = (window.currentAreaFilter === 'vitayela') ? 'vitayela' : 'fertil';
        selectProdArea(defaultArea);

        document.getElementById('production-modal').classList.remove('hidden');
        setTimeout(() => document.getElementById('prod_descripcion').focus(), 100);
    }

    function editProduction(p, focusBagging = false) {
        document.getElementById('production-form').action = `/production/records/productions/${p.production_record_id}`;
        document.getElementById('prod-method').value = "PUT";
        document.getElementById('prod-modal-title').innerText = focusBagging
            ? `Completar Ensacado (#${p.production_record_id})`
            : `Editar Registro de Producción (#${p.production_record_id})`;

        selectProdArea(p.area || 'fertil');

        document.getElementById('prod_fecha_preparacion').value = p.fecha_preparacion ? String(p.fecha_preparacion).substring(0, 10) : '';
        document.getElementById('prod_kg_preparados').value = p.kg_preparados ?? '';
        document.getElementById('prod_fecha_ensacado').value = p.fecha_ensacado ? String(p.fecha_ensacado).substring(0, 10) : (focusBagging ? getTodayIso() : '');
        document.getElementById('prod_kg_ensacados').value = p.kg_ensacados ?? '';
        document.getElementById('prod_num_sacos').value = p.num_sacos ?? '';
        document.getElementById('prod_descripcion').value = p.descripcion ?? '';

        document.getElementById('production-modal').classList.remove('hidden');
        if (focusBagging) {
            setTimeout(() => document.getElementById('prod_num_sacos').focus(), 100);
        }
    }

    function closeProductionModal() {
        document.getElementById('production-modal').classList.add('hidden');
    }

    $(function() {
        $('#production-form').submit(function(event) {
            event.preventDefault();
            const form = this;
            const data = new FormData(form);
            const saveBtn = $(form).find('button[type="submit"]');

            saveBtn.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: form.action,
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: response.message || 'El registro fue guardado correctamente.',
                        timer: 1400,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'No se pudo guardar el registro.'
                    });
                    saveBtn.prop('disabled', false);
                }
            });
        });
    });
</script>

