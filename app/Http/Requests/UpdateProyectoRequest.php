<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProyectoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_del_proyecto'  => 'required|string|max:255',
            'objeto_contractual'   => 'nullable|string|max:255',
            'lineas_de_accion'     => 'nullable|string',
            'cobertura'            => 'nullable|string|max:255',
            'entidad_contratante'  => 'nullable|string|max:255',
            'fecha_de_ejecucion'   => 'nullable|date',
            'plazo'                => 'nullable|numeric|min:0',
            'valor_total'          => 'nullable|numeric|min:0',
            'estado'               => 'nullable|string|in:activo,inactivo,cerrado',
            'archivo_proyecto'     => 'nullable|file|max:20480',
            'archivo_contrato'     => 'nullable|file|max:20480',
            'evidencias'           => 'nullable|array',
            'evidencias.*'         => 'file|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_del_proyecto.required' => 'El nombre del proyecto es obligatorio.',
            'estado.in'                    => 'El estado debe ser activo, inactivo o cerrado.',
            'plazo.numeric'                => 'El plazo debe ser un número.',
            'valor_total.numeric'          => 'El valor total debe ser un número.',
        ];
    }
}
