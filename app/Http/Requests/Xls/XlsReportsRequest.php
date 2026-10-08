<?php

namespace App\Http\Requests\Xls;

use Illuminate\Foundation\Http\FormRequest;

class XlsReportsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('year') && $this->year !== null) {
            $cleaned = strip_tags(trim((string) $this->year));
            $this->merge([
                'year' => $cleaned !== '' ? (int) $cleaned : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ];
    }

    public function messages(): array
    {
        return [
            'year.integer' => 'El año especificado debe ser un número entero.',
            'year.min'     => 'El año mínimo permitido es 2000.',
            'year.max'     => 'El año máximo permitido es 2100.',
        ];
    }
}
