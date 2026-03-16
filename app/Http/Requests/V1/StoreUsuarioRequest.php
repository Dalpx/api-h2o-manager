<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $usuarioId = $this->route('usuario') ? $this->route('usuario')->id : null;
        return [
            'nombre' => ['required', 'string', 'max:128'],
            'email'  => ['required', 'email'],
            'cedula' => [
                'required',
                'string',
                Rule::unique('usuario', 'cedula')->ignore($usuarioId)->whereNull('deleted_at'),
                'regex:/^(V|E)[-]?\d{6,8}$/i'
            ],
            'rol_id'    => ['required', 'exists:rol,id'],
            'sucursal_id' => ['required', 'exists:sucursal,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del usuario es obligatorio.',
            'email.email' => 'Debes ingresar un formato de correo válido.',
            'email.required'=> 'El campo email es requerido',
            'cedula.unique' => 'Esta cédula ya está registrada en nuestro sistema.',
            'cedula.regex' => 'La cédula debe empezar con V o E seguida de 6 a 8 números.',
            'cedula.required'=> 'El campo cedula es requerido',
            'sucursal_id.exists' => 'La sucursal seleccionada no es válida o no existe.',
            'sucursal_id.required' => 'El campo sucursal_id es requerido',
            'rol_id.in' => 'El rol seleccionado no está dentro de las opciones permitidas.',
            'rol_id.required'=> 'El campo rol_id es requerido',
        ];
    }

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'activo' => true
        ]);
    }
}
