<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateCarRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'placa' => [
                'required',
                'string',
                Rule::unique('carros'),
            ],

            'modelo' => [
                'required',
                'string',
            ],

            'cor' => [
                'required',
                'string',
            ],

            'estacionamento_id' => [
                'required',
                'int',
            ],
        ];

        if ('PATCH' === $this->method()) {
            $rules['placa'] = [
                'required',
                'string',
                Rule::unique('carros')->ignore($this->carro),
            ];

            $rules['modelo'] = [
                'nullable',
                'string',
            ];

            $rules['cor'] = [
                'nullable',
                'string',
            ];
        }

        return $rules;
    }
}
