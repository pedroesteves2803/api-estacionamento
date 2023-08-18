<?php

namespace App\Dtos\Cars;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

class CarsDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly string $plate,
        public readonly string $model,
        public readonly string $color,
        public readonly int $parking_id,
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'plate' => [
                'required',
                'string',
                Rule::unique('cars')->ignore(request()->cars),
            ],

            'model' => [
                'required',
                'string',
            ],

            'color' => [
                'required',
                'string',
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
