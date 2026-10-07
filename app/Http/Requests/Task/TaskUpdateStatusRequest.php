<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El estatus es obligatorio.',
            'status.in' => 'El estatus debe ser pending, in_progress o completed.',
        ];
    }
}
