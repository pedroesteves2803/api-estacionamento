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
        public readonly string $plate,
        public readonly string $model,
        public readonly string $color,
        public readonly string $input,
        public readonly int $parking_id,
        public readonly ?string $output = null,
        public readonly ?string $amountToBePaid = null,
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
