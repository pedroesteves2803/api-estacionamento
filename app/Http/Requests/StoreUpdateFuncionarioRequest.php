<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUpdateFuncionarioRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nome' => [
                'required',
                'string',
            ],

            'cpf' => [
                'cpf',
                'required',
                'string',
                'unique:funcionarios'
            ],

            'email' => [
                'required',
                'email',
                'unique:funcionarios'
            ],

            'cargo' => [
                'required',
                'string'
            ],

            'ativo' => [
                'required',
                'boolean',
            ],

            'estacionamento_id' => [
                'required',
                'int'
            ],
        ];

        if($this->method() === 'PATCH'){
            $rules['nome'] = [
                'nullable',
                'string',
            ];

            $rules['cpf'] = [
                'cpf',
                'nullable',
                'string',
                Rule::unique('funcionarios')->ignore($this->funcionario)
            ];

            $rules['email'] = [
                'nullable',
                'email',
                Rule::unique('funcionarios')->ignore($this->funcionario)
            ];

            $rules['ativo'] = [
                'nullable',
                'boolean',
            ];
        }

        return $rules;
    }
}
