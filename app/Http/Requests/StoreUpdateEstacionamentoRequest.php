<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateEstacionamentoRequest extends FormRequest
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
            'nome' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'unique:estacionamentos'
            ],

            'quantidadeDeVagas' => [
                'required',
                'integer',
                'min:1',
            ],

            'ativo' => [
                'required',
                'boolean'
            ]
        ];

        if($this->method() === 'PATCH'){
            $rules['nome'] = [
                'nullable',
                'string',
                'min:3',
                'max:255',
                Rule::unique('estacionamentos')->ignore($this->id)
            ];

            $rules['quantidadeDeVagas'] = [
                'nullable',
                'integer',
                'min:1',
            ];

            $rules['ativo'] = [
                'nullable',
                'boolean'
            ];
        }

        return $rules;
    }
}
