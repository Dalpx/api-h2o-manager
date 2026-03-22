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

        $baseRules = [
            'nombre' =>    ['string', 'max:128'],
            'email'  =>    ['email'],
            'cedula' =>    ['string', 'unique:usuario,email,' . $usuarioId, 'regex:/^(V|E)[-]?\d{6,8}$/i'],
            'rol_id' =>    ['exists:rol,id'],
            'sucursal_id' => ['exists:sucursal,id'],
        ];


        if ($method == 'PUT') {
            return array_map(fn($rule) => array_merge(['required'], $rule), $baseRules);
        } else {
            return array_map(fn($rule) => array_merge(['sometimes', 'required'], $rule), $baseRules);
        }
    }

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'activo' => true
        ]);
    }
}
