<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $method = $this->method();
        $usuarioId = $this->route('usuario') ? $this->route('usuario')->id : null;

        $rules = [
            'nombre' => ['string', 'max:128'],
            'email'  => ['email'],
            'cedula' => [
                'string', 
                Rule::unique('usuario', 'cedula')->ignore($usuarioId)->whereNull('deleted_at'), 
                'regex:/^(V|E)[-]?\d{6,8}$/i'
            ],
            'rol_id'      => ['exists:rol,id'],
            'sucursal_id' => ['exists:sucursal,id'],
            // La contraseña es opcional (sometimes), pero si viene, debe ser válida
            'password'    => ['sometimes', 'string', Password::min(8)->mixedCase()->numbers()],
        ];

        if ($method === 'PUT') {
            // En PUT, todos los campos (excepto password) suelen ser requeridos
            // Filtramos password para que no se vuelva 'required' por error
            return array_map(function($key, $rule) {
                return ($key === 'password') ? array_merge(['sometimes'], $rule) : array_merge(['required'], $rule);
            }, array_keys($rules), $rules);
        } else {
            // En PATCH, todo es 'sometimes'
            return array_map(fn($rule) => array_merge(['sometimes', 'required'], $rule), $rules);
        }
    }

    public function messages(): array
    {
        return [
            'password.min' => 'Si vas a cambiar la contraseña, debe tener al menos 8 caracteres.',
            'cedula.unique' => 'Esta cédula ya pertenece a otro usuario.',
            // ... puedes mantener tus otros mensajes aquí
        ];
    }
}