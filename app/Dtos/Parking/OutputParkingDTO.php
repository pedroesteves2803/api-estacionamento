<?php

namespace App\Dtos\Parking;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use DateTime;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;

class OutputParkingDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly int $numero_de_vagas,
        public readonly bool $ativo,
        public readonly DateTime $criado,
        public readonly Collection $carros,
        public readonly Collection $funcionarios
    )
    {
        $this->validate();
    }

    public function rules():array{
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
