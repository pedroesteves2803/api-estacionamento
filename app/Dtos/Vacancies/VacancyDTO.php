<?php

namespace App\Dtos\Vacancies;

use App\Dtos\AbstractDTO;
use App\Dtos\InterfaceDTO;
use Illuminate\Contracts\Validation\Validator;

/**
 * @OA\Schema(
 *     schema="ParkingDTO",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string", example="Estacionamento A"),
 *     @OA\Property(property="active", type="boolean", example=true),
 * )
 */
class VacancyDTO extends AbstractDTO implements InterfaceDTO
{
    public function __construct(
        public readonly int $number_of_vacancies,
        public readonly int $parking_id
    ) {
        $this->validate();
    }

    public function rules(): array
    {
        return [
            'number_of_vacancies' => [
                'required',
                'int',
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
