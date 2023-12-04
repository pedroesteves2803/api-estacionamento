<?php

namespace App\Dtos\Vacancies;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;

/**
 * @OA\Schema(
 *     schema="ParkingDTO",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string", example="Estacionamento A"),
 *     @OA\Property(property="active", type="boolean", example=true),
 * )
 */
class VacancyUpdateDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly int $parking_id,
        public readonly int $number,
        public readonly bool $available
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'parking_id' => [
                'required',
                'int',
            ],

            'number' => [
                'required',
                'int',
                Rule::unique('vacancies'),
            ],

            'available' => [
                'required',
                'bool',
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
