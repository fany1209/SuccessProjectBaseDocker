<section class="col-span-12 w-full flex flex-col items-center px-1 pb-20">
    <div class="mt-6 w-full max-w-6xl">
        <h1 class="text-2xl font-bold text-[#0d6efd]">Análisis de Ventas por Cliente</h1>
        <div class="mt-2 h-px bg-gray-200"></div>
    </div>

    <div id="seccion_records" class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-5xl mt-6 hidden">
        <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-6 rounded-3xl shadow-xl text-white">
            <p class="text-blue-100 text-xs font-bold uppercase tracking-wider">Récord Histórico de Ventas ($)</p>
            <h2 id="record_mes_dinero" class="text-2xl font-black mt-1">---</h2>
            <p id="record_monto_dinero" class="text-blue-100 text-sm mt-2 font-medium"></p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-green-800 p-6 rounded-3xl shadow-xl text-white">
            <p class="text-green-100 text-xs font-bold uppercase tracking-wider">Récord Histórico de Volumen (Kg)</p>
            <h2 id="record_mes_kg" class="text-2xl font-black mt-1">---</h2>
            <p id="record_monto_kg" class="text-green-100 text-sm mt-2 font-medium"></p>
        </div>
    </div>

    <div class="w-full max-w-5xl mt-6 bg-white p-6 rounded-3xl shadow-lg border border-gray-100">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-5">
                <label class="text-xs font-bold text-gray-400 mb-1 block uppercase">Cliente</label>
                <input type="text" id="input_cliente_busqueda" placeholder="Buscar cliente..." class="w-full rounded-xl border-gray-200 py-2.5 px-4 outline-none border focus:ring-2 focus:ring-[#0d6efd]">
            </div>
            <div class="md:col-span-2">
                <label class="text-xs font-bold text-gray-400 mb-1 block uppercase">Año</label>
                <select id="filtro_anio" onchange="procesarYMostrar()" class="w-full rounded-xl border-gray-200 py-2.5 border focus:ring-[#0d6efd]">
                    <option value="todos">Todos</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <label class="text-xs font-bold text-gray-400 mb-1 block uppercase">Mes</label>
                <select id="filtro_mes" onchange="procesarYMostrar()" class="w-full rounded-xl border-gray-200 py-2.5 border focus:ring-[#0d6efd]">
                    <option value="todos">Todos los meses</option>
                    <option value="1">Enero</option><option value="2">Febrero</option><option value="3">Marzo</option>
                    <option value="4">Abril</option><option value="5">Mayo</option><option value="6">Junio</option>
                    <option value="7">Julio</option><option value="8">Agosto</option><option value="9">Septiembre</option>
                    <option value="10">Octubre</option><option value="11">Noviembre</option><option value="12">Diciembre</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <button onclick="buscarDataCliente()" class="w-full bg-[#0d6efd] text-white py-2.5 rounded-xl font-bold hover:bg-[#0b5ed7] transition-all shadow-md">BUSCAR</button>
            </div>
        </div>
        
        <div class="flex justify-center mt-6 gap-2">
            <button id="btn_dinero" onclick="cambiarUnidad('total')" class="px-6 py-2 rounded-lg bg-[#0d6efd] text-white font-bold text-xs shadow-md">VENTA ($)</button>
            <button id="btn_kg" onclick="cambiarUnidad('kg')" class="px-6 py-2 rounded-lg bg-gray-100 text-gray-500 font-bold text-xs border">VOLUMEN (Kg)</button>
        </div>
    </div>

    <div class="w-full max-w-5xl mt-4 bg-white p-6 rounded-3xl shadow-xl border border-gray-100">
        <div id="contenedor_grafica" style="width: 100%; height: 400px;" class="flex items-center justify-center text-gray-300 italic"></div>
    </div>

    <div id="seccion_tabla" class="w-full max-w-5xl mt-6 hidden bg-white p-6 rounded-3xl shadow-lg border">
        <table class="w-full text-sm">
            <thead class="border-b text-gray-400 uppercase text-[10px] font-black">
                <tr>
                    <th class="py-3 text-left">Fecha</th>
                    <th class="py-3 text-left">Producto</th>
                    <th class="py-3 text-center">Cant. (Kg)</th>
                    <th class="py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody id="body_tabla" class="divide-y text-gray-700"></tbody>
        </table>
    </div>
