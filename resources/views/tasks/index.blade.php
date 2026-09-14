@extends('layouts.app')

@section('content')

<style>
    .task-card { cursor: grab; transition: all 0.2s ease; position: relative; }
    .task-card:active { cursor: grabbing; }
    .sortable-ghost { opacity: 0.4; border: 2px dashed #6366f1 !important; background: #f3f4f6 !important; }
    .dropdown-menu { display: none; position: absolute; right: 0; top: 30px; z-index: 50; width: 120px; background: white; border: 1px solid #e5e7eb; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .dropdown-menu.show { display: block; }
</style>

<section class="col-span-12 w-full flex flex-col px-4 py-2">
    @if($isAdmin)
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-8 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="md:col-span-4 border-r border-gray-100 pr-4">
            <h3 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Rendimiento</h3>
            <select id="chart-user-filter" class="w-full p-2 my-4 border-gray-300 rounded-md text-sm bg-gray-50">
                <option value="all">📊 Todo el equipo</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">👤 {{ $user->name }}</option>
                @endforeach
            </select>
            <div class="space-y-2">
                <div class="flex justify-between p-2 bg-gray-50 rounded border text-xs font-bold uppercase">
                    <span class="text-gray-500">Pendientes</span>
                    <span id="stat-pending">{{ $statusCounts['Pendientes'] }}</span>
                </div>
                <div class="flex justify-between p-2 bg-blue-50 rounded border border-blue-100 text-xs font-bold uppercase">
                    <span class="text-blue-500">En Proceso</span>
                    <span id="stat-in-progress">{{ $statusCounts['En Proceso'] }}</span>
                </div>
                <div class="flex justify-between p-2 bg-green-50 rounded border border-green-100 text-xs font-bold uppercase">
                    <span class="text-green-500">Listas</span>
                    <span id="stat-completed">{{ $statusCounts['Completadas'] }}</span>
                </div>
            </div>
        </div>
        <div class="md:col-span-8">
            <div id="piechart_status" style="width: 100%; height: 250px;"></div>
        </div>
    </div>
    @endif

    <div class="flex justify-between items-center w-full mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Tablero Kanban</h2>
        </div>
        @if(auth()->user()->isAdmin())
            <x-button-1 data-target="add-task" class="open-modal" colorBtn="green">+ Nueva Tarea</x-button-1>
        @endif
    </div>

    <div class="flex gap-2 w-full mb-6">
        <x-input class="w-full p-2" id="search-tasks" placeholder="Filtrar por título..."></x-input>
        <select id="filter-priority" class="p-2 border-gray-300 rounded-md text-sm">
            <option value="">Todas las prioridades</option>
            <option value="low">Baja</option>
            <option value="medium">Media</option>
            <option value="high">Alta</option>
            <option value="urgent">Urgente</option>
        </select>
    </div>

    <div class="flex flex-col md:flex-row gap-4 overflow-x-auto pb-6">
        @php
            $columns = [
                ['id' => 'pending', 'title' => 'Pendientes', 'bg' => 'bg-gray-100'],
                ['id' => 'in_progress', 'title' => 'En Proceso', 'bg' => 'bg-blue-50'],
                ['id' => 'completed', 'title' => 'Completadas', 'bg' => 'bg-green-50'],
            ];
        @endphp

        @foreach($columns as $column)
            <div class="flex-1 min-w-[320px] {{ $column['bg'] }} rounded-xl p-3 border border-gray-200 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-4 px-2 font-bold text-gray-700 uppercase text-xs tracking-widest">
                    <span>{{ $column['title'] }}</span>
                    <span class="bg-white px-2 py-1 rounded-full border shadow-sm">{{ $tasks->where('status', $column['id'])->count() }}</span>
                </div>

                <div class="space-y-3 flex-grow column-droppable" id="column-{{ $column['id'] }}" data-status="{{ $column['id'] }}">
                    @foreach($tasks->where('status', $column['id']) as $task)
                        <div class="task-card bg-white p-4 rounded-lg shadow-sm border-l-4 
                            @if($task->priority == 'urgent') border-red-500 
                            @elseif($task->priority == 'high') border-orange-400 
                            @elseif($task->priority == 'medium') border-blue-400 
                            @else border-gray-400 @endif"
                            data-id="{{ $task->id }}" data-priority="{{ $task->priority }}">
                            
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-sm text-gray-800 pr-6">{{ $task->title }}</h4>
                                
                                @if(auth()->user()->isAdmin())
                                    <div class="absolute top-2 right-2">
                                        <button class="btn-dots p-1 text-gray-400 hover:text-gray-600 focus:outline-none">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg>
                                        </button>
                                        <div class="dropdown-menu shadow-lg border bg-white rounded py-1">
                                            <button data-id="{{ $task->id }}" data-target="editTask" class="open-edit-modal flex items-center w-full px-4 py-2 text-xs hover:bg-indigo-50 text-indigo-600 font-bold uppercase">Editar</button>
                                            <button data-id="{{ $task->id }}" class="btn-delete flex items-center w-full px-4 py-2 text-xs hover:bg-red-50 text-red-600 font-bold uppercase">Eliminar</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <p class="text-xs text-gray-500 mb-3">{{ Str::limit($task->description, 60) }}</p>

                            <div class="flex flex-col gap-2">
                                {{-- FECHA DE ENTREGA --}}
                                @if($task->due_date)
                                    <div class="flex items-center gap-1 {{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'completed' ? 'text-red-600' : 'text-gray-400' }}">
                                        <i class="ri-calendar-line text-[11px]"></i>
                                        <span class="text-[10px] font-semibold">
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('d M, Y') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center">
                                    <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $task->priority }}</span>
                                    <div class="flex items-center gap-1">
                                        <small class="text-[10px] text-gray-400">{{ explode(' ', $task->responsable->name ?? 'N/A')[0] }}</small>
                                        <div class="w-6 h-6 rounded-full bg-indigo-500 flex items-center justify-center text-[10px] text-white font-bold">{{ strtoupper(substr($task->responsable->name ?? '?', 0, 1)) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    @include('tasks.modals.addTask') 
    @include('tasks.modals.editTask') 
</section>

@push('js')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>

<script>
$(document).ready(function(){
    $(document).on('click', '.open-modal', function() {
        $(`#${$(this).data('target')}`).removeClass('hidden');
    });

    $(document).on('click', '.close-modal', function() {
        $(this).closest('.fixed').addClass('hidden');
    });

    $(document).on('click', '.btn-dots', function(e) {
        e.stopPropagation();
        $('.dropdown-menu').not($(this).next()).removeClass('show');
        $(this).next().toggleClass('show');
    });

    $(document).on('click', function() { $('.dropdown-menu').removeClass('show'); });

    $('#search-tasks, #filter-priority').on('keyup change', function(){
        let search = $('#search-tasks').val().toLowerCase();
        let priority = $('#filter-priority').val();
        $('.task-card').each(function(){
            let match = $(this).find('h4').text().toLowerCase().includes(search) && 
                        (priority === "" || $(this).data('priority') === priority);
            $(this).toggle(match);
        });
    });

    $('.column-droppable').each(function(){
        new Sortable(this, {
            group: 'kanban', animation: 150, ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                if (evt.from !== evt.to) {
                    let id = $(evt.item).data('id');
                    let status = evt.to.getAttribute('data-status');
                    $.post(`/tasks/${id}/update-status`, { 
                        _token: '{{ csrf_token() }}', 
                        _method: 'PATCH', 
                        status: status 
                    }, () => location.reload());
                }
            }
        });
    });

    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        $('.dropdown-menu').removeClass('show');

        Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            confirmButtonText: "Sí, eliminar"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/tasks/${id}`,
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', _method: 'DELETE' },
                    success: function() {
                        location.reload();
                    }
                });
            }
        });
    });

    $(document).on('click', '.open-edit-modal', function(){
        let id = $(this).data('id');
        $('.dropdown-menu').removeClass('show');
        $.get(`/tasks/${id}/edit`, function(data){
            $('#edit_task_id').val(data.id);
            $('#edit_title').val(data.title);
            $('#edit_description').val(data.description);
            $('#edit_user_id').val(data.user_id);
            $('#edit_priority').val(data.priority);
            $('#edit_due_date').val(data.due_date);
            $('#editTask').removeClass('hidden');
        });
    });

    $(document).on('submit', '#edit-task-form', function(e) {
        e.preventDefault();
        let id = $('#edit_task_id').val();
        let formData = new FormData(this);
        $.ajax({
            type: 'POST',
            url: `/tasks/${id}`,
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                location.reload();
            }
        });
    });

    @if($isAdmin)
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(() => drawChart('all'));
        const tasksData = @json($tasks);

        function drawChart(uId) {
            let f = uId === 'all' ? tasksData : tasksData.filter(t => t.user_id == uId);
            let p = f.filter(t => t.status === 'pending').length;
            let i = f.filter(t => t.status === 'in_progress').length;
            let c = f.filter(t => t.status === 'completed').length;
            
            $('#stat-pending').text(p); $('#stat-in-progress').text(i); $('#stat-completed').text(c);
            
            let container = document.getElementById('piechart_status');
            if(container) {
                var data = google.visualization.arrayToDataTable([['E', 'C'],['P', p],['I', i],['C', c]]);
                var chart = new google.visualization.PieChart(container);
                chart.draw(data, { 
                    pieHole: 0.4, 
                    colors: ['#9ca3af', '#3b82f6', '#22c55e'], 
                    chartArea: {width:'100%', height:'80%'}, 
                    legend: 'bottom' 
                });
            }
        }
        $('#chart-user-filter').on('change', function() { drawChart($(this).val()); });
    @endif
});

if (typeof Pusher !== 'undefined') {
    var pusher = new Pusher('7916e627f69fccca6f7d', {
        cluster: 'us2',
        forceTLS: true
    });

    var channel = pusher.subscribe('user-tasks.{{ auth()->id() }}');
    channel.bind('task-assigned', function(data) {
        let safeEscape = window.escapeHtml || function(str){ return (str ?? '').toString().replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[s])); };
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: '<span class="text-indigo-600 font-bold uppercase">¡Nueva Tarea!</span>',
            html: `Te han asignado: <b>${safeEscape(data.title)}</b>`,
            showConfirmButton: false,
            timer: 6000,
            timerProgressBar: true
        });
    });
}
</script>
@endpush
@endsection