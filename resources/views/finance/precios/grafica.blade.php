<div class="w-full mt-10 bg-white p-6 rounded-xl shadow-lg border border-gray-100">
    
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-xl font-bold text-gray-700 flex items-center gap-2">
            <i class="ri-line-chart-line text-green-600"></i> Análisis de Tendencias y Costos
        </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 items-end">
        
        <div class="flex flex-col">
            <label class="text-xs font-bold text-gray-500 uppercase mb-1">Desde:</label>
            <input type="date" id="fecha-inicio" 
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-0 text-sm py-2 px-3 bg-gray-50 outline-none transition-all">
        </div>

        <div class="flex flex-col">
            <label class="text-xs font-bold text-gray-500 uppercase mb-1">Hasta:</label>
            <input type="date" id="fecha-fin" 
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-green-500 focus:ring-0 text-sm py-2 px-3 bg-gray-50 outline-none transition-all">
        </div>

        <div class="md:col-span-2 relative">
            <label class="text-xs font-bold text-gray-500 uppercase mb-1">Buscar Producto / Insumo:</label>
            <div class="relative">
                <input type="text" id="busqueda-insumo" autocomplete="off"
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm transition-all shadow-sm"
                    placeholder="Escribe el nombre del producto...">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <i class="ri-search-line"></i>
                </div>
            </div>
            
            <div id="lista-sugerencias" class="absolute z-50 w-full bg-white mt-1 border border-gray-200 rounded-lg shadow-xl hidden max-h-60 overflow-y-auto">
                @foreach($insumos as $insumo)
                    <div class="opcion-insumo px-4 py-2 hover:bg-green-50 cursor-pointer border-b border-gray-50 last:border-0 text-sm text-gray-700" data-value="{{ $insumo }}">
                        {{ $insumo }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="relative w-full bg-gray-50 rounded-xl border-2 border-dashed border-gray-200" style="min-height: 450px;">
        <div id="loading-chart" class="hidden absolute inset-0 flex flex-col justify-center items-center bg-white bg-opacity-80 z-10 rounded-xl">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600 mb-2"></div>
            <p class="text-sm font-medium text-green-700">Obteniendo historial...</p>
        </div>

        <div id="chart_div" class="w-full h-[450px]">
            <div class="flex flex-col items-center justify-center h-full text-gray-400 italic">
                <i class="ri-cursor-line text-5xl mb-3 opacity-20"></i>
                <p>Usa el buscador para visualizar el histórico de precios</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    <div class="bg-green-50 p-4 rounded-lg border border-green-200 text-center">
        <p class="text-xs text-green-600 font-bold uppercase">Proveedor Recomendado</p>
        <h4 id="resumen-proveedor" class="text-lg font-bold text-green-800">-</h4>
    </div>
    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 text-center">
        <p class="text-xs text-blue-600 font-bold uppercase">Mejor Precio</p>
        <h4 id="resumen-precio" class="text-lg font-bold text-blue-800">$0.00</h4>
    </div>
</div>

@push('js')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">
    google.charts.load('current', {'packages':['corechart']});

    $(function() {
        const inputBusqueda = $('#busqueda-insumo');
        const lista = $('#lista-sugerencias');
        const fechaInicio = $('#fecha-inicio');
        const fechaFin = $('#fecha-fin');

        inputBusqueda.on('keyup', function() {
            const valor = $(this).val().toLowerCase();
            if (valor.length > 0) {
                lista.removeClass('hidden');
                $('.opcion-insumo').each(function() {
                    const texto = $(this).text().toLowerCase();
                    $(this).toggle(texto.indexOf(valor) > -1);
                });
            } else {
                lista.addClass('hidden');
            }
        });

        $(document).on('click', '.opcion-insumo', function() {
            const seleccionado = $(this).data('value');
            inputBusqueda.val(seleccionado);
            lista.addClass('hidden');
            cargarDatosGrafica(seleccionado);
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.relative').length) lista.addClass('hidden');
        });

        $('#fecha-inicio, #fecha-fin').on('change', function() {
            const insumo = inputBusqueda.val();
            if (insumo) cargarDatosGrafica(insumo);
        });

        
        function cargarDatosGrafica(insumo) {
            if (!insumo) return;

            $('#loading-chart').removeClass('hidden');

            $.ajax({
                url: "{{ route('precios.grafica') }}", 
                method: 'GET',
                data: { 
                    get_data_grafica: true, 
                    insumo: insumo,
                    fecha_inicio: fechaInicio.val(),
                    fecha_fin: fechaFin.val()
                },
                success: function(response) {
                    if(response && response.data && response.data.length > 0) {
                        
                        drawChart(insumo, response.data);

                        if(response.analisis) {
                            const an = response.analisis;
                            
                            $('#resumen-proveedor').text(an.mejor_proveedor);
                            $('#resumen-precio').text(`$${an.mejor_precio} ${an.moneda}`);
                            
                            const tendenciaEl = $('#resumen-tendencia');
                            const cardTendencia = $('#status-tendencia');
                            
                            tendenciaEl.text(an.tendencia);

                            cardTendencia.removeClass('bg-green-50 border-green-200 text-green-800 bg-red-50 border-red-200 text-red-800 bg-gray-50 border-gray-200 text-gray-800');
                            
                            if(an.tendencia === 'Alza') {
                                cardTendencia.addClass('bg-red-50 border-red-200 text-red-800');
                            } else if(an.tendencia === 'Baja') {
                                cardTendencia.addClass('bg-green-50 border-green-200 text-green-800');
                            } else {
                                cardTendencia.addClass('bg-gray-50 border-gray-200 text-gray-800');
                            }
                        }

                    } else {
                        $('#chart_div').html(`
                            <div class="flex flex-col items-center justify-center h-full text-orange-500 text-center p-4">
                                <i class="ri-error-warning-line text-4xl mb-2"></i>
                                <p>No hay datos suficientes para este producto <br> o rango de fechas.</p>
                            </div>
                        `);
                        
                        $('#resumen-proveedor').text('-');
                        $('#resumen-precio').text('$0.00');
                        $('#resumen-tendencia').text('-');
                        $('#status-tendencia').removeClass().addClass('p-4 rounded-lg border text-center bg-white border-gray-100');
                    }
                },
                error: function(xhr) {
                    console.error("Error detallado:", xhr.responseText);
                    $('#chart_div').html('<div class="flex items-center justify-center h-full text-red-500 font-bold">Error 500: Revisa los logs del servidor</div>');
                },
                complete: function() {
                    $('#loading-chart').addClass('hidden');
                }
            });
        }

        function drawChart(nombreInsumo, rawData) {
            const dataTable = new google.visualization.DataTable();
            dataTable.addColumn('string', 'Fecha');
            dataTable.addColumn('number', 'Precio');
            dataTable.addColumn({type: 'string', role: 'tooltip', p: {html: true}});
            
            rawData.forEach(item => {
                const tooltipContent = `
                    <div style="padding:10px; min-width:150px;">
                        <div style="font-weight:bold; border-bottom:1px solid #ccc; margin-bottom:5px; padding-bottom:3px;">${item.fecha}</div>
                        <strong>Proveedor:</strong> <span style="color:#198754;">${item.proveedor}</span><br>
                        <strong>Precio:</strong> <span style="font-size:1.1em;">$${item.precio.toFixed(2)} ${item.moneda}</span>
                    </div>`;
                
                dataTable.addRow([item.fecha, parseFloat(item.precio), tooltipContent]);
            });

            const options = {
                title: 'EVOLUCIÓN DE PRECIOS: ' + nombreInsumo.toUpperCase(),
                titleTextStyle: { color: '#374151', fontSize: 16, bold: true },
                curveType: 'function', 
                legend: { position: 'bottom' },
                colors: ['#10b981'],
                hAxis: { title: 'Línea de Tiempo', textStyle: {fontSize: 11} },
                vAxis: { title: 'Costo Unitario ($)', format: 'currency', gridlines: {color: '#f3f4f6'} },
                pointSize: 10,
                tooltip: { isHtml: true },
                animation: { startup: true, duration: 1000, easing: 'out' },
                chartArea: { width: '85%', height: '70%' },
                backgroundColor: 'transparent'
            };

            const chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(dataTable, options);
        }

        $(window).resize(function(){
            const insumo = inputBusqueda.val();
            if(insumo) drawChart(insumo, []);
        });
    });
</script>
@endpush