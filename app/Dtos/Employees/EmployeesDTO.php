<?php

namespace App\Dtos\Employees;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class EmployeesDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $cpf,
        public readonly string $email,
        public readonly string $office,
        public readonly bool $active,
        public readonly int $parking_id,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('employees')->ignore(request()->employees),
            ],

            'cpf' => [
                'required',
                'string',
                Rule::unique('employees')->ignore(request()->employees),
            ],

            'email' => [
                'required',
                'string',
            ],

            'office' => [
                'required',
                'string',
            ],

            'active' => [
                'required',
                'boolean',
            ],

            'parking_id' => [
                'required',
                'int',
            ],
        ];
    }

    public function messages(): array
    {
        return [];
    }

    public function validator(): Validator
    {
        return validator($this->toArray(), $this->rules(), $this->messages());
    }

    public function validate(): array
    {
        return $this->validator()->validate();
    }
}
