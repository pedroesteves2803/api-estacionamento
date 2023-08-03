<?php

namespace App\Dtos\Parking;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use App\Models\Carro;
use App\Models\Funcionario;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class OutputParkingDTO extends AbstractDTO implements InterfaceDTO
{
    public readonly DateTime $criado;

    public function __construct(
        public readonly int $id,
        public readonly string $nome,
        public readonly int $numero_de_vagas,
        public readonly bool $ativo,
        DateTime $criado,
        // public readonly Carro $carros,
        // public readonly Funcionario $funcionarios
    )
    {
        $this->criado = Carbon::make($this->criado)->format('d-m-Y');
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
