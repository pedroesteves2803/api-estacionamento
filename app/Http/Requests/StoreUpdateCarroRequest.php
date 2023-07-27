<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateCarroRequest extends FormRequest
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
                'unique:carros'
            ],

            'modelo' => [
                'required',
                'string',
            ],

            'cor' => [
                'required',
                'string'
            ],

            'estacionamento_id' => [
                'required',
                'int'
            ],
        ];

        if($this->method() === 'PATCH'){
            $rules['placa'] = [
                'nullable',
                'string',
                Rule::unique('carros')->ignore($this->id)
            ];

            $rules['modelo'] = [
                'nullable',
                'string',
            ];

            $rules['cor'] = [
                'nullable',
                'string'
            ];
        }

        return $rules;
    }
}
