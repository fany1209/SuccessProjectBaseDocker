@extends('layouts.app')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 pb-12">
  <section class="col-span-12 w-full flex flex-col items-center px-1">
    
    <!-- Encabezado -->
    <div class="mt-6 text-center w-full relative">
      <a href="{{ route('sistemas-ti.index') }}" class="absolute left-0 top-0 text-[#198754] hover:text-[#157347] transition flex items-center gap-1 font-semibold">
        <i class="fas fa-chevron-left text-xl"></i> Volver
      </a>

      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#198754]">
        <i class="fas fa-box-open mr-1"></i> Inventario de Equipos de TI
      </h1>
      <p class="text-sm text-gray-600 mt-1">Gestión y control general de activos informáticos</p>
      <div class="mt-2 h-px mx-auto bg-gradient-to-r from-transparent via-[#198754]/50 to-transparent max-w-sm"></div>
    </div>

    @if(session('success'))
    <div class="w-full mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
            <span class="block sm:inline font-medium"><i class="fas fa-check-circle mr-1"></i> {{ session('success') }}</span>
        </div>
    </div>
    @endif

    <!-- Filtros de búsqueda -->
    <div class="mb-2 mt-6 flex justify-between items-end w-full flex-wrap gap-4">
      <div>
        <button type="button" class="bg-[#198754] text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-[#157347] transition flex items-center gap-1" onclick="openModal('modalRegistro')">
            <i class="fas fa-plus"></i> Agregar Registro
        </button>
      </div>
      <div class="bg-white/80 backdrop-blur-md border border-gray-200 rounded-xl p-3 shadow-sm flex items-center flex-wrap gap-3">
        <form action="{{ route('sistemas-ti.inventario') }}" method="GET" class="flex items-center flex-wrap gap-3 m-0">
          <div class="flex items-center gap-2">
            <input type="text" name="search" value="{{ request('search') }}" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]" placeholder="Buscar por serie o modelo...">
          </div>
          <div class="flex items-center gap-2">
            <select name="department" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
                <option value="">Departamentos</option>
                <option value="Gerencia" {{ request('department') == 'Gerencia' ? 'selected' : '' }}>Gerencia</option>
                <option value="Sistemas" {{ request('department') == 'Sistemas' ? 'selected' : '' }}>Sistemas</option>
                <option value="I+D" {{ request('department') == 'I+D' ? 'selected' : '' }}>I+D</option>
                <option value="Calidad" {{ request('department') == 'Calidad' ? 'selected' : '' }}>Calidad</option>
                <option value="Finanzas" {{ request('department') == 'Finanzas' ? 'selected' : '' }}>Finanzas</option>
                <option value="Oficina" {{ request('department') == 'Oficina' ? 'selected' : '' }}>Oficina</option>
                <option value="Laboratorio" {{ request('department') == 'Laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                <option value="RH" {{ request('department') == 'RH' ? 'selected' : '' }}>RH</option>
                <option value="Almacen" {{ request('department') == 'Almacen' ? 'selected' : '' }}>Almacen</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <select name="article" class="text-sm border-gray-300 rounded-lg focus:ring-[#198754] focus:border-[#198754]">
                <option value="">Artículos</option>
                <option value="Laptop" {{ request('article') == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                <option value="PC" {{ request('article') == 'PC' ? 'selected' : '' }}>PC</option>
                <option value="Monitor" {{ request('article') == 'Monitor' ? 'selected' : '' }}>Monitor</option>
                <option value="Monitor Curvo" {{ request('article') == 'Monitor Curvo' ? 'selected' : '' }}>Monitor Curvo</option>
            </select>
          </div>
          <button type="submit" class="bg-[#217346] text-white px-4 py-1.5 rounded-lg text-sm font-medium hover:bg-[#1e6b40] transition flex items-center gap-1">
            <i class="fas fa-filter"></i> Filtrar
          </button>
        </form>
      </div>
    </div>

    <!-- Tabla de Inventario -->
    <div class="w-full mt-4">
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto w-full p-4">
            <table class="display w-full divide-y divide-gray-200 text-sm text-left">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold text-xs tracking-wider">
                    <tr>
                        <th class="px-2 py-2">Imagen</th>
                        <th class="px-2 py-2">Departamento</th>
                        <th class="px-2 py-2">Responsable</th>
                        <th class="px-2 py-2">Artículo</th>
                        <th class="px-2 py-2">Marca / Modelo</th>
                        <th class="px-2 py-2">No. Serie</th>
                        <th class="px-2 py-2">Código Success</th>
                        <th class="px-2 py-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    @forelse($equipments as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-2 py-2">
                            @if($item->image_url)
                                <img src="{{ $item->image_url }}" alt="Img" class="w-10 h-10 object-cover rounded shadow-sm">
                            @else
                                <div class="w-10 h-10 bg-gray-100 text-gray-400 rounded flex items-center justify-center">
                                    <i class="fas fa-laptop text-lg"></i>
                                </div>
                            @endif
                        </td>
                        <td class="px-2 py-2">
                            <span class="font-bold text-gray-700">{{ $item->department ?: 'N/A' }}</span>
                        </td>
                        <td class="px-2 py-2 font-semibold text-blue-800">{{ $item->responsible ?: 'N/A' }}</td>
                        <td class="px-2 py-2">
                            <span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium">{{ $item->article }}</span>
                        </td>
                        <td class="px-2 py-2">
                            <div class="text-sm font-semibold text-gray-800">{{ $item->brand ?: 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $item->model ?: 'N/A' }}</div>
                        </td>
                        <td class="px-2 py-2">
                            <span class="px-2 py-1 bg-blue-50 text-blue-800 rounded text-xs font-semibold">{{ $item->serial_number ?: 'N/A' }}</span>
                        </td>
                        <td class="px-2 py-2 text-gray-600 font-medium">{{ $item->success_code ?: '—' }}</td>
                        <td class="px-2 py-2">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" class="p-1.5 rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition" title="Editar Detalles" onclick="openEditModal({{ $item }})">
                                    <i class="fas fa-edit text-lg"></i>
                                </button>
                                <form action="{{ route('sistemas-ti.inventario.destroy', $item->id) }}" method="POST" class="inline-block m-0" onsubmit="return confirm('¿Estás seguro de eliminar este equipo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded bg-red-100 text-red-600 hover:bg-red-200 transition" title="Eliminar Registro">
                                        <i class="fas fa-trash-alt text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 font-medium">No se encontraron registros en el inventario.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal Nuevo/Editar Registro -->
<div id="modalRegistro" class="fixed inset-0 bg-black bg-opacity-50 z-[100] hidden flex items-center justify-center overflow-y-auto pt-10 pb-10">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl mx-4 my-auto relative max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 sticky top-0 bg-white z-10">
            <h5 class="text-lg font-bold text-[#198754]" id="modalTitle">
                <i class="fas fa-plus-circle mr-1"></i> Registrar Nuevo Equipo
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none" onclick="closeModal('modalRegistro')">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="equipoForm" method="POST" action="{{ route('sistemas-ti.inventario.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                        <input type="text" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50" name="department" id="inputDepartment" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                        <input type="text" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50" name="responsible" id="inputResponsible">
                    </div>
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Artículo</label>
                            <input type="text" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50" name="article" id="inputArticle" placeholder="Ej. Laptop, PC, Monitor" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                            <input type="text" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50" name="brand" id="inputBrand">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modelo</label>
                            <input type="text" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50" name="model" id="inputModel">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de Serie</label>
                        <input type="text" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50" name="serial_number" id="inputSerialNumber">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código Success</label>
                        <input type="text" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50" name="success_code" id="inputSuccessCode">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL de la Imagen</label>
                        <input type="url" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-[#198754] focus:ring focus:ring-[#198754]/50" name="image_url" id="inputImageUrl" placeholder="https://ejemplo.com/imagen.jpg">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50 sticky bottom-0 z-10 rounded-b-xl">
                <button type="button" class="px-4 py-2 border border-gray-300 bg-white text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-100 transition" onclick="closeModal('modalRegistro')">Cancelar</button>
                <button type="submit" class="bg-[#198754] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#157347] transition flex items-center gap-1" id="btnSubmit">
                    Guardar Registro
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-plus-circle mr-1"></i> Registrar Nuevo Equipo';
        document.getElementById('equipoForm').action = "{{ route('sistemas-ti.inventario.store') }}";
        document.getElementById('formMethod').value = "POST";
        document.getElementById('btnSubmit').innerHTML = 'Guardar Registro';
        document.getElementById('equipoForm').reset();
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openEditModal(item) {
        document.getElementById('modalRegistro').classList.remove('hidden');
        document.getElementById('modalTitle').innerHTML = '<i class="fas fa-edit mr-1"></i> Editar Equipo';
        document.getElementById('equipoForm').action = "/sistemas-ti/inventario/" + item.id;
        document.getElementById('formMethod').value = "PUT";
        document.getElementById('btnSubmit').innerHTML = 'Actualizar Registro';

        document.getElementById('inputDepartment').value = item.department || '';
        document.getElementById('inputResponsible').value = item.responsible || '';
        document.getElementById('inputArticle').value = item.article || '';
        document.getElementById('inputBrand').value = item.brand || '';
        document.getElementById('inputModel').value = item.model || '';
        document.getElementById('inputSerialNumber').value = item.serial_number || '';
        document.getElementById('inputSuccessCode').value = item.success_code || '';
        document.getElementById('inputImageUrl').value = item.image_url || '';
    }
</script>
@endsection
