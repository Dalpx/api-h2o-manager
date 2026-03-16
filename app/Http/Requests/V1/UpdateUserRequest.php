<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $method = $this->method();
        $usuarioId = $this->route('usuario') ? $this->route('usuario')->id : null;

        if ($method == 'PUT') {
            return [
                'nombre' =>    ['required', 'string', 'max:128'],
                'email'  =>    ['required', 'email'],
                'cedula' =>    ['required', 'string', 'unique:usuario,email,' . $usuarioId, 'regex:/^(V|E)[-]?\d{6,8}$/i'],
                'rol_id' =>    ['required', 'exists:rol,id'],
                'sucursal_id'=>['required', 'exists:sucursal,id' ],
            ];
        } else {
            return [
                'nombre' =>    ['sometimes', 'required', 'string', 'max:128'],
                'email'  =>    ['sometimes', 'required', 'email'],
                'cedula' =>    ['sometimes', 'required', 'string', 'unique:usuario,email,' . $usuarioId, 'regex:/^(V|E)[-]?\d{6,8}$/i'],
                'rol_id' =>    ['sometimes', 'required', 'exists:rol,id'],
                'sucursal_id'=>['sometimes', 'required', 'exists:sucursal,id'],
            ];
        }
    }

    public function prepareForValidation()
    {
       $this->mergeIfMissing([
            'activo' => true
        ]);
    }
}
