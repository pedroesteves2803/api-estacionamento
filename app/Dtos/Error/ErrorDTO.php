<?php

namespace App\Dtos\Error;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;

class ErrorDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $message,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'message' => [
                'required',
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
