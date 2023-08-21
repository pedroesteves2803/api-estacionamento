<?php

namespace App\Dtos\Cars;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;

class OutputCarsDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $plate = null,
        public readonly ?string $model = null,
        public readonly ?string $color = null,
        public readonly ?string $input = null,
        public readonly ?int $parking_id = null,
        public readonly ?string $output = null,
        public readonly ?string $amountToBePaid = null,
        public readonly ?bool $erro = false,
        public readonly ?string $message = null,
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
