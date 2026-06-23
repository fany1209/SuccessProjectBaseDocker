<section class="col-span-12 w-full flex flex-col items-center px-1">
  <div class="flex flex-col justify-center items-center w-full">
    <div class="mt-6 text-center w-full">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        Histórico de Registros de Asistencia
      </h1>
      <p class="text-sm text-gray-600 mt-1">Consulta y búsqueda rápida de checadas del personal.</p>
      <div class="mt-2 h-px bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent"></div>
    </div>

    <div class="w-full overflow-x-auto mt-4 bg-white p-4 rounded-lg shadow-sm">
      <table id="attendance-table" class="display w-full divide-y divide-gray-200 table-fixed text-md text-left">
        <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-bold tracking-wider">
          <tr>
            <th class="px-4 py-3 text-left w-1/4">Trabajador</th>
            <th class="px-4 py-3 text-left">Fecha</th>
            <th class="px-4 py-3 text-left">Entrada</th>
            <th class="px-4 py-3 text-left">Salida Comida</th>
            <th class="px-4 py-3 text-left">Regreso Comida</th>
            <th class="px-4 py-3 text-left">Salida Final</th>
            <th class="px-4 py-3 text-center">Estatus</th>
            <th class="px-4 py-3 text-left w-1/4">Comentarios</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white text-gray-800 text-sm whitespace-normal break-words"></tbody>
      </table>
    </div>
  </div>

<script>
function debugAttendanceDataSrc(json){
  if (Array.isArray(json)) return json;
  if (Array.isArray(json?.data)) return json.data;
  return [];
}

document.addEventListener('DOMContentLoaded', function () {
  const urlParams = new URLSearchParams(window.location.search);
  const empleado = urlParams.get('empleado') || '';
  const fechaInicio = urlParams.get('fecha_inicio') || '';
  const fechaFin = urlParams.get('fecha_fin') || '';

  const table = $('#attendance-table').DataTable({
    ajax: {
      url: "{{ route('attendance.index') }}", 
      data: {
        empleado: empleado,
        fecha_inicio: fechaInicio,
        fecha_fin: fechaFin
      },
      dataSrc: debugAttendanceDataSrc,
      headers: { 'Accept': 'application/json' }
    },
    columns: [
      { data: 'nombre', className: 'font-medium text-gray-900' },
      { 
        data: 'fecha',
        render: function(data) {
          if(!data) return '—';
          const partes = data.split('-');
          return partes.length === 3 ? `${partes[2]}/${partes[1]}/${partes[0]}` : data;
        }
      },
      { data: 'entrada', render: (d) => d ? `<span class="text-dark">${d}</span>` : '<span class="text-gray-400">00:00:00</span>' },
      { data: 'salida_comida', render: (d) => d || '<span class="text-gray-400">—</span>' },
      { data: 'regreso_comida', render: (d) => d || '<span class="text-gray-400">—</span>' },
      { data: 'salida_final', render: (d) => d ? `<span class="text-dark">${d}</span>` : '<span class="text-gray-400">00:00:00</span>' },
      { 
        data: 'tipo',
        className: 'text-center',
        render: function(v) {
          let estatus = v ? v.trim().toLowerCase() : 'normal';
          let badges = {
            'normal': 'bg-gray-100 text-gray-800',
            'falta': 'bg-red-100 text-red-800 font-bold',
            'viaje': 'bg-blue-100 text-blue-800 font-bold',
            'curso': 'bg-purple-100 text-purple-800 font-bold',
            'permiso': 'bg-yellow-100 text-yellow-800 font-bold',
            'otro': 'bg-orange-100 text-orange-800 font-bold'
          };
          
          let claseClave = badges[estatus] || 'bg-gray-100 text-gray-800';
          let textoMostrar = v ? v : 'Normal';
          
          return `<span class="px-2.5 py-1 rounded-full text-xs font-semibold uppercase tracking-wide ${claseClave}">${textoMostrar}</span>`;
        }
      },
      { data: 'comentario', render: (d) => d || '<span class="text-gray-400 italic">Sin comentarios</span>' }
    ],
    lengthChange: false,
    pageLength: 10,
    order: [[1, 'asc']], 
    language: {
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
      infoEmpty: "No hay registros disponibles",
      zeroRecords: "No se encontraron resultados coincidentes",
      search: "Buscar:",
      paginate: { next: "Siguiente", previous: "Anterior" }
    }
  });
});
</script>
</section>