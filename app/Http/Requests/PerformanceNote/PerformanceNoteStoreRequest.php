<?php

namespace App\Http\Requests\PerformanceNote;

use Illuminate\Foundation\Http\FormRequest;

class PerformanceNoteStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && method_exists($this->user(), 'isAdmin') && $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('comments')) {
            $this->merge([
                'comments' => strip_tags(trim((string)$this->input('comments'))),
            ]);
        }
        if ($this->has('type')) {
            $this->merge([
                'type' => strip_tags(trim((string)$this->input('type'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'string', 'in:positive,improvement,neutral'],
            'comments' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'El usuario destinatario es obligatorio.',
            'user_id.exists' => 'El usuario seleccionado no existe en el sistema.',
            'type.required' => 'El tipo de retroalimentación es obligatorio.',
            'type.in' => 'El tipo debe ser positivo (positive), mejora (improvement) o neutral (neutral).',
            'comments.required' => 'Los comentarios son obligatorios.',
            'comments.string' => 'Los comentarios deben ser texto.',
        ];
    }
}
