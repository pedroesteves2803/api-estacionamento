<?php

namespace App\Dtos\Employees;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use DateTime;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Date;
use Illuminate\Validation\Rule;

class OutputEmployeesDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly string $cpf,
        public readonly string $email,
        public readonly string $cargo,
        public readonly bool $ativo,
        public readonly int $estacionamento_id,
    )
    {
        $this->validate();
    }

    public function rules():array
    {
        return [];
    }

    public function messages():array{
        return [];
    }

    public function validator(): Validator{
        return validator($this->toArray(), $this->rules(), $this->messages());
    }

    public function validate():array{
        return $this->validator()->validate();
    }
}
