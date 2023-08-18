<?php

namespace App\Dtos\Employees;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;

class OutputEmployeesDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $cpf,
        public readonly string $email,
        public readonly string $office,
        public readonly bool $active,
        public readonly int $parking_id,
        public readonly ?string $error = null,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [];
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
