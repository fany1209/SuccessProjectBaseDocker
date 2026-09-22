<?php

namespace App\Http\Requests\ClimaLaboral;

use Illuminate\Foundation\Http\FormRequest;

class StoreClimaLaboralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('q12_sugerencias') && $this->q12_sugerencias !== null) {
            $this->merge([
                'q12_sugerencias' => strip_tags(trim($this->q12_sugerencias)),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'q1_ambiente'                => 'required|integer|min:1|max:5',
            'q2_respeto'                 => 'required|integer|min:1|max:5',
            'q3_comunicacion_oportuna'   => 'required|integer|min:1|max:5',
            'q4_comunicacion_escucha'    => 'required|integer|min:1|max:5',
            'q5_liderazgo'               => 'required|integer|min:1|max:5',
            'q6_reconocimiento'          => 'required|integer|min:1|max:5',
            'q7_desarrollo'              => 'required|integer|min:1|max:5',
            'q8_motivacion'              => 'required|integer|min:1|max:5',
            'q9_satisfaccion'            => 'required|integer|min:1|max:5',
            'q10_bienestar_carga'        => 'required|integer|min:1|max:5',
            'q11_bienestar_preocupacion' => 'required|integer|min:1|max:5',
            'q12_sugerencias'            => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'q1_ambiente.required'                => 'La respuesta para Ambiente de trabajo es obligatoria.',
            'q1_ambiente.integer'                 => 'La calificación debe ser un número entero entre 1 y 5.',
            'q1_ambiente.min'                     => 'La calificación mínima es 1.',
            'q1_ambiente.max'                     => 'La calificación máxima es 5.',
            'q2_respeto.required'                 => 'La respuesta para Respeto es obligatoria.',
            'q3_comunicacion_oportuna.required'   => 'La respuesta para Comunicación oportuna es obligatoria.',
            'q4_comunicacion_escucha.required'    => 'La respuesta para Escucha es obligatoria.',
            'q5_liderazgo.required'               => 'La respuesta para Liderazgo es obligatoria.',
            'q6_reconocimiento.required'          => 'La respuesta para Reconocimiento es obligatoria.',
            'q7_desarrollo.required'              => 'La respuesta para Desarrollo es obligatoria.',
            'q8_motivacion.required'              => 'La respuesta para Motivación es obligatoria.',
            'q9_satisfaccion.required'            => 'La respuesta para Satisfacción es obligatoria.',
            'q10_bienestar_carga.required'        => 'La respuesta para Carga de trabajo es obligatoria.',
            'q11_bienestar_preocupacion.required' => 'La respuesta para Preocupación por el personal es obligatoria.',
            'q12_sugerencias.max'                 => 'Las sugerencias no pueden exceder los 1000 caracteres.',
        ];
    }
}
