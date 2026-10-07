<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $merge = [];
        if ($this->has('title')) {
            $merge['title'] = strip_tags(trim((string)$this->input('title')));
        }
        if ($this->has('description')) {
            $merge['description'] = $this->filled('description') ? strip_tags(trim((string)$this->input('description'))) : null;
        }

        if (!empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'user_id' => ['required', 'exists:users,id'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'due_date' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'El título de la tarea es obligatorio.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'user_id.required' => 'Debe asignar la tarea a un usuario.',
            'user_id.exists' => 'El usuario seleccionado no existe.',
            'priority.required' => 'La prioridad de la tarea es obligatoria.',
            'priority.in' => 'La prioridad debe ser baja (low), media (medium), alta (high) o urgente (urgent).',
            'due_date.date' => 'La fecha límite debe ser una fecha válida.',
        ];
    }
}
