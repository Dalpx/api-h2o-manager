<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password; // Importamos las reglas de seguridad de Laravel

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuarioId = $this->route('usuario') ? $this->route('usuario')->id : null;
        
        return [
            'nombre'   => ['required', 'string', 'max:128'],
            'email'    => ['required', 'email'],
            'password' => [
                'required', 
                'string', 
                Password::min(8)->mixedCase()->numbers() // Regla recomendada: min 8 caracteres, letras y números
            ],
            'cedula'   => [
                'required',
                'string',
                Rule::unique('usuario', 'cedula')->ignore($usuarioId)->whereNull('deleted_at'),
                'regex:/^(V|E)[-]?\d{6,8}$/i'
            ],
            'rol_id'      => ['required', 'exists:rol,id'],
            'sucursal_id' => ['required', 'exists:sucursal,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'      => 'El nombre del usuario es obligatorio.',
            'email.email'          => 'Debes ingresar un formato de correo válido.',
            'email.required'       => 'El campo email es requerido.',
            'password.required'    => 'La contraseña es obligatoria para crear un nuevo usuario.',
            'password.min'         => 'La contraseña debe tener al menos 8 caracteres.',
            'cedula.unique'        => 'Esta cédula ya está registrada en nuestro sistema.',
            'cedula.regex'         => 'La cédula debe empezar con V o E seguida de 6 a 8 números.',
            'cedula.required'      => 'El campo cédula es requerido.',
            'sucursal_id.exists'   => 'La sucursal seleccionada no es válida o no existe.',
            'sucursal_id.required' => 'El campo sucursal_id es requerido.',
            'rol_id.required'      => 'El campo rol_id es requerido.',
        ];
    }

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'activo' => true
        ]);
    }
}