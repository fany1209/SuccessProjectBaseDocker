<?php

namespace App\Http\Requests\Requisition;

use Illuminate\Foundation\Http\FormRequest;

class RequisitionUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $sanitized = [];
        foreach ($this->all() as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = strip_tags(trim($value));
            } elseif (is_array($value)) {
                $sanitized[$key] = array_map(function ($item) {
                    return is_string($item) ? strip_tags(trim($item)) : $item;
                }, $value);
            }
        }
        if (isset($sanitized['consecutive']) && trim((string)$sanitized['consecutive']) === '') {
            $sanitized['consecutive'] = null;
        }
        $this->merge($sanitized);
    }

    public function rules(): array
    {
        $reqId = $this->input('req_id') ?? $this->route('requisition');
        if (is_object($reqId)) {
            $reqId = $reqId->id;
        }

        return [
            'req_id'         => 'nullable|integer|exists:purchases_requisitions,id',
            'applicant'      => 'required|string|max:150',
            'department'     => 'required|string|max:70',
            'data_sheet'     => 'required|boolean',
            'safety_sheet'   => 'required|boolean',
            'comparative_id' => 'nullable|integer',
            'consecutive'    => 'nullable|string|max:30|unique:purchases_requisitions,consecutive,' . $reqId,
            'id'             => 'nullable|array',
            'description'    => 'nullable|array',
            'description.*'  => 'nullable|string|max:1000',
            'supplier'       => 'nullable|array',
            'supplier.*'     => 'nullable|string|max:100',
            'url'            => 'nullable|array',
            'url.*'          => 'nullable|string|max:1024',
            'use'            => 'nullable|array',
            'use.*'          => 'nullable|string|max:100',
            'quantity'       => 'nullable|array',
            'quantity.*'     => 'nullable|numeric',
            'unit'           => 'nullable|array',
            'unit.*'         => 'nullable|string|max:10',
            'image_url'      => 'nullable|array',
            'image_url.*'    => 'nullable|string|max:2048',
            'insumo'         => 'nullable|array',
            'insumo.*'       => 'nullable|string|in:directo,indirecto',
        ];
    }

    public function messages(): array
    {
        return [
            'applicant.required'    => 'El solicitante es obligatorio.',
            'department.required'   => 'El departamento es obligatorio.',
            'data_sheet.required'   => 'Debe indicar si cuenta con ficha técnica.',
            'safety_sheet.required' => 'Debe indicar si cuenta con hoja de seguridad.',
            'consecutive.unique'    => 'El número consecutivo ya ha sido registrado.',
        ];
    }
}
