<?php

namespace App\Dtos\Parking;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class ParkingDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $name,
        public readonly int $numberOfVacancies,
        public readonly bool $active,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('parkings')->ignore(request()->parking),
            ],

            'numberOfVacancies' => [
                'required',
                'integer',
                'min:1',
            ],

            'active' => [
                'required',
                'boolean',
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
