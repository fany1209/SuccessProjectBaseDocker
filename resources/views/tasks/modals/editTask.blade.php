<x-modal id="editTask">
    <form class="flex flex-col items-center w-full gap-2" id="edit-task-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="task_id" id="edit_task_id">

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-tittle-form>Editar Tarea Seleccionada</x-tittle-form>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_title">Título de la Tarea</x-label>
                <x-input-1 required name="title" id="edit_title" placeholder="Ej. Revisar inventario"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_description">Descripción (Opcional)</x-label>
                <textarea name="description" id="edit_description" rows="3" 
                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm p-2 text-sm" 
                    placeholder="Detalles adicionales..."></textarea>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_user_id">Asignar a</x-label>
                <x-select-1 name="user_id" id="edit_user_id" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </x-select-1>
            </x-wrapper-form-2>
            <x-wrapper-form-2>
                <x-label for="edit_priority">Prioridad</x-label>
                <x-select-1 name="priority" id="edit_priority">
                    <option value="low">Baja</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                </x-select-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1>
            <x-wrapper-form-2>
                <x-label for="edit_due_date">Fecha Límite</x-label>
                <x-input-1 type="date" name="due_date" id="edit_due_date"></x-input-1>
            </x-wrapper-form-2>
        </x-wrapper-form-1>

        <x-wrapper-form-1 class="mt-4">
            <x-button-1 class="close-modal" type="button" colorBtn="red">Cancelar</x-button-1>
            <x-button-1 type="submit" id="save-edit-task" colorBtn="blue">Guardar Cambios</x-button-1>
        </x-wrapper-form-1>
    </form>
</x-modal>