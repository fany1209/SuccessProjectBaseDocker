<x-modal id="add-task">
    <form class="flex flex-col items-center w-full gap-2" id="create-task-form">
        @csrf
        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Asignar Nueva Tarea</x-tittle-form>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="title">Título de la Tarea</x-label>
                <x-input-1 required name="title" id="title" placeholder="Ej. Revisar inventario de CEDIS"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="description">Descripción (Opcional)</x-label>
                <textarea name="description" id="description" rows="3" 
                    class="w-full border-gray-300 focus:border-green-500 focus:ring-green-500 rounded-md shadow-sm p-2 text-sm" 
                    placeholder="Detalles adicionales sobre la tarea..."></textarea>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="user_id">Asignar a</x-label>
                <x-select-1 name="user_id" id="user_id" required>
                    <option value="">Seleccionar responsable</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="priority">Prioridad</x-label>
                <x-select-1 name="priority" id="priority">
                    <option value="low">Baja</option>
                    <option value="medium" selected>Media</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="due_date">Fecha Límite</x-label>
                <x-input-1 type="date" name="due_date" id="due_date"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 class="close-modal" type="reset" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" id="save-task" colorBtn="green">Guardar Tarea</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>

@push('js')
<script>
$(function(){
    const taskModal = $('#addTask');
    
   function addTask(){
    $("#create-task-form").on("submit", function(event) {
        event.preventDefault();
        
        var form = $(this);
        var data = new FormData(form[0]);
        var btn = $('#save-task');
        
        btn.prop('disabled', true).text('Guardando...');
        
        $.ajax({
            type: 'POST',
            url: "{{ route('tasks.store') }}",
            data: data,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'La tarea ha sido asignada correctamente.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    location.reload();
                });
            },
            error: function(xhr){
                console.log(xhr.responseText); 
                let message = 'Ocurrió un error inesperado.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(xhr.responseJSON.errors)[0][0];
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK'
                });
                btn.prop('disabled', false).text('Guardar Tarea');
            }
        });
    });
}

    addTask();
});
</script>
@endpush