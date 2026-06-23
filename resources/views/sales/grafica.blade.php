<section class="col-span-12 w-full flex flex-col items-center px-1">
    
    <div class="mt-6 w-full max-w-6xl">
        <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
            Rendimiento de Vendedores
        </h1>
        <p class="text-sm text-gray-600">Filtre por periodo y haga clic en la gráfica para ver folios y totales.</p>
        <div class="mt-2 h-px bg-gradient-to-r from-[#198754]/50 via-[#198754]/20 to-transparent"></div>
    </div>

    <div class="w-full max-w-5xl mt-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-wrap items-end justify-center gap-4">
        <div class="flex flex-col">
            <label class="text-xs font-bold text-gray-500 mb-1 ml-1">FECHA INICIO</label>
            <input type="date" id="fecha_inicio" class="rounded-lg border-gray-200 text-sm focus:ring-[#198754] focus:border-[#198754]">
        </div>
        <div class="flex flex-col">
            <label class="text-xs font-bold text-gray-500 mb-1 ml-1">FECHA FIN</label>
            <input type="date" id="fecha_fin" class="rounded-lg border-gray-200 text-sm focus:ring-[#198754] focus:border-[#198754]">
        </div>
        <button onclick="initDonutProcess()" class="bg-[#198754] hover:bg-[#157347] text-white px-6 py-2 rounded-lg font-medium transition-colors text-sm shadow-sm">
            Filtrar y Actualizar
        </button>
    </div>

    <div class="w-full max-w-5xl mt-4 bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
        <div id="donut_chart_final" style="width: 100%; height: 500px;">
            <div class="flex items-center justify-center h-full text-gray-400 italic">
                Cargando datos financieros...
            </div>
        </div>

        <div id="detalle_ventas_seccion" class="mt-8 hidden border-t border-gray-100 pt-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h2 id="nombre_vendedor_tit" class="text-xl font-bold text-[#198754]">Desglose de Ventas</h2>
                <div class="bg-[#198754] text-white px-6 py-3 rounded-2xl shadow-lg flex items-center gap-3">
                    <span class="text-xs uppercase opacity-80 font-bold">Total en Dinero:</span>
                    <span id="monto_total_dinero" class="text-xl font-black">$0.00</span>
                </div>
            </div>
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Folio</th>
                            <th class="px-4 py-3 text-left font-bold">Fecha</th>
                            <th class="px-4 py-3 text-center font-bold">Estatus</th>
                            <th class="px-4 py-3 text-left font-bold">Producto</th>
                            <th class="px-4 py-3 text-center font-bold">Cant.</th>
                            <th class="px-4 py-3 text-center font-bold">Costo U.</th>
                            <th class="px-4 py-3 text-right font-bold">Subtotal con IVA</th>
                        </tr>
                    </thead>
                    <tbody id="tabla_detalles_body" class="divide-y divide-gray-100 bg-white">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});
    google.charts.setOnLoadCallback(initDonutProcess);

    let rawData = null;
    let dataFiltradaActual = {}; 

    async function initDonutProcess() {
        try {
            const response = await fetch("{{ route('sales.chartData') }}?v=" + Date.now());
            const res = await response.json();
            
            if (res.error) {
                console.error("Error:", res.error);
                return;
            }

            rawData = res.datos;
            
            const fInicio = document.getElementById('fecha_inicio').value;
            const fFin = document.getElementById('fecha_fin').value;
            let totalesVendedores = {};
            dataFiltradaActual = {}; 
            let huboDatos = false;

            Object.keys(rawData).forEach(fecha => {
                if ((!fInicio || fecha >= fInicio) && (!fFin || fecha <= fFin)) {
                    huboDatos = true;
                    dataFiltradaActual[fecha] = rawData[fecha];
                    rawData[fecha].forEach(v => {
                        let nombre = v.seller;
                        totalesVendedores[nombre] = (totalesVendedores[nombre] || 0) + 1;
                    });
                }
            });

            if (huboDatos) {
                renderDonut(totalesVendedores);
            } else {
                document.getElementById('donut_chart_final').innerHTML = '<div class="flex items-center justify-center h-full text-gray-500 italic">No hay registros en este rango.</div>';
                document.getElementById('detalle_ventas_seccion').classList.add('hidden');
            }
        } catch (error) {
            console.error("Error en la carga:", error);
        }
    }

    function renderDonut(totales) {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Vendedor');
        data.addColumn('number', 'Registros');

        Object.keys(totales).forEach(v => data.addRow([v, totales[v]]));

        var options = {
            title: 'PARTICIPACIÓN POR NÚMERO DE VENTAS',
            pieHole: 0.5,
            height: 500,
            chartArea: { width: '90%', height: '80%' },
            legend: { position: 'right', alignment: 'center', textStyle: { fontSize: 13 } },
            colors: ['#198754', '#20c997', '#0dcaf0', '#ffc107', '#fd7e14', '#6610f2', '#dc3545'],
            titleTextStyle: { fontSize: 18, color: '#198754', bold: true },
            backgroundColor: 'transparent'
        };

        const container = document.getElementById('donut_chart_final');
        var chart = new google.visualization.PieChart(container);

        google.visualization.events.addListener(chart, 'select', function() {
            var selected = chart.getSelection()[0];
            if (selected) {
                var vendedorNombre = data.getValue(selected.row, 0);
                mostrarDetallesFinancieros(vendedorNombre);
            }
        });

        chart.draw(data, options);
    }

    function mostrarDetallesFinancieros(nombre) {
        const body = document.getElementById('tabla_detalles_body');
        const montoGlobal = document.getElementById('monto_total_dinero');
        
        document.getElementById('detalle_ventas_seccion').classList.remove('hidden');
        body.innerHTML = '';
        let sumaTotal = 0;

        Object.values(dataFiltradaActual).forEach(dia => {
            dia.forEach(v => {
                if (v.seller === nombre) {
                    
                    sumaTotal += parseFloat(v.total_money);
                    
                    const statusName = (v.status_name || '').toLowerCase().trim();
                    let esCancelado = false;
                    let rowClass = 'hover:bg-gray-50';
                    let statusBadge = '';
                    let folioColor = 'text-gray-700';

                    if (statusName === 'cancelado' || statusName === 'cancelada') {
                        esCancelado = true;
                        rowClass = 'bg-red-50 text-red-500 italic';
                        folioColor = 'text-red-400 font-bold';
                        statusBadge = '<span class="px-2 py-1 rounded-md bg-red-100 text-red-600 font-bold text-xs uppercase">Cancelada</span>';
                    } 
                    else if (statusName === 'en proceso') {
                        rowClass = 'bg-yellow-50 hover:bg-yellow-100';
                        folioColor = 'text-yellow-600 font-bold';
                        statusBadge = '<span class="px-2 py-1 rounded-md bg-yellow-100 text-yellow-700 font-bold text-xs uppercase">En Proceso</span>';
                    } 
                    else if (statusName === 'finalizado' || statusName === 'finalizada') {
                        rowClass = 'bg-green-50 hover:bg-green-100';
                        folioColor = 'text-green-600 font-bold';
                        statusBadge = '<span class="px-2 py-1 rounded-md bg-green-100 text-green-700 font-bold text-xs uppercase">Finalizado</span>';
                    } 
                    else {
                        folioColor = 'text-blue-600 font-bold';
                        statusBadge = `<span class="px-2 py-1 rounded-md bg-gray-100 text-gray-600 font-bold text-xs uppercase">${v.status_name || 'N/A'}</span>`;
                    }

                    body.innerHTML += `
                        <tr class="${rowClass} transition-colors">
                            <td class="px-4 py-3 ${folioColor}">${v.folio || 'N/A'}</td>
                            <td class="px-4 py-3 text-gray-600">${v.date}</td>
                            <td class="px-4 py-3 text-center">${statusBadge}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">${v.public_product_name || 'N/A'}</td>
                            <td class="px-4 py-3 text-center text-gray-700">${v.quantity}</td>
                            <td class="px-4 py-3 text-center text-gray-700">$${parseFloat(v.cost || 0).toFixed(2)}</td>
                            <td class="px-4 py-3 text-right font-black">
                                ${esCancelado ? '<span class="text-red-400">$0.00</span>' : '<span class="text-[#198754]">$' + v.total_money.toLocaleString('en-US', {minimumFractionDigits: 2}) + '</span>'}
                            </td>
                        </tr>
                    `;
                }
            });
        });

        montoGlobal.innerText = "$" + sumaTotal.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('detalle_ventas_seccion').scrollIntoView({ behavior: 'smooth' });
    }

    window.addEventListener('resize', initDonutProcess);
</script>