</section>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});

    let datosOriginales = [];
    let unidadActual = 'total';
    const nombresMeses = ["", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];

    window.buscarDataCliente = async function() {
        const query = document.getElementById('input_cliente_busqueda').value.trim();
        if (query.length < 3) return alert("Mínimo 3 letras");

        try {
            const resp = await fetch(`{{ route('api.clienteData') }}?q=${encodeURIComponent(query)}`);
            datosOriginales = await resp.json();
            
            if (datosOriginales.length > 0) {
                popularAnios();
                calcularRecords();
                document.getElementById('seccion_records').classList.remove('hidden');
            }
            procesarYMostrar();
        } catch (e) { console.error(e); }
    };

    function popularAnios() {
        const selectAnio = document.getElementById('filtro_anio');
        const aniosUnicos = [...new Set(datosOriginales.map(p => p.anio))].sort((a, b) => b - a);
        
        selectAnio.innerHTML = '<option value="todos">Todos</option>';
        aniosUnicos.forEach(a => {
            selectAnio.innerHTML += `<option value="${a}">${a}</option>`;
        });
    }

    function calcularRecords() {
        let resumenMesAnioDinero = {}, resumenMesAnioKg = {};

        datosOriginales.forEach(p => {
            const clave = `${nombresMeses[p.mes]} ${p.anio}`;
            resumenMesAnioDinero[clave] = (resumenMesAnioDinero[clave] || 0) + parseFloat(p.subtotal);
            resumenMesAnioKg[clave] = (resumenMesAnioKg[clave] || 0) + parseFloat(p.quantity);
        });

        let maxD = 0, claveD = "---", maxK = 0, claveK = "---";
        Object.keys(resumenMesAnioDinero).forEach(k => {
            if(resumenMesAnioDinero[k] > maxD) { maxD = resumenMesAnioDinero[k]; claveD = k; }
            if(resumenMesAnioKg[k] > maxK) { maxK = resumenMesAnioKg[k]; claveK = k; }
        });

        document.getElementById('record_mes_dinero').innerText = claveD;
        document.getElementById('record_monto_dinero').innerText = "Venta: $" + maxD.toLocaleString('en-US', {minimumFractionDigits:2});
        document.getElementById('record_mes_kg').innerText = claveK;
        document.getElementById('record_monto_kg').innerText = "Volumen: " + maxK.toLocaleString() + " Kg";
    }

    function cambiarUnidad(u) {
        unidadActual = u;
        document.getElementById('btn_dinero').className = (u === 'total') ? 'px-6 py-2 rounded-lg bg-[#0d6efd] text-white font-bold text-xs shadow-md' : 'px-6 py-2 rounded-lg bg-gray-100 text-gray-500 font-bold text-xs border';
        document.getElementById('btn_kg').className = (u === 'kg') ? 'px-6 py-2 rounded-lg bg-[#198754] text-white font-bold text-xs shadow-md' : 'px-6 py-2 rounded-lg bg-gray-100 text-gray-500 font-bold text-xs border';
        procesarYMostrar();
    }

    function procesarYMostrar() {
        if (!datosOriginales.length) return;
        const anioF = document.getElementById('filtro_anio').value;
        const mesF = document.getElementById('filtro_mes').value;
        const bodyTabla = document.getElementById('body_tabla');
        const agrupados = {};
        
        bodyTabla.innerHTML = '';
        let hayDatos = false;

        datosOriginales.forEach(p => {
            if (anioF !== 'todos' && p.anio.toString() !== anioF) return;
            if (mesF !== 'todos' && p.mes.toString() !== mesF) return;
            
            hayDatos = true;
            const valor = (unidadActual === 'total') ? parseFloat(p.subtotal) : parseFloat(p.quantity);
            agrupados[p.date] = (agrupados[p.date] || 0) + valor;

            bodyTabla.innerHTML += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 font-mono text-xs">${p.date}</td>
                    <td class="py-3 font-medium">${p.producto}</td>
                    <td class="py-3 text-center">${parseFloat(p.quantity).toFixed(2)}</td>
                    <td class="py-3 text-right font-bold text-gray-900">$${parseFloat(p.subtotal).toLocaleString('en-US',{minimumFractionDigits:2})}</td>
                </tr>`;
        });

        if (hayDatos) {
            document.getElementById('seccion_tabla').classList.remove('hidden');
            renderGrafica(agrupados);
        } else {
            document.getElementById('contenedor_grafica').innerHTML = "Sin registros para este periodo.";
            document.getElementById('seccion_tabla').classList.add('hidden');
        }
    }

    function renderGrafica(dataObj) {
        const data = new google.visualization.DataTable();
        data.addColumn('string', 'Fecha');
        data.addColumn('number', unidadActual === 'total' ? 'Dinero ($)' : 'Kg');
        Object.keys(dataObj).sort().forEach(f => data.addRow([f, dataObj[f]]));

        const chart = new google.visualization.ColumnChart(document.getElementById('contenedor_grafica'));
        chart.draw(data, {
            colors: [unidadActual === 'total' ? '#0d6efd' : '#198754'],
            legend: 'none',
            chartArea: {width: '90%', height: '70%'},
            vAxis: { format: unidadActual === 'total' ? '$#,###' : '#,### Kg' },
            hAxis: { slantedText: true, textStyle: {fontSize: 10} }
        });
    }
</script>