<?php

namespace App\Dtos\Cars;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use App\Models\Carro;
use App\Models\Funcionario;
use Carbon\Carbon;
use DateTime;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class OutputCarsDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $placa,
        public readonly string $modelo,
        public readonly string $cor,
        public readonly string $entrada,
        public readonly ?string $saida = null,
        public readonly ?string $valor_para_ser_pago = null,
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
