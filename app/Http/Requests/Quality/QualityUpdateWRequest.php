<?php

namespace App\Http\Requests\Quality;

use Illuminate\Foundation\Http\FormRequest;

class QualityUpdateWRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];

        if ($this->has('comentarios') && is_string($this->input('comentarios'))) {
            $sanitized['comentarios'] = strip_tags(trim($this->input('comentarios')));
        }

        if ($this->has('obs') && is_array($this->input('obs'))) {
            $obs = $this->input('obs');
            foreach ($obs as $i => $row) {
                if (is_array($row)) {
                    if (isset($row['name']) && is_string($row['name'])) {
                        $obs[$i]['name'] = strip_tags(trim($row['name']));
                    }
                }
            }
            $sanitized['obs'] = $obs;
        }

        if (!empty($sanitized)) {
            $this->merge($sanitized);
        }
    }

    public function rules(): array
    {
        return [
            'comentarios'          => ['nullable', 'string'],
            'obs'                  => ['nullable', 'array'],
            'obs.*.id'             => ['nullable', 'integer', 'exists:observaciones,id'],
            'obs.*.name'           => ['nullable', 'string', 'max:255'],
            'obs.*.fecha'          => ['nullable', 'date'],
            'obs.*.ev_corr_file'   => ['nullable', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }
}
