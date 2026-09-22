<?php

namespace App\Http\Requests\Complaint;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreComplaintRequest extends FormRequest
{
    public const ALLOWED_MOTIVOS = [
        'trato_personal',
        'tiempos_respuesta',
        'condiciones_trabajo',
        'procesos_internos',
        'comunicacion_interna',
        'instalaciones_equipo',
        'seguridad_higiene',
        'cumplimiento_politicas',
        'liderazgo_supervision',
        'falta_apoyo_recursos',
        'discriminacion_mal_ambiente',
        'sugerencia_mejora',
        'ambiente_laboral',
        'equipo_de_trabajo',
        'cumplimiento_reglas',
        'falta_de_recursos',
        'otro',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $motivos = $this->input('motivos');
        if (is_array($motivos)) {
            $motivos = array_values(array_unique(array_filter(array_map(function ($val) {
                return is_string($val) ? strip_tags(trim($val)) : $val;
            }, $motivos))));
        } else {
            $motivos = [];
        }

        $motivoOtro = is_string($this->motivo_otro) ? strip_tags(trim($this->motivo_otro)) : null;
        if (!in_array('otro', $motivos, true)) {
            $motivoOtro = null;
        }

        $this->merge([
            'tipo' => is_string($this->tipo) ? strip_tags(trim($this->tipo)) : $this->tipo,
            'motivos' => $motivos,
            'motivo_otro' => $motivoOtro,
            'descripcion' => is_string($this->descripcion) ? strip_tags(trim($this->descripcion)) : $this->descripcion,
        ]);
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date'],
            'tipo' => ['required', 'string', Rule::in(['peticion', 'queja', 'reclamo', 'sugerencia', 'felicitacion', 'denuncia'])],
            'motivos' => ['nullable', 'array'],
            'motivos.*' => ['string', Rule::in(self::ALLOWED_MOTIVOS)],
            'motivo_otro' => [
                Rule::requiredIf(fn () => in_array('otro', (array) $this->input('motivos', []), true)),
                'nullable',
                'string',
                'max:255',
            ],
            'descripcion' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha debe tener un formato válido.',
            'tipo.required' => 'Debe seleccionar el tipo de solicitud.',
            'tipo.in' => 'El tipo de solicitud seleccionado no es válido.',
            'motivos.array' => 'Los motivos deben ser una lista válida.',
            'motivos.*.in' => 'Uno o más motivos seleccionados no son válidos.',
            'motivo_otro.required' => 'Debe especificar el motivo cuando selecciona la opción Otro.',
            'motivo_otro.max' => 'El motivo especificado no puede superar los 255 caracteres.',
            'descripcion.required' => 'La descripción es obligatoria.',
        ];
    }
}
